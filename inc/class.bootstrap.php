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
	 * If this plugin does not have all of its dependencies, it refuses to load its files.
	 * 
	 * @return boolean Returns FALSE if it's missing dependencies, else TRUE.
	 */
	function load() {

		// For each php file in the inc/ folder, require it.
		foreach( glob( BANANAS_PATH . 'inc/*.php' ) as $filename ) {

			require_once( $filename );

		}

		return TRUE;

	}

	function create() {

		global $bananas;

		$bananas -> meta                  = new Meta;
		$bananas -> settings              = new Settings;
		$bananas -> subsite_control_panel = new Subsite_Control_Panel;
		$bananas -> dashboard_widget      = new Dashboard_Widget;

	}

}