<?php

/**
 * A class for dealing with transients.
 *
 * @package WordPress
 * @subpackage Mailorc
 * @since Mailorc 0.1
 */

namespace Bananas;

class Transients {

	/**
	 * Clear all transients for the current list.
	 */
	function delete() {
		
		// Grab the options table.
		global $wpdb;
		$options = $wpdb -> options;

		// The DB prefix for all rows pertaining to our plugin.
		$prefix = esc_sql( BANANAS );

		// Grab each row from the database pertaining to our plugin.
		$t = esc_sql( "_transient_timeout_$prefix%" );
		$sql = $wpdb -> prepare (
			"
				SELECT option_name
				FROM $options
				WHERE option_name LIKE '%s'
			",
			$t
		);
		$transients = $wpdb -> get_col( $sql );

		// For each transient...
		foreach( $transients as $transient ) {

			// Strip away the WordPress prefix in order to arrive at the transient key.
			$key = str_replace( '_transient_timeout_', '', $transient );

			// Now that we have the key, use WordPress core to the delete the transient.
			delete_transient( $key );

		}

		// But guess what?  Sometimes transients are not in the DB, so we have to do this too:
		wp_cache_flush();

	}

}