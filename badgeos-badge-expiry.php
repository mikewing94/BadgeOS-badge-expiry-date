<?php
/**
 * @package badgeos-badge-expiry
 * @version 1.0
 */
/*
Plugin Name: Mikes BadgeOS badge expiry
Plugin URI: http://wordpress.org/plugins/badgeos-badge-expiry/
Description: Code to expire badgeos badge after set validity period.
Author: Mike
Version: 1.0
Author URI: http://michaelwing.co.uk/
*/

//error_reporting(E_ALL & ~E_DEPRECATED);


define('BADGEOS_BADGE_EXPIRY_PATH', plugin_dir_path( __FILE__ ));
define('BADGEOS_BADGE_EXPIRY_URL', plugins_url().'/'.basename(dirname(__FILE__)).'/');

register_activation_hook( __FILE__, array( 'Badgeos_Badge_Expiry_Settings', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Badgeos_Badge_Expiry_Settings', 'deactivate' ) );

require_once BADGEOS_BADGE_EXPIRY_PATH.'/includes/badgeos-badge-expiry-settings.php';
if (class_exists('Badgeos_Badge_Expiry_Settings')) {
  $Badgeos_Badge_Expiry_Settings  = new Badgeos_Badge_Expiry_Settings;
}

