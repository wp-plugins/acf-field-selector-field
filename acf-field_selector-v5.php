<?php

class acf_field_field_selector extends acf_field {


	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function __construct() {

		$this->name = 'field_selector';
		$this->label = __('Field Selector', 'acf-field_selector');
		$this->category = __("Choice",'acf');
		$this->defaults = array(
			'group_filtering' => 'include',
			'groups' => '',
			'type_filtering'  => 'include',
			'types'  => ''
		);
		$this->common = new acf_field_field_selector_common();

    	parent::__construct();

		add_filter( 'acffsf/item_filters', array( 'acf_field_field_selector_common', 'type_filter' ), 10, 2 );
		add_filter( 'acffsf/item_filters', array( 'acf_field_field_selector_common', 'group_filter' ), 10, 2 );

	}


	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field_settings( $field ) {

		$field = array_merge($this->defaults, $field);
		if( !empty( $field['types'] ) ) {
			$field['types'] = implode(',', $field['types']);
		}
		else {
			$field['types'] = '';
		}

		if( !empty( $field['groups'] ) ) {
			$field['groups'] = implode(',', $field['groups']);
		}
		else {
			$field['groups'] = '';
		}


		acf_render_field_setting( $field, array(
			'label'			=> __('Group Filtering','acf-field_selector'),
			'instructions'	=> __('Set how the given groups are used','acf-field_selector'),
			'type'			=> 'radio',
			'name'			=> 'group_filtering',
			'layout'	=>	'horizontal',
			'choices'	=>	array(
				'include' => __('Include'),
				'exclude' => __('Exclude'),
			),
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Groups','acf-field_selector'),
			'instructions'	=> __('Set the ID of groups to include or exclude','acf-field_selector'),
			'type'			=> 'text',
			'name'			=> 'groups',
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Type Filtering','acf-field_selector'),
			'instructions'	=> __('Set how the given types are used','acf-field_selector'),
			'type'			=> 'radio',
			'name'			=> 'type_filtering',
			'layout'	=>	'horizontal',
			'choices'	=>	array(
				'include' => __('Include'),
				'exclude' => __('Exclude'),
			),
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Types','acf-field_selector'),
			'instructions'	=> __('Set the types to include or exclude','acf-field_selector'),
			'type'			=> 'text',
			'name'			=> 'types',
		));


	}



	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field( $field ) {


		?>
		<div class='multiselector'>
			<div class='selectable-container field-container'>
				<div class='title'><?php _e( 'Available Fields', 'acf-field_selector' ) ?></div>
				<div class='search'><input type='text' id="field-search" placeholder='Type to search...'></div>
				<div class='selectable field'>
					<?php
					$selectable = $this->get_items( $field, $this->get_selectable_item_fields( $field ) );
					$this->common->show_items( $selectable );
					?>
				</div>
			</div>
			<div class='selected-container field-container'>
				<div class='title'><?php _e( 'Selected Fields', 'acf-field_selector' ) ?></div>
				<div class='message'><?php _e( 'Drag and drop to re-order your selection', 'acf-field_selector' ) ?></div>
				<div class='selected field'>
					<?php
						$selected = $this->get_items( $field, $this->get_selected_item_fields( $field ), false );
						$this->common->show_items( $selected );
					?>
				</div>
			</div>
			<input type='hidden' id='field-value' name='<?php echo esc_attr($field['name']) ?>' value="<?php echo esc_attr($field['value']) ?>">
		</div>
		<?php


	}


	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	function input_admin_enqueue_scripts() {

		$dir = plugin_dir_url( __FILE__ );



		// register ACF scripts
		wp_register_script( 'acf-input-field_selector', $dir . 'js/input.js', array('acf-input'), $this->settings['version'] );
		wp_register_style( 'acf-input-field_selector', $dir . 'css/input.css', array('acf-input'), $this->settings['version'] );

		wp_enqueue_script( 'jquery-ui-sortable' );


		// scripts
		wp_enqueue_script(array(
			'acf-input-field_selector',
		));

		// styles
		wp_enqueue_style(array(
			'acf-input-field_selector',
		));


	}





	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/


	function update_field( $field ) {

		if( !empty( $field['types'] ) ) {
			$field['types'] = array_map( 'trim', explode( ',', $field['types'] ) );
		}
		else {
			$field['types'] = array();
		}

		if( !empty( $field['groups'] ) ) {
			$field['groups'] = array_map( 'trim', explode( ',', $field['groups'] ) );
		}
		else {
			$field['groups'] = array();
		}

		return $field;

	}

	function sort_items_by_label($a, $b) {
		return strcmp( $a["label"], $b["label"] );
	}

	function get_items( $field, $items, $sort = true ) {
		$final_items = array();
		if( !empty( $items ) ) {
			foreach( $items as $item ) {
				$item_value = get_field_object( $item->post_name );

				$item_value['group'] = array(
					'ID' => $item->post_parent,
					'post_title' => get_the_title( $item->post_parent )
				);

				$final_items[$item_value['key']] = $item_value;
			}

			if( $sort == true ) {
				usort($final_items, array( $this, 'sort_items_by_label' ) );
			}

		}

		$final_items = apply_filters( 'acffsf/item_filters', $final_items, $field, $final_items );

		return $final_items;

	}

	function get_selectable_item_fields( $field ) {
		global $wpdb;

		$field_keys = array();
		$field_keys_query = 9999;

		if( !empty( $field['value'] ) ) {
			$field_keys = json_decode( $field['value'], true );
			$field_keys_query = "'" . implode( "', '", $field_keys ) . "'";
		}

		$fields = $wpdb->get_results( "SELECT post_name, post_parent FROM $wpdb->posts WHERE post_parent IN (SELECT ID FROM $wpdb->posts WHERE post_type = 'acf-field-group' ) AND post_name NOT IN ( $field_keys_query ) " );


		return $fields;

	}

	function get_selected_item_fields( $field ) {
		global $wpdb;

		$field_keys = array();
		$field_keys_query = 9999;

		if( !empty( $field['value'] ) ) {
			$field_keys = json_decode( $field['value'], true );
			$field_keys_query = "'" . implode( "', '", $field_keys ) . "'";
		}

		$fields = $wpdb->get_results( "SELECT post_name, post_parent FROM $wpdb->posts WHERE post_parent IN (SELECT ID FROM $wpdb->posts WHERE post_type = 'acf-field-group' ) AND post_name IN ( $field_keys_query ) " );

		$sortable_fields = array();
		foreach( $fields as $field ) {
			$sortable_fields[$field->post_name] = $field;
		}

		$fields = array();
		foreach( $field_keys as $key ) {
			$fields[] = $sortable_fields[$key];
		}

		return $fields;

	}


}


// create field
new acf_field_field_selector();

?>
