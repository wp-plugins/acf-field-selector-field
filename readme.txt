=== Advanced Custom Fields: Field Selector Field ===
Contributors: danielpataki
Tags: acf, custom fields
Requires at least: 3.4
Tested up to: 4.0
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

This ACF field type is compatible with:
* ACF 5
* ACF 4

== Installation ==

1. Copy the `acf-field_selector_field` folder into your `wp-content/plugins` folder
2. Activate the Field Selector plugin via the plugins admin page
3. Create a new field via ACF and select the Field Selector type
4. Please refer to the description for more info regarding the field type settings

== Changelog ==

= 1.0 =
* Initial Release.

= 2.0 =
* Added ACF 5 Support
* Removed ACF 3 Support
