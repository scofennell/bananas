<?php

/**
 * A class for interacting with the lists endpoint of the MailChimp api.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Lists extends Resource {

	function __construct( $args = array() ) {

		parent::__construct();

	}

	/**
	 * The endpoint in the MailChimp API.
	 */
	function set_endpoint() {

		$this -> endpoint = 'lists';

	}

	/**
	 * Get the lists as an ID -> name array.
	 * 
	 * @return array The lists as an ID -> name array.
	 */
	function get_as_kv() {

		$lists = $this -> get_response();
		if( is_wp_error( $lists ) ) { return $lists; }
		$lists = $lists['lists'];

		foreach( $lists as $list ) {

			$out[ $list['id'] ] = $list['name'];

		}

		return $out;

	}

	/**
	 * Build a bar graph to display subscrber counts.
	 * 
	 * @return string A bar graph showing subcriber counts per list.
	 */
	function get_graph() {

		// Get the lists.
		$lists = $this -> get_response();
		if( is_wp_error( $lists ) ) { return $lists; }
		$lists = $lists['lists'];

		// Set up the args for our graph.
		$args = array(
			'title' => esc_html__( 'Subscriber Count by List', 'bananas' ),
			'values' => array(),
		);

		// For each list...
		foreach( $lists as $list ) {

			// Grab the list ID.
			$list_id = sanitize_text_field( $list['id'] );

			// Grab the list name.
			$list_name = sanitize_text_field( $list['name'] );

			// Grab the member count.
			$stats = $list['stats'];
			$member_count = absint( $stats['member_count'] );

			// Add it to the graph.
			$args['values'][ $list_id ] = array(

				'label' => $list_name,
				'value' => $member_count,
			
			);

		}

		$graph = new Graph( $args );

		return $graph -> get();

	}

}