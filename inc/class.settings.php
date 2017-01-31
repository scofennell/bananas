<?php

/**
 * A class for defining our plugin settings.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Settings {

	function __construct() {

		// Define our settings.
		$this -> set_settings();

	}

	/**
	 * Get the array that defines our plugin settings.
	 * 
	 * @return array Our plugin settings.
	 */
	function get_settings() {

		return $this -> settings;

	}

	/**
	 * Store our plugin settigns definitions.
	 */
	function set_settings() {

		$out = array(

			// A section.
			'mailchimp_account_setup' => array(

				// The label for this section.
				'label' => esc_html( 'MailChimp Account Setup', 'bananas' ),

				// For subsites?
				'subsite' => TRUE,

				// For multisite?
				'network' => TRUE,

				// The settings for this section.
				'settings' => array(

					// A setting.
					'api_key' => array(
						'subsite'     => TRUE,
						'network'     => TRUE,
						'type'        => 'text',
						'label'       => esc_html__( 'MailChimp API Key', 'bananas' ),
						'description' => esc_html__( 'Example: 2t3g46fy4hf75k98uytr5432wer3456u-us3', 'bananas' ),
						'attrs'       => array(
							'placeholder' => esc_attr__( 'Your MailChimp API Key', 'bananas' ),
							'pattern'     => '.{30,40}',
							'title'       => esc_attr__( 'Should be about 36 characters and include your datacenter', 'bananas' ),
						),
					),

				),

			),

		);

		$this -> settings = $out;

	}

	/**
	 * Get the values of our subsite settings.
	 * 
	 * @return array The values of our subsite settings.
	 */
	function get_subsite_values() {

		if( ! isset( $this -> subsite_values ) ) {
			$this -> set_subsite_values();
		}

		return $this -> subsite_values;

	}

	/**
	 * Get the values of our network settings.
	 * 
	 * @return array The values of our network settings.
	 */
	function get_network_values() {

		if( ! isset( $this -> network_values ) ) {
			$this -> set_network_values();
		}

		return $this -> network_values;

	}

	/**
	 * Store the values of our subsite settings.
	 */
	function set_subsite_values() {

		$settings = $this -> get_settings();

		$subsite_values = get_option( BANANAS );

		$out = $subsite_values;

		if( is_multisite() ) {

			$network_values = $this -> get_network_values();

			foreach( $settings as $section_id => $section ) {

				if( ! $section['network'] ) { continue; }
				if( ! $section['subsite'] ) { continue; }

				foreach( $section['settings'] as $setting_id => $setting ) {

					if( ! $setting['network'] ) { continue; }
					if( ! $setting['subsite'] ) { continue; }

					$subsite_val = FALSE;
					if( isset( $subsite_values[ $section_id ][ $setting_id ] ) ) {
						$subsite_val = $subsite_values[ $section_id ][ $setting_id ];
					}

					$network_val = FALSE;
					if( isset( $network_values[ $section_id ][ $setting_id ] ) ) {
						$network_val = $network_values[ $section_id ][ $setting_id ];
					}					

					if( is_null( $subsite_val ) || ! $subsite_val ) {
						$out[ $section_id ][ $setting_id ] = $network_val;
					}	

				}

			}

		}

		$this -> subsite_values = $out;

	}

	/**
	 * Store the values of our subsite settings.
	 */
	function set_network_values() {

		$this -> network_values = get_site_option( BANANAS );

	}	

	/**
	 * Get the definition of our subsite settings.
	 * 
	 * @return array The definition of our subsite settings.
	 */
	function get_subsite_settings() {

		// Start with all settings.
		$settings = $this -> get_settings();

		// For each setting...
		foreach( $settings as $section_id => $section ) {

			// If it's not a subsite setting, remove it.
			if( ! $section['subsite'] ) {
				unset( $settings[ $section_id] );
			}

		}

		return $settings;

	}

	/**
	 * Get the value of a given subsite setting.
	 * 
	 * @param  string $section_id The section.
	 * @param  string $setting_id The setting.
	 * @return mixed              The setting value.
	 */
	function get_subsite_value( $section_id, $setting_id ) {

		$values = $this -> get_subsite_values();

		return $values[ $section_id ][ $setting_id ];

	}

	/**
	 * Get the value of a given network setting.
	 * 
	 * @param  string $section_id The section.
	 * @param  string $setting_id The setting.
	 * @return mixed              The setting value.
	 */
	function get_network_value( $section_id, $setting_id ) {

		$values = $this -> get_network_values();

		return $values[ $section_id ][ $setting_id ];

	}

	function update_network_values( $new_values ) {

		$old_values = $this -> get_network_values();	

		if( ! is_array( $old_values ) ) {
		
			$out = $new_values;
		
		} else {
		
			$out = $old_values;

			foreach( $old_values as $old_section_id => $old_settings ) {
				
				if( ! isset( $new_values[ $old_section_id ] ) ) { continue; }

				foreach( $old_settings as $old_setting_id => $old_setting_value ) {

					if( ! isset( $new_values[ $old_section_id ][ $old_setting_id ] ) ) {

						$new_values[ $old_section_id ][ $old_setting_id ] = FALSE;

					}

					$out[ $old_section_id ][ $old_setting_id ] = $new_values[ $old_section_id ][ $old_setting_id ];



				}
		
			}
		
		}
		
		$this -> network_values = $out;

		return update_site_option( BANANAS, $out );

	}

}