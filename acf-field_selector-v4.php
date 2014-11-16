<?php

class acf_field_field_selector extends acf_field {

	var $settings,
		$defaults;


	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/

	function __construct()
	{

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

		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.0'
		);
		
		add_filter( 'acffsf/item_filters', array( 'acf_field_field_selector_common', 'type_filter' ), 10, 2 );
		add_filter( 'acffsf/item_filters', array( 'acf_field_field_selector_common', 'group_filter' ), 10, 2 );

	}


	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like below) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/

	function create_options( $field )
	{

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

		// key is needed in the field names to correctly save the data
		$key = $field['name'];


		// Create Field Options HTML
		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Group Filtering",'acf'); ?></label>
		<p class="description"><?php _e("Enter group id numbers separated by commas to include or exclude them.",'acf'); ?></p>
	</td>
	<td>
		<?php

		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][group_filtering]',
			'value'		=>	$field['group_filtering'],
			'layout'	=>	'horizontal',
			'choices'	=>	array(
				'include' => __('Include'),
				'exclude' => __('Exclude'),
			)
		));

		?>

		<?php

		do_action('acf/create_field', array(
			'type'		=>	'text',
			'name'		=>	'fields['.$key.'][groups]',
			'value'		=>	$field['groups']
		));

		?>


	</td>
</tr>


<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Type Filtering",'acf'); ?></label>
		<p class="description"><?php _e("Enter type slugs separated by commas to include or exclude them.",'acf'); ?></p>
	</td>
	<td>
		<?php

		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][type_filtering]',
			'value'		=>	$field['type_filtering'],
			'layout'	=>	'horizontal',
			'choices'	=>	array(
				'include' => __('Include'),
				'exclude' => __('Exclude'),
			)
		));

		?>

		<?php

		do_action('acf/create_field', array(
			'type'		=>	'text',
			'name'		=>	'fields['.$key.'][types]',
			'value'		=>	$field['types']
		));

		?>


	</td>
</tr>



		<?php
	}


	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function create_field( $field )
	{
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
	*  Use this action to add CSS + JavaScript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{


		// register ACF scripts
		wp_register_script( 'acf-input-field_selector', $this->settings['dir'] . 'js/input.js', array('acf-input'), $this->settings['version'] );
		wp_register_style( 'acf-input-field_selector', $this->settings['dir'] . 'css/input.css', array('acf-input'), $this->settings['version'] );

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
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the field group ID (post_type = acf)
	*
	*  @return	$field - the modified field
	*/

	function update_field( $field, $post_id )
	{

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

	function format_value_for_api( $value, $post_id, $field ) {
		if( !empty( $value ) ) {
			$value = json_decode( $value, true );
		}

		return $value;
	}


	function sort_items_by_label($a, $b) {
		return strcmp( $a["label"], $b["label"] );
	}

	function get_items( $field, $items, $sort = true ) {
		$final_items = array();
		if( !empty( $items ) ) {
			foreach( $items as $item ) {
				$item_value = unserialize( $item->meta_value );

				$item_value['group'] = array(
					'ID' => $item->post_id,
					'post_title' => get_the_title( $item->post_id )
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

		$fields = $wpdb->get_results( "SELECT meta_key, meta_value, post_id FROM $wpdb->postmeta WHERE post_ID IN (SELECT ID FROM $wpdb->posts WHERE post_type = 'acf' AND post_status = 'publish' ) AND meta_key LIKE 'field_%'
		AND meta_key NOT IN ( $field_keys_query )  " );
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

		$fields = $wpdb->get_results( "SELECT meta_key, meta_value, post_id FROM $wpdb->postmeta WHERE post_ID IN (SELECT ID FROM $wpdb->posts WHERE post_type = 'acf' AND post_status = 'publish' ) AND meta_key IN ( $field_keys_query ) " );

		$sortable_fields = array();
		foreach( $fields as $field ) {
			$sortable_fields[$field->meta_key] = $field;
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
