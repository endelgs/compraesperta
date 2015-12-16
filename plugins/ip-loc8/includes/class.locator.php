<?php 
/* IP-LOC8
/*
/* CLASS.LOCATE.PHP
/* Gets the location based on IP. */

// if giving bad results use http://api.geoiplookup.net/?query=5.53.169.1 

// former ip2location_lite
final class IpLocLocator {
	protected $errors = array();
	protected $settings = array();
	protected $redirects = array();
	protected $service = 'api.ipinfodb.com';
	protected $version = 'v3';
	protected $apiKey = '';

    /**
     * Construct
     */
	public function __construct() {
		$this->settings = get_option( 'iploc8','' ); 
		if (!isset($this->settings['key'])) { $this->settings['key'] = ''; }
		$this->settings['geo'] = ( !empty($this->settings['geo']) ? 1 : 0 );
		$this->settings['precision'] = ( !empty($this->settings['precision']) ? 1 : 0 );
		$this->setKey($this->settings['key']);
	}
    /**
     * Set API key
     */
	public function setKey($key){
		if(!empty($key)) $this->apiKey = $key; 
	}
    /**
     * Create a COOKIE and fill with data
     */	
	public function setData(){
		$data = array();
		$visitorGeolocation = $this->getResult($_SERVER['REMOTE_ADDR']);
		if ($visitorGeolocation['statusCode'] == 'OK') {
			if ($this->settings['precision']=='1') {
				$data = array(
					'code'		=> $visitorGeolocation['countryCode'],
					'country'	=> $visitorGeolocation['countryName'],
					'region'	=> $visitorGeolocation['regionName'],
					'city'		=> $visitorGeolocation['cityName'],
					'zip'		=> $visitorGeolocation['zipCode'],
					'lat'		=> $visitorGeolocation['latitude'],
					'lon'		=> $visitorGeolocation['longitude'],
					'timezone'	=> $visitorGeolocation['timeZone'],
				); 
			} else {
				$data = array(
					'code'		=> $visitorGeolocation['countryCode'],
					'country'	=> $visitorGeolocation['countryName'],
				);
			}
			$this->code = $data['code'];
			$cookie = base64_encode(serialize($visitorGeolocation));
		   	setcookie("iploc8", $cookie, time()+3600*24*14, '/'); //set cookie for a maximum of 2 weeks - people with smartphones move!
		}
		return $data;
	}
	
	public function redirect(){
		if (!empty($this->code)) {
			add_filter( 'qtranslate_detect_language', array($this,'set_qtranslate_language'), 10, 1);
			do_action( 'iploc8_set_country', $this->code ); // action for other plugins to use, only run upon user first visit... 
			define('IPLOC8NEW',$this->code); // GLOBAL with user country, only set upon user's first visit for use in themes...
		}
	}
	
	public function set_qtranslate_language($url_info) { 
		$this->redirects = get_option( 'iploc8_redir','' );
		if (!empty($this->redirects)) { 
			if (isset($this->redirects[$this->code])) {
				$url_info['language'] = esc_attr($this->redirects[$this->code]);
			} elseif (isset($this->redirects['00'])) {
				$url_info['language'] = esc_attr($this->redirects['00']);
			} 
		}
		return $url_info;				
	}
	
	public function getError(){
		return implode("\n", $this->errors);
	}

	public function getCountry($host){
		return $this->getResult($host, 'ip-country');
	}

	public function getCity($host){
		return $this->getResult($host, 'ip-city');
	}

	private function getResult($host, $name='ip-country'){
		if ($this->settings['precision']=='1') {
			$name = 'ip-city';
		}
		
		// $ip = @gethostbyname($host); - we will not need to check hostnames.. 
		$ip = $host;
		// if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
		if(filter_var($ip, FILTER_VALIDATE_IP)){
			//$xml = @file_get_contents('http://' . $this->service . '/' . $this->version . '/' . $name . '/?key=' . $this->apiKey . '&ip=' . $ip . '&format=xml'); 
			// CURL USE!! MANY FAST!!
			$xml = $this->curl_get_contents('http://' . $this->service . '/' . $this->version . '/' . $name . '/?key=' . $this->apiKey . '&ip=' . $ip . '&format=xml');
		    

			if (get_magic_quotes_runtime()){
				$xml = stripslashes($xml);
			}

			try{
				$response = @new SimpleXMLElement($xml);

				foreach($response as $field=>$value){
					$result[(string)$field] = (string)$value;
				}

				return $result;
			}
			catch(Exception $e){
				$this->errors[] = $e->getMessage();
				return;
			}
		}

		$this->errors[] = '"' . $host . '" is not a valid IP address or hostname.';
		return;
	}
	private function curl_get_contents($url) {
		$ch = curl_init();

	    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
	
	    $data = curl_exec($ch);
	    curl_close($ch);
	
	    return $data;
	}
}
