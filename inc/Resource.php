<?php

/**
 * A class for interacting with a given MailChimp API endpoint.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

abstract class Resource {

	function __construct( $args = array() ) {

		// Store the endpoint to which we're making api calls.
		$this -> set_endpoint();

		// Store the args that were passed in.
		$this -> set_args( $args );

	}

	/**
	 * Get the endpoint to which we're making API calls.
	 * 
	 * @return string The endpoint to which we're making API calls.
	 */
	function get_endpoint() {

		return $this -> endpoint;

	}

	/**
	 * Store the args that were passed in to our class.
	 */
	function set_args( $args ) {

		$this -> args = $args;

	}

	/**
	 * Get the API response for our resource.
	 * 
	 * @return mixed Returns an http response on success, wp_error on failure.
	 */
	function get_response() {

		$args = array(
			'endpoint' => $this -> get_endpoint(),
		);

		$call = new Call( $args );

		return $call -> get_response();

	}

	/**
	 * Get the total number of items in an endpoint.
	 * 
	 * @return integer The total number of items in an endpoint.
	 */
	function get_total_items() {

		$response = $this -> get_response();

		return $response['total_items'];

	}

}