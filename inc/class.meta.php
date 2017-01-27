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

	/**
	 * Get the public-facing name for the plugin.
	 * 
	 * @return string The public-facing name for the plugin.
	 */
	function get_label() {

		return esc_html__( 'Bananas', 'bananas' );

	}

	/**
	 * Make a test call to the API to see if we have a valid API key.
	 * 
	 * @return mixed Returns an http response on success, a wp_error on failure.
	 */
	function has_api_key() {

		$call = new call();

		$response = $call -> get_response();

		return $response;

	}

}