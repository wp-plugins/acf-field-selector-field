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

		acf_render_field_setting( $field, array(
			'label'			=> __('Display Type','acf-field_selector'),
			'type'			=> 'select',
			'name'			=> 'field_type',
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

		acf_render_field_setting( $field, array(
			'label'			=> __('Return Value','acf-field_selector'),
			'type'			=> 'radio',
			'name'			=> 'return_value',
			'choices' => array(
				'key' => __( 'Field Key', 'acf' ),
				'name' => __( 'Field Name', 'acf' ),
				'object' => __( 'Field Object', 'acf' ),
			)
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Maximum items','acf-field_selector'),
			'type'			=> 'number',
			'name'			=> 'max',
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Allowed Field Types','acf-field_selector'),
			'instructions'	=> __('Leave empty to allow all, otherwise one type per row','acf-field_selector'),
			'type'			=> 'textarea',
			'name'			=> 'allowed_types',
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Excluded Field Types','acf-field_selector'),
			'instructions'	=> __('Set field types to exclude specifically, one per row','acf-field_selector'),
			'type'			=> 'textarea',
			'name'			=> 'exclude_types',
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Allowed Field Groups','acf-field_selector'),
			'type'			=> 'select',
			'multiple'      => true,
			'name'			=> 'allowed_groups',
			'choices'       => $this->get_field_group_array()
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
		$field['allowed_types'] = explode( "\n", $field['allowed_types'] );
		$field['exclude_types'] = explode( "\n", $field['exclude_types'] );

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
							echo '<option ' . selected( $field['value'][0], $item['key'] , false ) . ' value="' . $item['key'] . '">' . $item['label'] . '</value>';
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
								$hidden = ( !empty( $field['value'] ) && is_array( $field['value'] ) && in_array( $customfield['field']['key'], $field['value'] ) ) ? 'class="hide"' : '';
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
					if( !empty( $field['value'] ) && is_array( $field['value'] ) )
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


		// register & include JS
		wp_register_script( 'acf-input-field_selector', "{$dir}js/input.js" );
		wp_enqueue_script('acf-input-field_selector');


		// register & include CSS
		wp_register_style( 'acf-input-field_selector', "{$dir}css/input.css" );
		wp_enqueue_style('acf-input-field_selector');


	}



	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/



	function format_value( $value, $post_id, $field ) {
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






	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/



	function load_field( $field ) {
		if( !empty( $field['allowed_types'] ) && is_array( $field['allowed_types'] ) ) {
			$field['allowed_types'] = implode( "\n", $field['allowed_types'] );
		}
		if( !empty( $field['exclude_types'] ) && is_array( $field['exclude_types'] ) ) {
			$field['exclude_types'] = implode( "\n", $field['exclude_types'] );
		}

		return $field;

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


}


// create field
new acf_field_field_selector();

?>
