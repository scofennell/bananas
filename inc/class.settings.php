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

		$this -> set_settings();

	}

	function get_settings() {

		return $this -> settings;

	}

	function set_settings() {

		$out = array(

			'setup' => array(

				'settings' => array(

					'api_key' => array(
						'type'  => 'text',
						'label' => esc_html__( 'API Key', 'bananas' ),
					),

					'single_site' => TRUE,
					'network'     => TRUE,

				),

			),

		);

		$this -> settings = $out;

	}

}