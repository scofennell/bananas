<?php

/**
 * A class for interacting with the root endpoint of the MailChimp api.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Root extends Resource {

	function __construct( $args = array() ) {

		parent::__construct();

	}

	/**
	 * The endpoint in the MailChimp API.
	 */
	function set_endpoint() {

		$this -> endpoint = '/';

	}

	/**
	 * Get the total number of subscribers from this account.
	 * 
	 * @return integer The total number of subscribers from this account.
	 */
	function get_total_subscribers() {

		$response = $this -> get_response();

		return $response['total_subscribers'];

	}

}