=== IP Loc8 ===
Contributors: studioreforma
Donate link: http://talkingaboutthis.eu/
Tags: ip geolocation, geolocation, qtranslate
Requires at least: 4.0.0
Tested up to: 4.3.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin finds the country and city of the user by the IP address and saves the info in a cookie and a global php variable. Furthermore, for sites using qTranslate X for multilanguage, it can set the language of the site upon the user’s first visit, using country-based rules that you set up in the plugin options.

== Description ==

This plugin would figure out a user location (country and city, but have in mind that the city information is only 60% reliable) by matching his IP address to an IP addresses database. The information would then be stored in a cookie and in a global variable, to be used by other plugins or themes. This happens quietly in the background without the user seeing anything. 

Furthermore, for sites using qTranslate X for multilanguage, it can set the language of the site upon the user’s first visit, using country-based rules that you set up in the plugin options. Normally qTranslate X offers automatic setting of language based on the browser language, but in many countries browser localisation is not as popular and a lot of people use english language browsers, so this method is not reliable and some people would prefer to set the language based on the user’s country.

This plugin does not do much on its own (except for the integration with qTranslate) - it provides valuable data to be integrated with other code on your website. The idea behind it is to have a fast, simple and reliable tool for getting location, which could be used on a per-need basis. The plugin loads no scripts and no stylesheets and uses class autoloading to load its code only when it's acutally needed.

Future verions will also provide a tool for precise geolocation, using the user’s GPS and WiFi data (html5 geolocation).

= Features =

* Automatically gets country and city by IP address and saves it in a cookie
* Possible qTranslate X language setting based on country, upon first visit
* Ability to get precise geolocation information by asking for user permissions and using his GPS and WiFi data

== Installation ==

This plugin needs cURL PHP extension! PHP sessions should also work properly on your server, to avoid querying the IPDB on every page load by users with cookies disabled.

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= How long does the cookie remain in the browser? =

The cookie is kept for 2 weeks only. People with smartphones and tablets often move, so we do not want to have data that is very old.

= What if cookies are disabled? =

The information is also stored in a global variable $visitorGeolocation. The plugin also uses the user session to store the information, so that the IP database is queried only upon the first page load. 

= What if I want to do other things upon first page load? =

If you want to do some additional things upon first page load (set currency in your shop, for example), there are two ways to do that:
1. Use the 'iploc8_set_country' action, which is only run upon first page load (country 2 letter code is passed as a variable).
2. Check if the 'IPLOC8NEW' constant is defined. It is defined only at the first page load and its value is the user’s country 2-letter code. 

== Screenshots ==


== Changelog ==

= 1.0 =
* First release


== Upgrade Notice ==

= 1.0 =
First release

