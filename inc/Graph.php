<?php

/**
 * A class for making a bar graph.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Graph {

	/**
	 * Set up our class args and resources.
	 * 
	 * @param array $args Expects 'title' and 'values'.
	 */
	function __construct( $args = array() ) {

		// We'll need our plugin CSS.
		wp_enqueue_style( BANANAS . '-style' );
		
		// Store the args that were passed in.
		$this -> set_args( $args );

		// Store the title of the graph.
		$this -> set_title();

		// Store the values for the graph.
		$this -> set_values();

		// Store the max value for the graph.
		$this -> set_max_value();		

	}

	/**
	 * Get the args that were passed into the class.
	 * 
	 * @return array The args that were passed into the class.
	 */
	function get_args() {

		return $this -> args;

	}

	/**
	 * Store the args that were passed in to our class.
	 */
	function set_args( $args ) {

		$this -> args = $args;

	}

	/**
	 * Get the values for the graph bars.
	 * 
	 * @return array The values for the graph bars.
	 */
	function get_values() {

		return $this -> values;

	}

	/**
	 * Store the values for the graph bars.
	 */
	function set_values() {

		$args = $this -> get_args();

		$this -> values = $args['values'];

	}

	/**
	 * Get the title of the graph.
	 * 
	 * @return string The title of the graph.
	 */
	function get_title() {

		return $this -> title;

	}

	/**
	 * Store the title of the graph.
	 */
	function set_title() {

		$args = $this -> get_args();

		$this -> title = $args['title'];

	}

	/**
	 * Get the largest value for the graph.
	 * 
	 * @return float The largest value for the graph.
	 */
	function get_max_value() {

		return $this -> max_value;

	}

	/**
	 * Store the largest value for the graph.
	 */
	function set_max_value() {

		$values = $this -> get_values();

		// Let's start off assuming 1 is the largest value.
		$max_value = 1.0;

		// Loop through the values.
		foreach( $values as $k => $v ) {

			$value = floatval( $v['value'] );

			// Do we have a new largest value?
			if( $value > $max_value ) {
				$max_value = $v['value'];
			}

		}

		$this -> max_value = $max_value;

	}	

	/**
	 * The primary template tag for this class.  Get a bar graph.
	 * 
	 * @return string A bar graph.
	 */
	function get() {

		// A css class for the output.
		$class = sanitize_html_class( __CLASS__ . '-' . __FUNCTION__ );

		// Will hold a bar graph.
		$out = '';

		// Get the graph values.
		$values = $this -> get_values();

		// Get the largest bar value.
		$max_val = $this -> get_max_value();

		// We don't want to divide by zero.
		if( empty( $max_val ) ) { return FALSE; }

		// For each value...
		foreach( $values as $k => $v ) {

			// The label for this bar.
			$label = esc_html__( $v['label'] );

			// The value for this bar.
			$value = floatval( $v['value'] );

			// The width of this bar.
			$width = ( $value / $max_val ) * 100 . '%';

			// Build the bar.
			$out .= "
				<div class='$class-bar'>
					<span class='$class-bar-label'>$label</span>
					<span class='$class-bar-rectangle' style='width:$width;'>
						<span class='$class-bar-rectangle-value'>$value</span>
					</span>
				</div>
			";

		}

		// No bars?  Bail.
		if( empty( $out ) ) { return FALSE; }

		$title = $this -> get_title();

		$out = "
			<div class='$class'>
				<h3 class='$class-title'>$title</h3>
				<div class='$class-bars'>
					$out
				</div>
			</div>
		";

		return $out;

	}

}