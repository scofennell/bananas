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

	function get_interest_categories() {

		$ic = new Interest_Categories( $this -> get_id() );

		return $ic -> get_collection();

	}

	function get_interests_as_list() {

		$out = '';

		$interest_categories = $this -> get_interest_categories();

		foreach( $interest_categories as $interest_category_id => $interest_category ) {

			$ic_asset = $interest_category['asset'];
			$ic_collection = $interest_category['collection'];
			$ic_title = $ic_asset -> get_title();

			$out .= "<ul><h4>$ic_title</h4>";

			foreach( $ic_collection as $interest_id => $interest ) {

				$name = $interest -> get_name();
				$id = $interest -> get_id();

				$out .= "
					<li>$name: <code>$id</code></li>
				";

			}

			$out .= '</ul>';

		}

		if( empty( $out ) ) { return FALSE; }

		return $out;

	}

	function get_interests_as_comma_sep() {
	
		$out = '';

		$interest_categories = $this -> get_interest_categories();

		$list_id = $this -> get_id();

		$max = 2;
		$i = 0;

		foreach( $interest_categories as $interest_category_id => $interest_category ) {

			$ic_asset = $interest_category['asset'];
			$ic_collection = $interest_category['collection'];

			foreach( $ic_collection as $interest_id => $interest ) {

				$i++;
				$out .= $interest -> get_id() . ',';

				if( $i == $max ) { break; }

			}

			if( $i == $max ) { break; }

		}

		$out = rtrim( $out, ',' );

		if( empty( $out ) ) { return FALSE; }

		return $out;

	}
	
}