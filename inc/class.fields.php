<?php

/**
 * A class for building form fields.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Fields {

	/**
	 * Set up our class variables.
	 * 
	 * @param boolean $current_value The current value of the setting for which we're building a field.
	 */
	function __construct( $current_value = FALSE ) {

		$this -> current_value = $current_value;
		
	}

	/**
	 * Get our WP pages as select options.
	 * 
	 * @return string Our WP pages as select options.
	 */
	function get_pages_as_options() {

		$pages = get_pages();
		$pages = wp_list_pluck( $pages, 'post_title', 'ID' );

		// Add a blank item to the beginning.
		$pages = array( 0 => esc_html__( 'Please choose a page.', 'mailorc' ) ) + $pages;

		$out = $this -> get_array_as_options( $pages );

		return $out;

	}

	/**
	 * Get our MC lists as select options.
	 * 
	 * @return string Our MC lists as select options.
	 */
	function get_lists_as_options() {

		$lists     = new Lists;
		$get_lists = $lists -> get_as_kv();
		if( ! is_array( $get_lists ) ) { return $get_lists; }

		// Add a blank item to the beginning.
		$get_lists = array( 0 => esc_html__( 'Please choose a list.', 'mailorc' ) ) + $get_lists;

		$out = $this -> get_array_as_options( $get_lists );

		return $out;

	}	

	/**
	 * Convert as associative array to select options.
	 * 
	 * @param  array  $array An associative array.
	 * @return string        Select options.
	 */
	function get_array_as_options( array $array ) {

		$out = '';

		foreach( $array as $k => $v ) {

			$selected = selected( $this -> current_value, $k, FALSE );

			$out .= "<option value='$k' $selected>$v</option>";

		}

		return $out;

	}

}