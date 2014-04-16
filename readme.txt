=== Advanced Custom Fields: Field Selector Field ===
Contributors: danielpataki
Tags: acf, custom fields
Requires at least: 3.4
Tested up to: 3.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to create a field selector field for ACF. This allows the selection of one or more custom fields from the defined fields.

== Description ==

The filed selector field allows the selection of other custom fields. This is useful if you want to build a form from the custom fields a user has selected for example. When creating the field you have the following options:

* Display Type (autocomplete, checkbox, select, multiselect, radio)
* Maximum Selectable Items
* Allowed Field Types
* Excluded Field Types
* Allowed Field Groups

= Compatibility =

This add-on will work with:

* version 4 and up
* version 3 and bellow

== Installation ==

This add-on can be treated as both a WP plugin and a theme include.

= Plugin =
1. Copy the 'acf-field_selector' folder into your plugins folder
2. Activate the plugin via the Plugins admin page

= Include =
1.	Copy the 'acf-field_selector' folder into your theme folder (can use sub folders). You can place the folder anywhere inside the 'wp-content' directory
2.	Edit your functions.php file and add the code below (Make sure the path is correct to include the acf-field_selector.php file)

`
add_action('acf/register_fields', 'my_register_fields');

function my_register_fields()
{
	include_once('acf-field_selector/acf-field_selector.php');
}
`

== Changelog ==

= 1.0 =
* Initial Release.
