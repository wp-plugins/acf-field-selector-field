<?php

/*
Plugin Name: Advanced Custom Fields: Field Selector
Plugin URI: https://github.com/danielpataki/acf-field_selector_field
Description: This plugin will let you create an input field for selecting one or more custom fields.
Version: 3.0.2
Author: Daniel Pataki
Author URI: http://danielpataki.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/




// 1. set text domain
// Reference: https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
load_plugin_textdomain( 'acf-field_selector', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );


include_once('acf-field_selector-common.php');


// 2. Include field type for ACF5
// $version = 5 and can be ignored until ACF6 exists
function include_field_types_field_selector( $version ) {

	include_once('acf-field_selector-v5.php');

}

add_action('acf/include_field_types', 'include_field_types_field_selector');




// 3. Include field type for ACF4
function register_fields_field_selector() {

	include_once('acf-field_selector-v4.php');

}

add_action('acf/register_fields', 'register_fields_field_selector');




?>
