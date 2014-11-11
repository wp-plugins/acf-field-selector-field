<?php

/*
Plugin Name: Advanced Custom Fields: Field Selector
Plugin URI: https://github.com/danielpataki/acf-field_selector_field
Description: A field which allows you to select other custom fields
Version: 2.0
Author: Daniel Pataki
Author URI: http://danielpataki.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/



// Load Text Domain
load_plugin_textdomain( 'acf-field_selector', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );

/**
 * Include Field Type For ACF5
 */
function include_field_types_field_selector( $version ) {
	include_once('acf-field_selector-v5.php');
}
// Action To Include Field Type For ACF5
add_action('acf/include_field_types', 'include_field_types_field_selector');

/**
 * Include Field Type For ACF4
 */
function register_fields_field_selector() {
	include_once('acf-field_selector-v4.php');
}
// Action To Include Field Type For ACF4
add_action('acf/register_fields', 'register_fields_field_selector');



?>
