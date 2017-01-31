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

	function __construct() {

		global $bananas;

		$this -> settings = $bananas -> settings;

	}

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

	/**
	 * Get the API key, preferring the subsite API key, falling back to the network API key.
	 * 
	 * @return string The API key. 
	 */
	function get_api_key() {

		$out = '';

		if( is_multisite() ) {

			$out = $this -> settings -> get_network_value( 'mailchimp_account_setup', 'api_key' );

		} else {

			$subsite_api_key = $this -> settings -> get_subsite_value( 'mailchimp_account_setup', 'api_key' );

			if( ! empty( $subsite_api_key ) ) {

				$out = $subsite_api_key;

			}

		}

		return $out;

	}

}