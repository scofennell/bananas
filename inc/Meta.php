<?php

/**
 * A class for getting info about our plugin itself.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Meta {

	function __construct() {}

	/**
	 * Get the public-facing name for the plugin.
	 * 
	 * @return string The public-facing name for the plugin.
	 */
	function get_label() {

		return esc_html__( 'Bananas', 'bananas' );

	}

}