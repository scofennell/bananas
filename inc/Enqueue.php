<?php

/**
 * A class for loading our plugin scripts and styles.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Enqueue {

	function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'script' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'style' ) );

	}
	
	/**
	 * If this plugin does not have all of its dependencies, it refuses to load its files.
	 * 
	 * @return bool Whether the script has been registered. True on success, false on failure.
	 */
	function script() {

		$handle    = BANANAS . '-script';
		$src       = BANANAS_URL . '/js/script.js';
		$deps      = array( 'jquery' );
		$ver       = BANANAS_VERSION;
		$in_footer = TRUE;

		return wp_register_script( $handle, $src, $deps, $ver, $in_footer );

	}

	/**
	 * Register our plugin CSS.
	 * 
	 * @return bool Whether the style has been registered. True on success, false on failure.
	 */
	function style() {

		$handle = BANANAS . '-style';
		$src    = BANANAS_URL . '/css/style.css';
		$deps   = FALSE;
		$ver    = BANANAS_VERSION;
		$media  = 'all';

		return wp_register_style( $handle, $src, $deps, $ver, $media );

	}

}