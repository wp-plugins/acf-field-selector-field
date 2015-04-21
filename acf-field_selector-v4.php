<?php
/**
 * ACF 4 Field Class
 *
 * This file holds the class required for our field to work with ACF 4
 *
 * @author Daniel Pataki
 * @since 4.0.0
 *
 */

/**
 * ACF 4 Field Selector Class
 *
 * The Field selector class enables users to select other fields from
 * the ones created with ACF. This is the class that is used for ACF 4.
 *
 * @author Daniel Pataki
 * @since 4.0.0
 *
 */

class acf_field_field_selector extends acf_field {

	var $settings,
		$defaults;


	/**
	 * Field Constructor
	 *
	 * Sets basic properties and runs the parent constructor
	 *
	 * @author Daniel Pataki
	 * @since 4.0.0
	 *
	 */
	function __construct() {

		$this->name = 'field_selector';
		$this->label = __('Field Selector', 'acf-field-selector-field');
		$this->category = __("Choice",'acf');
		$this->defaults = array(
			'group_filtering' => 'include',
			'groups' => '',
			'type_filtering'  => 'include',
			'types'  => ''
		);

    	parent::__construct();

		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.0'
		);

		add_filter( 'acffsf/item_filters', 'acffsf_type_filter', 10, 2 );
		add_filter( 'acffsf/item_filters', 'acffsf_group_filter', 10, 2 );

	}


	/**
	 * Field Options
	 *
	 * Creates the options for the field, they are shown when the user
	 * creates a field in the back-end. Currently there are Two fields.
	 *
	 * The Group Filtering settings allow you to filter the fields shown to
	 * the user based on their group. You can include or exclude groups
	 * separated by commas.
	 *
	 * The Type Filtering settings allow you to filter the fields shown to
	 * the user based on their type. You can include or exclude types
	 * separated by commas.
	 *
	 * @param array $field The details of this field
	 * @author Daniel Pataki
	 * @since 4.0.0
	 *
	 */
	function create_options( $field ) {

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

		$key = $field['name'];

		?>
		<!-- Group Filtering -->
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Group Filtering",'acf-field-selector-field'); ?></label>
				<p class="description"><?php _e("Enter group id numbers separated by commas to include or exclude them.",'acf-field-selector-field'); ?></p>
			</td>
			<td>
				<?php

				do_action('acf/create_field', array(
					'type'		=>	'radio',
					'name'		=>	'fields['.$key.'][group_filtering]',
					'value'		=>	$field['group_filtering'],
					'layout'	=>	'horizontal',
					'choices'	=>	array(
						'include' => __('Include', 'acf-field-selector-field'),
						'exclude' => __('Exclude','acf-field-selector-field'),
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

		<!-- Type Filtering -->
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Type Filtering",'acf-field-selector-field'); ?></label>
				<p class="description"><?php _e("Enter type slugs separated by commas to include or exclude them.",'acf-field-selector-field'); ?></p>
			</td>
			<td>
				<?php

				do_action('acf/create_field', array(
					'type'		=>	'radio',
					'name'		=>	'fields['.$key.'][type_filtering]',
					'value'		=>	$field['type_filtering'],
					'layout'	=>	'horizontal',
					'choices'	=>	array(
						'include' => __('Include', 'acf-field-selector-field'),
						'exclude' => __('Exclude', 'acf-field-selector-field'),
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


	/**
	 * Field Display
	 *
	 * This function takes care of displaying our field to the users, taking
	 * the field options into account.
	 *
	 * @param array $field The details of this field
	 * @author Daniel Pataki
	 * @since 4.0.0
	 *
	 */
	function create_field( $field ) {
		?>
		<div class='multiselector'>
			<div class='selectable-container field-container'>
				<div class='title'><?php _e( 'Available Fields', 'acf-field-selector-field' ) ?></div>
				<div class='search'><input type='text' id="field-search" placeholder='<?php _e( 'Type to search...', 'acf-field-selector-field' ) ?>'></div>
				<div class='selectable field'>
					<?php
					$selectable = $this->get_items( $field, $this->get_selectable_item_fields( $field ) );
					acffsf_show_items( $selectable );
					?>
				</div>
			</div>
			<div class='selected-container field-container'>
				<div class='title'><?php _e( 'Selected Fields', 'acf-field-selector-field' ) ?></div>
				<div class='message'><?php _e( 'Drag and drop to re-order your selection', 'acf-field-selector-field' ) ?></div>
				<div class='selected field'>
					<?php
						$selected = $this->get_items( $field, $this->get_selected_item_fields( $field ), false );
						acffsf_show_items( $selected );
					?>
				</div>
			</div>
			<input type='hidden' id='field-value' name='<?php echo esc_attr($field['name']) ?>' value="<?php echo esc_attr($field['value']) ?>">
		</div>
		<?php
	}


	/**
	 * Enqueue Assets
	 *
	 * This function enqueues the scripts and styles needed to display the
	 * field
	 *
	 * @author Daniel Pataki
	 * @since 4.0.0
	 *
	 */
	function input_admin_enqueue_scripts() {

		wp_enqueue_script( 'acf-input-field_selector', $this->settings['dir'] . 'js/input.js', array('acf-input'), $this->settings['version'] );
		wp_enqueue_style( 'acf-input-field_selector', $this->settings['dir'] . 'css/input.css', array('acf-input'), $this->settings['version'] );
		wp_enqueue_script( 'jquery-ui-sortable' );

	}




	/**
	 * Pre-Save Value Modification
	 *
	 * This filter is applied to the $value before it is updated in the db
	 *
	 * @param mixed $value The value which will be saved in the database
	 * @param int $post_id The $post_id of which the value will be saved
	 * @param array $field The field array holding all the field options
	 * @return mixed The new value
	 * @author Daniel Pataki
	 * @since 4.0.0
	 *
	 */
	function update_field( $field, $post_id ) {

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

	/**
	 * Format Value
	 *
	 * This filter is applied to the $value after it is loaded from the db and
	 * before it is passed back to the API functions such as the_field
	 *
	 * @param mixed $value The value which was loaded from the database
	 * @param int $post_id The $post_id from which the value was loaded
	 * @param array $field The field array holding all the field options
	 * @return mixed The new value
	 * @author Daniel Pataki
	 * @since 4.0.0
	 *
	 */
	function format_value_for_api( $value, $post_id, $field ) {
		if( !empty( $value ) ) {
			$value = json_decode( $value, true );
		}

		return $value;
	}

	/**
	 * Get Items
	 *
	 * Retrieves items to show for the dual pane viewer
	 *
	 * @param array $field The data of the current field
	 * @param array $items Items to show
	 * @param bool $sort Wether to sort or not
	 * @return array Final list of items to show
 	 * @author Daniel Pataki
	 * @since 4.0.0
	 *
	 */
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
				usort($final_items, 'acffsf_sort_items_by_label' );
			}

		}

		$final_items = apply_filters( 'acffsf/item_filters', $final_items, $field, $final_items );

		return $final_items;

	}

	/**
	 * Get Selectable Items
	 *
	 * Retrieves a list of selectable fields
	 *
	 * @param array $field The data of the current field
	 * @return array Final list of items
 	 * @author Daniel Pataki
	 * @since 4.0.0
	 *
	 */
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

	/**
	 * Get Selected items
	 *
	 * Gets the items the user has selected
	 *
	 * @param array $field The data of the current field
	 * @return array Final list of items
 	 * @author Daniel Pataki
	 * @since 4.0.0
	 *
	 */
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
