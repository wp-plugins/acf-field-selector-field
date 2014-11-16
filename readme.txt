=== Advanced Custom Fields: Field Selector Field ===
Contributors: danielpataki
Tags: acf, fields, google
Requires at least: 3.5
Tested up to: 4.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A field for Advanced Custom Fields which allows users to select a list of created custom fields

== Description ==

The filed selector field allows the selection of other custom fields. This is useful if you want to build a form from the custom fields a user has selected for example. When creating the field you have the following options:

* Include/Exclude Field Types
* Include/Exclude Field Groups


This ACF field type is compatible with:

* ACF 5
* ACF 4

== Installation ==

= Installation =

1. Copy the `acf-field_selector_field` folder into your `wp-content/plugins` folder
2. Activate the Advanced Custom Fields: Field Selector plugin via the plugins admin page
3. Create a new field via ACF and select the Field Selector type
4. Please refer to the description for more info regarding the field type settings

= Usage =

For developers I've included a filter which allows you to further filter selected fields. At the moment this filter is used to make sure that included and excluded types and groups are reflected in the selectable list.

`add_filter( 'acffsf/item_filters', 'selectable_item_filter', 10, 2 )`

The first parameter is the list of items to modify, the second is the setting field.

== Screenshots ==

1. ACF control for field creation
2. The user-facing field selector

== Changelog ==

= 3.0.1 =
* Made sure search is case-insensitive
* Made sure value was json decoded when returned to the_field

= 3.0 =
* Complete rewrite with custom controls

= 2.0 =
* Added ACF 5 Support
* Removed ACF 3 Support

= 1.0 =
* Initial Release.
