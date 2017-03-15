<?php

/**
 * A class for loading our plugin.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Bootstrap {

	public function __construct() {

		$this -> create();

	}

	/**
	 * Instantiate and store a bunch of our plugin classes.
	 */
	function create() {

		global $bananas;

		$bananas -> meta                  = new Meta;
		$bananas -> settings              = new Settings;
		$bananas -> post_meta_fields      = new PostMetaFields;		
		$bananas -> config                = new Config;
		$bananas -> enqueue               = new Enqueue;		
		$bananas -> subsite_control_panel = new SubsiteControlPanel;
		$bananas -> post_meta_box         = new PostMetaBox;
		$bananas -> dashboard_widget      = new DashboardWidget;
		$bananas -> widget                = new Widget;

		if( is_multisite() ) {
			$bananas -> network_control_panel = new NetworkControlPanel;
		}

		return $bananas;

	}

}