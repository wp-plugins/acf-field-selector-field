<?php



class acf_field_field_selector_common {

	public static function type_filter( $items, $field ) {
		if( !empty( $items ) ) {
		foreach( $items as $key => $item ) {
			if( !empty( $field['types'] ) ) {
					if( $field['type_filtering'] == 'include' && !in_array( $item['type'], $field['types'] ) ) {
					unset( $items[$key] );
				}
				if( $field['type_filtering'] == 'exclude' && in_array( $item['type'], $field['types'] ) ) {
					unset( $items[$key] );
				}
			}
		}
		}

		return $items;
	}

	public static function group_filter( $items, $field ) {
		if( !empty( $items ) ) {
		foreach( $items as $key => $item ) {
			if( !empty( $field['groups'] ) ) {
					if( $field['group_filtering'] == 'include' && !in_array( $item['group']['ID'], $field['groups'] ) ) {
					unset( $items[$key] );
				}
				if( $field['group_filtering'] == 'exclude' && in_array( $item['group']['ID'], $field['groups'] ) ) {
					unset( $items[$key] );
				}
			}
		}
		}

		return $items;
	}


	function show_items( $items ) {
		echo '<ul>';
		if( !empty( $items ) ) {
			foreach( $items as $item ) {
				$search_term = $item['label']  . ' ' . $item['group']['post_title'];
				echo '<li data-search_term="' . $search_term . '" data-key="' . $item['key'] . '"><strong>' . $item['label'] . '</strong><br>' . $item['group']['post_title'] . '</li>';
			}
		}
		echo '</ul>';
	}
}

?>
