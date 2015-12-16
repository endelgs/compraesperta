<?php 
/* IP-LOC8
/*
/* CLASS.ADMIN.PHP
/* The option options page. */


class IpLocAdmin {
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options = array();

    /**
     * Construct
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page() {
        // add_options_page adds a sub-page to the "Settings" menu
        add_options_page(
            'IP Location', 
            'IP Location', 
            'manage_options', 
            'iploc8_settings', // page slug
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Pre load options
        $this->options['iploc8'] = get_option( 'iploc8' );
        $this->options['iploc8_redir'] = get_option( 'iploc8_redir' );
        ?>
        <div class="wrap">
            <h2>IP Location Settings</h2>           
            <form method="post" action="options.php">
            <?php
                settings_fields( 'iploc8_options_group' );  // Option group  
                do_settings_sections( 'iploc8_settings' ); // page slug
                submit_button(); 
            ?>
            </form>
            <script>
            	jQuery(document).ready(function() { 
					jQuery('.redir_add_row').click(function(event) { 
						jQuery('.redir_table').append('<tr class="redir_row"><td><input type="text" name="iploc8_redir_country[]" value="" /></td><td><input type="text" name="iploc8_redir_lang[]" value="" size="2" /></td></tr>'); 
					}); 
					jQuery('.redir_del').click(function(event) {
						jQuery(this).closest('tr').remove();
					}); 
				}); 
			</script>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init() {    
    	// register options for wp_options   
        register_setting(
            'iploc8_options_group', // Option group
            'iploc8', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );
        register_setting(
            'iploc8_options_group', // Option group
            'iploc8_redir', // Option name
            array( $this, 'validate_redir' ) // Sanitize
        );
		// register sections per page
        add_settings_section(
            'iploc8_section_1', // ID
            'Basic Setup', // Title
            array( $this, 'empty_section_info' ), // Callback
            'iploc8_settings' // Page
        );  
        add_settings_section(
            'iploc8_section_2', // ID
            'Redirects', // Title
            array( $this, 'empty_section_info' ), // Callback
            'iploc8_settings' // Page
        );
		// register settings fields per section and page
        add_settings_field(
            'iploc8_key', // ID
            'IP Database Key', // Title 
            array( $this, 'input_text' ), // Callback
            'iploc8_settings', // Page
            'iploc8_section_1', // Section           
            array('Your key from api.ipinfodb.com','iploc8','key')
        );      
        add_settings_field(
            'iploc8_city', 
            'City level', 
            array( $this, 'input_checkbox' ), 
            'iploc8_settings', 
            'iploc8_section_1',
            array('If checked, plugin would also try to get the user\'s city (60% accuracy), besides country (99% accuracy).','iploc8','precision')
        ); 
        add_settings_field(
            'iploc8_geo', 
            'Use GPS data', 
            array( $this, 'input_checkbox' ), 
            'iploc8_settings', 
            'iploc8_section_1',
            array('Soon!','iploc8','geo')
        );  
        add_settings_field(
            'iploc8_redir', // ID
            'Redirect users', // Title 
            array( $this, 'input_redir' ), // Callback
            'iploc8_settings', // Page
            'iploc8_section_2', // Section           
            array('Redirect users based on their country. Use two-letter country and language codes.','iploc8_redir','')
        );    
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) { 
        $new_input = array();
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = absint( $input['id_number'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        // return $new_input;
        return $input;
    }

    /** 
     * Print the Section text
     */
    public function empty_section_info() {
        echo '';
    }

    /** 
     * Prints input type=text options
     */
    public function input_text($args) {
    	$option = $this->extract_option_data($args);
		// Render the output
		echo '<input type="text" id="'. $option['id'] .'" name="'. $option['name'] .'" value="'.stripslashes(esc_attr( $option['value'] )).'" />';
		echo '<br /><small><label for="'.$option['id'].'">'. $args[0] .'</label></small>'; 
    }

    /** 
     * Prints checkbox options
     */
    public function input_checkbox($args) {
    	$option = $this->extract_option_data($args);
		// Render the output
		echo '<label for="'. $option['id'] .'"><input type="checkbox" id="'. $option['id'] .'" name="'. $option['name'] .'" size="20" value="1" '.checked($option['value'],'1',false).' />'. $args[0] . '</label>';
    }
    
     /** 
     * Prints custom option REDIRECTS
     */
    public function input_redir($args) {
		// Render the output
		echo 'Default language: <input type="text" id="iploc8_redir_default" name="iploc8_redir[default]" value="'.(isset( $this->options['iploc8_redir']['00'] ) ? $this->options['iploc8_redir']['00'] : '').'" size="2" /><br />';
		echo '<table class="redir_table"><tr><th>Country</th><th>Language</th>';
		if ($this->options['iploc8_redir']) {
		foreach ($this->options['iploc8_redir'] as $country=>$lang) {
			if ($country=='00') { continue; }
			echo '<tr class="redir_row">';
				echo '<td><input type="text" name="iploc8_redir[country][]" value="'.$country.'" /></td>';
				echo '<td><input type="text" name="iploc8_redir[lang][]" value="'.$lang.'" size="2" /><a href="#" class="redir_del">X</a></td>';
			echo '</tr>';
		}
		}
		echo '<tr class="redir_row">';
		echo '<td><input type="text" name="iploc8_redir[country][]" value="" /></td>';
		echo '<td><input type="text" name="iploc8_redir[lang][]" value="" size="2" /></td>';
		echo '</tr>';
		echo '</table>';
		echo '<br /><a href="#" class="redir_add_row">Add more rows</a>';
		echo '<br /><small><label for="iploc8_redir">'. $args[0] .'</label></small>'; 
		
    }
    /** 
     * Prepares an $option array with name, id and value for this option
     */
    public function extract_option_data($args) {
		$option = array();
		if ( !empty($args[2]) ) { 
			$option['name'] = $args[1] . '[' . $args[2] . ']'; 
			$option['id'] = $args[1].'-'.$args[2]; 
			$settings = get_option($args[1],'');
			$option['value'] = ( isset($this->options[$args[1]][$args[2]]) ? $this->options[$args[1]][$args[2]] : '');
		} else { 
			$option['name'] = $args[1]; 
			$option['id'] = $args[1];
			$option['value'] = ( isset( $this->options[$args[1]] ) ? $this->options[$args[1]] : '');
		}
		return $option;
	}
	
	public function validate_redir( $input ) {
		$newinput = array();
		if (!empty($input['default'])) {
			$newinput['00'] = $input['default'];
		}
		if (!empty($input['country'])) {
			foreach ($input['country'] as $key => $country) {
				if (!empty($input['lang'][$key])) {
					$newinput[$country] = $input['lang'][$key];
				}
			}
		}
		return $newinput;
	}
	
	public function validate_options( $input ) {
		// Create our array for storing the validated options
		$output = array();
		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if( isset( $input[$key] ) ) {
				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
			} // end if
		} // end foreach
		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'kosher_validate_options', $output, $input );
	}

}