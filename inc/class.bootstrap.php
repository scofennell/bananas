<?php

/**
 * A class for loading our plugin.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

new Bootstrap;

class Bootstrap {

	function __construct() {

		$this -> load();

		$this -> create();

	}
	
	/**
	 * Load our plugin files.
	 * 
	 * @return boolean Returns FALSE if it loads all of its files, else TRUE.
	 */
	function load() {

		// For each php file in the inc/ folder, require it.
		foreach( glob( BANANAS_PATH . 'inc/*.php' ) as $filename ) {

			require_once( $filename );

		}

		return TRUE;

	}

	/**
	 * Instantiate and store a bunch of our plugin classes.
	 */
	function create() {

		global $bananas;

		$bananas = new \stdClass();

		$bananas -> meta                  = new Meta;
		$bananas -> settings              = new Settings;
		$bananas -> post_meta_fields      = new Post_Meta_Fields;		
		$bananas -> config                = new Config;
		$bananas -> enqueue               = new Enqueue;		
		$bananas -> subsite_control_panel = new Subsite_Control_Panel;
		$bananas -> post_meta_box         = new Post_Meta_Box;
		$bananas -> dashboard_widget      = new Dashboard_Widget;
		$bananas -> widget                = new Widget;

		if( is_multisite() ) {
			$bananas -> network_control_panel = new Network_Control_Panel;
		}

		return $bananas;

	}

}