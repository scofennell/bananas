<?php

/**
 * A class for creating a dashboard widget.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Subsite_Control_Panel extends Control_Panel {

	function __construct() {

		parent::__construct();

		// Get the subsite settings values.
		$this -> values = $this -> settings -> get_subsite_values();

		// Add our options page to wp-admin.
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );

		// Register our options sections.
		add_action( 'admin_init', array( $this, 'register' ) );

		// Register our admin notices.
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

	}

	/**
	 * Get the slug for the form handler of our settings page.
	 * 
	 * @return string The slug for the form handler of our settings page.
	 */
	function get_handler_slug() {

		return 'options.php';

	}	

	/**
	 * Determine if we are on the settings page.
	 * 
	 * @return boolean Returns TRUE if we are on the settings page, else FALSE.
	 */
	function is_current_page() {

		if( is_multisite() ) {
			if( is_network_admin() ) { return FALSE; }
		}

		global $pagenow;

		// If we're not in either of these two, bail.  options.php is required for form handling.
		if( ( $pagenow != $this -> get_parent_slug() ) && ( $pagenow != $this -> get_handler_slug() ) ) { return FALSE; }

		if( $pagenow == $this -> get_parent_slug() ) {

			if( ! isset( $_GET['page'] ) ) { return FALSE; }

			if( $_GET['page'] != BANANAS ) { return FALSE; }

		} 

		return TRUE;

	}

	/**
	 * Get the url of the form handler.
	 * 
	 * @return string The url of the form handler.
	 */
	function get_handler_url(){

		return get_admin_url( FALSE, $this -> get_handler_slug() );

	}

	/**
	 * Loop through our settings and register them.
	 */
	public function register() {

		if( ! $this -> is_current_page() ) { return FALSE; }

		// Grab our plugin settings definition.
		$subsite_settings = $this -> settings -> get_subsite_settings();

		// For each section of settings...
		foreach( $subsite_settings as $section_id => $section ) {

			$this -> add_settings_section( $section, $section_id );

			// For each setting in this section...
			foreach( $section['settings'] as $setting_id => $setting ) {

				// It might be the case that this setting is dependant upon some condition.
				if( isset( $setting['subsite_dependencies'] ) ) {
			
					// Let's see if any dependencies are unmet.
					$parse_deps = $this -> parse_dependencies( $setting['subsite_dependencies'] );
					if( is_wp_error( $parse_deps ) ) { continue; }

				}

				// We made it!  Add the field.
				$this -> add_settings_field( $section_id, $setting_id, $setting );

			}

		}

		$this -> register_setting();

	}

	/**
	 * Get the label for the setting.
	 * 
	 * @param  string $setting_id The ID of the setting.
	 * @param  array  $setting    The definition of the setting. 
	 * @return string             The label for the setting.        
	 */
	function get_setting_label( $setting_id, $setting ) {

		// The setting label.
		$out = $setting['label'];
		if( $setting['network'] && is_multisite() ) {
			$out = $this -> get_asterisk() . $out;
		}

		return $out;

	}

}