<?php

class acf_field_field_selector extends acf_Field
{

	// vars
	var $settings,
		$defaults;


	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*	- This function is called when the field class is initalized on each page.
	*	- Here you can add filters / actions and setup any other functionality for your field
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	*
	*-------------------------------------------------------------------------------------*/

	function __construct($parent)
	{

		// do not delete!
		parent::__construct($parent);

		// vars
		$this->name = 'field_selector';
		$this->label = __('Field Selector', 'acf');
		$this->category = __("Relational",'acf'); // Basic, Content, Choice, etc
		$this->defaults = array(
			'allowed_groups'     => '',
			'allowed_types'      => '',
			'exclude_types'      => '',
			'max'                => '',
			'return_value'       => 'key',
			'field_type'         => 'autocomplete',
			'additional_fields'  => '',
		);


		// do not delete!
		parent::__construct();

		$this->l10n = array(
			'max'		=> __("Maximum fields reached ( {max} fields )",'acf'),
			'tmpl_li'	=> '
				<li>
					<a href="#" data-name="<%= name %>" data-value="<%= value %>"><%= title %><span class="acf-button-remove"></span></a>
					<input type="hidden" name="<%= name %>[]" value="<%= value %>" />
				</li>
			'
		);


		// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.0'
		);

  }


	/*
	*  Field Groups Array
	*
	*  Generates an array of all field groups
	*
	*/
	function get_field_group_array() {
		$field_groups = get_posts( array( 'post_type' => 'acf', 'posts_per_page' => -1 ) );
		$groups = array();

		foreach( $field_groups as $group ) {
			$groups[$group->ID] = $group->post_title;
		}

		return $groups;
	}



 	/*
  *  helpers_get_path
  *
  *  @description: calculates the path (works for plugin / theme folders)
  *  @since: 3.6
  *  @created: 30/01/13
  */

  function helpers_get_path($file)
  {
    return trailingslashit(dirname($file));
  }


  /*
  *  helpers_get_dir
  *
  *  @description: calculates the directory (works for plugin / theme folders)
  *  @since: 3.6
  *  @created: 30/01/13
  */

  function helpers_get_dir($file)
  {
    $dir = trailingslashit(dirname($file));
    $count = 0;


    // sanitize for Win32 installs
    $dir = str_replace('\\', '/', $dir);


    // if file is in plugins folder
    $wp_plugin_dir = str_replace('\\', '/', WP_PLUGIN_DIR);
    $dir = str_replace($wp_plugin_dir, WP_PLUGIN_URL, $dir, $count);


    if($count < 1)
    {
      // if file is in wp-content folder
      $wp_content_dir = str_replace('\\', '/', WP_CONTENT_DIR);
      $dir = str_replace($wp_content_dir, WP_CONTENT_URL, $dir, $count);
    }


    if($count < 1)
    {
      // if file is in ??? folder
      $wp_dir = str_replace('\\', '/', ABSPATH);
      $dir = str_replace($wp_dir, site_url('/'), $dir);
    }

    return $dir;
  }


	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*	- this function is called from core/field_meta_box.php to create extra options
	*	for your field
	*
	*	@params
	*	- $key (int) - the $_POST obejct key required to save the options to the field
	*	- $field (array) - the field object
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	*
	*-------------------------------------------------------------------------------------*/

	function create_options($key, $field)
	{
		$field = array_merge($this->defaults, $field);
		$key = $field['name'];


		// Create Field Options HTML
		?>

		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Display Type",'acf'); ?></label>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'	=>	'select',
					'name'	=>	'fields['.$key.'][field_type]',
					'value'	=>	$field['field_type'],
					'choices' => array(
						__( 'Multiple Values', 'acf' ) => array(
							'autocomplete' => __( 'Autocomplete', 'acf' ),
							'checkbox' => __( 'Checkbox', 'acf' ),
							'multi_select' => __( 'Multi Select', 'acf' )
						),
						__( 'Single Value', 'acf' ) => array(
							'radio' => __( 'Radio Buttons', 'acf' ),
							'select' => __( 'Select', 'acf' )
						)
					)
				));
				?>
			</td>
		</tr>



		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Return Value",'acf'); ?></label>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][return_value]',
					'value'	=>	$field['return_value'],
					'choices' => array(
						'key' => __( 'Field Key', 'acf' ),
						'name' => __( 'Field Name', 'acf' ),
						'object' => __( 'Field Object', 'acf' ),
					)
				));
				?>
			</td>
		</tr>


		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Maximum items",'acf'); ?></label>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'	=>	'number',
					'name'	=>	'fields['.$key.'][max]',
					'value'	=>	$field['max'],
				));
				?>
			</td>
		</tr>

		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Allowed Field Types",'acf'); ?></label>
				<p class="description"><?php _e( "Leave empty to allow all, otherwise one type per row", 'acf' ) ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'	=>	'textarea',
					'name'	=>	'fields['.$key.'][allowed_types]',
					'value'	=>	implode( "\n", $field['allowed_types'] ),
				));
				?>
			</td>
		</tr>



		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Excluded Field Types",'acf'); ?></label>
				<p class="description"><?php _e( "Set field types to exclude specifically, one per row", 'acf' ) ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'	=>	'textarea',
					'name'	=>	'fields['.$key.'][exclude_types]',
					'value'	=>	implode( "\n", $field['exclude_types'] ),
				));
				?>
			</td>
		</tr>

		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Allowed Field Groups",'acf'); ?></label>
				<p class="description"><?php _e( "Leave empty to allow all, otherwise one type per row", 'acf' ) ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'	=>	'select',
					'multiple' => true,
					'name'	=>	'fields['.$key.'][allowed_groups]',
					'value'	=>	$field['allowed_groups'],
					'choices' => $this->get_field_group_array()
				));
				?>
			</td>
		</tr>
		<?php
	}



	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*	- this function is called on edit screens to produce the html for this field
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	*
	*-------------------------------------------------------------------------------------*/

	function create_field($field)
	{
		$args = array(
			'post_type' => 'acf',
			'posts_per_page' => -1
		);

		if( !empty( $field['allowed_groups'] ) ) {
			$args['include'] = $field['allowed_groups'];
		}

		$field_groups = get_posts( $args );

		$fields = array();
		if( !empty( $field_groups ) ) {
			foreach( $field_groups as $field_group ) {
				$custom_fields = get_post_meta( $field_group->ID );
				foreach( $custom_fields as $key => $customfield ) {
					if( substr_count( $key, 'field_' ) > 0 ) {
						$customfield = get_post_meta( $field_group->ID, $key, true );
						$fields[$customfield['label']] = array(
							'group' => $field_group->post_title,
							'field' => $customfield
						);
					}
				}
			}
			ksort($fields);
		}


		foreach( $fields as $name => $data ) {
			if( !empty( $field['allowed_types'] ) && !in_array( $data['field']['type'], $field['allowed_types'] ) ) {
				unset( $fields[$name] );
			}
			if( !empty( $field['exclude_types'] ) && in_array( $data['field']['type'], $field['exclude_types'] ) ) {
				unset( $fields[$name] );
			}
		}


		?>
		<div>

		<?php

			$multiselect = ( $field['field_type'] == 'multi_select' ) ? 'multiple="multiple"' : '';

			$fields_by_group = array();
			foreach( $fields as $name => $data ) {
				$fields_by_group[$data['group']][] = $data['field'];
			}

			$fields_by_key = array();
			foreach( $fields as $name => $data ) {
				$fields_by_key[$data['field']['key']]['field'] = $data['field'];
				$fields_by_key[$data['field']['key']]['group'] = $data['group'];
			}


			switch ( $field['field_type'] ) {
				case 'select' :
				case 'multi_select' :
					echo "<select " . $multiselect . " name='" . $field['name'] . "[]'>";
					foreach( $fields_by_group as $name => $group ) {
						echo '<optgroup label="' . $name . '">';
						foreach( $group as $item ) {
							echo '<option ' . selected( $field['value'], $item['key'] , false ) . ' value="' . $item['key'] . '">' . $item['label'] . '</value>';
						}
						echo '</optgroup>';
					}
					echo '</select>';
					break;
				case 'radio' :
					echo '<ul class="acf-radio-list radio vertical acffs-option-group-list">';
					foreach( $fields_by_group as $name => $group ) {
						echo '<li><span class="acffs-option-group-label">' . $name . '</span><ul>';
						foreach( $group as $item ) {
							echo '<li><label><input ' . checked( $field['value'], $item['key'] , false ) . ' type="radio" name="' . $field['name'] . '[]" value="' . $item['key'] . '">' . $item['label'] . '</label></li>';
						}
						echo '</ul></li>';
					}
					echo '</ul>';
					break;
				case 'checkbox' :
					echo '<ul class="acf-checkbox-list checkbox vertical acffs-option-group-list">';
					foreach( $fields_by_group as $name => $group ) {
						echo '<li><span class="acffs-option-group-label">' . $name . '</span><ul>';
						foreach( $group as $item ) {
							echo '<li><label><input ' . checked( $field['value'], $item['key'] , false ) . ' type="checkbox" name="' . $field['name'] . '[]" value="' . $item['key'] . '">' . $item['label'] . '</label></li>';
						}
						echo '</ul></li>';
					}
					echo '</ul>';
					break;
				case 'autocomplete' :
				default :

					$attributes = array(
						'max' => $field['max'],
						's' => '',
						'field_key' => $field['key']
					);

				?>
				<div class="acffs-autocomplete-container" <?php foreach( $attributes as $k => $v ): ?> data-<?php echo $k; ?>="<?php echo $v; ?>"<?php endforeach; ?>>
					<input type="hidden" name="<?php echo $field['name']; ?>" value="" />

				<!-- Left List -->
				<div class="acffs-autocomplete-left">
					<table class="widefat">
						<thead>
							<tr>
								<th>
									<input class="acffs-autocomplete-search" placeholder="<?php _e("Search...",'acf'); ?>" type="text" id="custom_field_selector_<?php echo $field['name']; ?>" />
								</th>
							</tr>
						</thead>
					</table>
					<ul class="bl acffs-autocomplete-list">
						<?php
							if( !empty( $fields ) ) :
								foreach( $fields as $customfield ) :
								$hidden = ( in_array( $customfield['field']['key'], $field['value'] ) ) ? 'class="hide"' : '';
							?>
						<li <?php echo $hidden ?>>
							<a href="#" data-name="<?php echo $customfield['field']['label'] ?> <?php echo $customfield['group'] ?>" data-value="<?php echo $customfield['field']['key'] ?>"><?php echo $customfield['field']['label'] ?> <span class='additional-data'><?php echo $customfield['group'] ?></span> <span class="acf-button-add"></span></a>
						</li>
						<?php endforeach; endif ?>

					</ul>
				</div>
				<!-- /Left List -->

				<!-- Right List -->
				<div class="acffs-autocomplete-right">
					<ul class="bl acffs-autocomplete-list">
					<?php
					if( !empty( $field['value'] ) )
					{
						foreach( $field['value'] as $value )
						{

							if( !empty( $fields_by_key[$value] ) ) {

							$customfield = $fields_by_key[$value];
							echo '<li>
								<a href="#" class="" data-name="' . $customfield['field']['label'] . '" data-value="' . $customfield['field']['key'] . '">' . $customfield['field']['label'] . '<span class="additional-data">' . $customfield['group'] .'</span> <span class="acf-button-remove"></span></a>
								<input type="hidden" name="' . $field['name'] . '[]" value="' . $customfield['field']['key'] . '" />
							</li>';

							}


						}
					}

					?>
					</ul>
				</div>

				</div>
				<!-- / Right List -->


				<?php


			}
		?>

		</div>
		<?php
	}


	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_scripts / admin_print_styles
	*	- this function is called in the admin_print_scripts / admin_print_styles where
	*	your field is created. Use this function to register css and javascript to assist
	*	your create_field() function.
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	*
	*-------------------------------------------------------------------------------------*/

	function admin_print_scripts()
	{
		// register acf scripts
		wp_register_script('acf-input-field_selector', $this->settings['dir'] . 'js/input.js', array('acf-input'), $this->settings['version']);


		// scripts
		wp_enqueue_script(array(
			'acf-input-field_selector',
		));

	}

	function admin_print_styles()
	{
		// Note: This function can be removed if not used
		wp_register_style('acf-input-field_selector', $this->settings['dir'] . 'css/input.css', array('acf-input'), $this->settings['version']);
		// styles
		wp_enqueue_style(array(
			'acf-input-field_selector',
		));

	}



	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*	- called from your template file when using the API functions (get_field, etc).
	*	This function is useful if your field needs to format the returned value
	*
	*	@params
	*	- $post_id (int) - the post ID which your value is attached to
	*	- $field (array) - the field object.
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	*
	*-------------------------------------------------------------------------------------*/

	function get_value_for_api($post_id, $field)
	{
		$value = get_field( $field['key'], $post_id  );
		if( !empty( $value ) ) {
			if( $field['return_value'] == 'object' ) {
				foreach( $value as $key => $item ) {
					$value[$key] = get_field_object( $item );
				}
			}

			if( $field['return_value'] == 'name' ) {
				foreach( $value as $key => $item ) {
					$field_object = get_field_object( $item );
					$value[$key] = $field_object['name'];
				}
			}
		}
		

		return $value;

	}

	function update_value($post_id, $field, $value)
	{
		$field['allowed_types'] = explode( "\n", $field['allowed_types'] );
		$field['allowed_types'] = array_map( 'trim', $field['allowed_types'] );
		$field['allowed_types'] = array_filter( $field['allowed_types'] );

		$field['exclude_types'] = explode( "\n", $field['exclude_types'] );
		$field['exclude_types'] = array_map( 'trim', $field['exclude_types'] );
		$field['exclude_types'] = array_filter( $field['exclude_types'] );

		return $field;
	}

}

?>