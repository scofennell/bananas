<?php

/**
 * A class for creating a dashboard widget.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

abstract class Control_Panel {

	function __construct() {

		// Grab our plugin-wide helpers.
		global $bananas;
		$this -> meta     = $bananas -> meta;
		$this -> settings = $bananas -> settings;

	}

	function get_asterisk() {

		$class = sanitize_html_class( __CLASS__ . '-' . __FUNCTION__ );

		$out = "<span class='$class' title='Subsites will inherit this setting if they leave it empty.'>*</span>";

		return $out;

	}

}