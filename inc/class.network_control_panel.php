<?php

/**
 * A class for creating a dashboard widget.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Network_Control_Panel extends Control_Panel {

	function __construct() {

		parent::__construct();

		// Get the network settings values.
		$this -> values = $this -> settings -> get_network_values();

		// Add our options page to network admin.
		add_action( 'network_admin_menu', array( $this, 'add_options_page' ) );

		// Register our options sections.
		add_action( 'admin_init', array( $this, 'handle' ), 1 );
		add_action( 'admin_init', array( $this, 'register' ), 100 );

		// Register our admin notices.
		add_action( 'network_admin_notices', array( $this, 'admin_notices' ) );

	}

	/**
	 * Determine if we are on the settings page.
	 * 
	 * @return boolean Returns TRUE if we are on the settings page, else FALSE.
	 */
	function is_current_page() {

		if( ! is_network_admin() ) { return FALSE; }

		global $pagenow;

		if( $pagenow != $this -> get_parent_slug() ) { return FALSE; }

		if( ! isset( $_GET['page'] ) ) { return FALSE; }

		if( $_GET['page'] != BANANAS ) { return FALSE; }

		return TRUE;

	}

	/**
	 * Get the url for the form handler.
	 * 
	 * @return string The url for the form handler.
	 */
	function get_handler_url() {

		return $this -> get_network_admin_url();

	}

	/**
	 * Get a nonce input for our form.
	 * 
	 * @return string A nonce input.
	 */
	function get_nonce() {
	
		$action     = 'update_network_settings';
		$nonce_name = BANANAS  . '-nonce';
		$referrer   = TRUE;
		$echo       = FALSE;

		return wp_nonce_field(
			$action,
			$nonce_name,
			$referrer,
			$echo
		);
	
	}

	/**
	 * Handle our form submission.
	 * 
	 * @return mixed Returns FALSE if no form was submit, else the result of a settings update.
	 */
	public function handle() {

		// Was the form submit?
		if( ! isset( $_POST['option_page'] ) ) { return FALSE; }
		if( ! isset( $_POST['submit'] ) ) { return FALSE; }
		if( ! isset( $_POST[ BANANAS ] ) ) { return FALSE; }

		// Are we on the settings page?
		if( ! $this -> is_current_page() ) { return FALSE; }

		// Check the nonce.
		if ( ! isset($_POST[ BANANAS . '-nonce' ] ) ) { return FALSE; }
		if ( empty($_POST[ BANANAS . '-nonce' ] ) ) { return FALSE; }
		if( ! wp_verify_nonce( $_POST[ BANANAS . '-nonce' ], 'update_network_settings' ) ) { return FALSE; }

		// We made it!  Update the setting.
		$new_value = $this -> sanitize( $_POST[ BANANAS ] );

		// Update the settings in the DB.
		$update_network_values = $this -> settings -> update_network_values( $new_value );

		// Update the settings for this page load.
		$this -> values = $new_value;

		return $new_value;

	}

	/**
	 * Loop through our settings and register them.
	 */
	public function register() {

		if( ! $this -> is_current_page() ) { return FALSE; }

		// Grab our plugin settings definition.
		$network_settings = $this -> settings -> get_network_settings();

		// For each section of settings...
		foreach( $network_settings as $section_id => $section ) {

			$this -> add_settings_section( $section, $section_id );

			// For each setting in this section...
			foreach( $section['settings'] as $setting_id => $setting ) {

				if( isset( $setting['network_dependencies'] ) ) {

					$parse_deps = $this -> parse_dependencies( $setting['network_dependencies'] );

					if( is_wp_error( $parse_deps ) ) { continue; }

				}

				$this -> add_settings_field( $section_id, $setting_id, $setting );

			}

		}

		$this -> register_setting();

	}

	/**
	 * Get the url for our network settings page.
	 * 
	 * @return string The url for our network settings page.
	 */
	function get_network_admin_url() {

		return network_admin_url( $this -> get_parent_slug() . '?page=' . BANANAS );

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
		if( $setting['subsite'] ) {
			$out = $this -> get_asterisk() . $out;
		}

		return $out;

	}

}