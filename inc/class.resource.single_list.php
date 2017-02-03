<?php

/**
 * A class for interacting with the list/$list_id endpoint of the MailChimp api.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Single_List extends Resource {

	function __construct( string $id ) {

		$this -> id = $id;

		parent::__construct();

	}

	/**
	 * The endpoint in the MailChimp API.
	 */
	function set_endpoint() {

		$id = $this -> get_id();

		$this -> endpoint = "lists/$id";

	}
	
}