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
						'type'        => 'text',
						'label'       => esc_html__( 'MailChimp API Key', 'bananas' ),
						'description' => esc_html__( 'Example: 2t3g46fy4hf75k98uytr5432wer3456u-us3', 'bananas' ),
						'attrs'       => array(
							'required'    => 'required',
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
	 * Store the values of our subsite settings.
	 */
	function set_subsite_values() {

		$this -> subsite_values = get_option( BANANAS );

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

}