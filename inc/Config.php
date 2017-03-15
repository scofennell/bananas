<?php

/**
 * A class for getting info about the configuration of our plugin.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Config {

	function __construct() {

		global $bananas;
		$this -> settings = $bananas -> settings;

	}

	/**
	 * Make a test call to the API to see if we have a valid API key.
	 * 
	 * @return mixed Returns an http response on success, a wp_error on failure.
	 */
	function has_api_key() {

		$call = new Call();

		$response = $call -> get_response();

		return $response;

	}

	/**
	 * Get the API key, preferring the subsite API key, falling back to the network API key.
	 * 
	 * @return string The API key. 
	 */
	function get_api_key() {

		return $this -> settings -> get_subsite_value( 'mailchimp_account_setup', 'api_key' );

	}

	/**
	 * Make a test call to the API to see if we have a valid list_id.
	 * 
	 * @return mixed Returns an http response on success, a wp_error on failure.
	 */
	function has_list_id() {

		$lists = new Lists;

		$response = $lists -> get_response();

		return $response;

	}

	/**
	 * Get the list_id, preferring the subsite list_id, falling back to the network list_id.
	 * 
	 * @return string The list_id. 
	 */
	function get_list_id() {

		return $this -> settings -> get_subsite_value( 'mailchimp_account_setup', 'list_id' );

	}	

}