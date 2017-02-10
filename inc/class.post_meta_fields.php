<?php

/**
 * A class for defining our post meta fields.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Post_Meta_Fields {

	function __construct() {

		// Define our settings.
		$this -> set_fields();

	}

	/**
	 * Get the array that defines our plugin post meta fields.
	 * 
	 * @return array Our plugin post meta fields.
	 */
	function get_fields() {

		return $this -> fields;

	}

	/**
	 * Store our meta fields definitions.
	 */
	function set_fields() {

		$out = array(

			// A section.
			'a_section' => array(

				// The label for this section.
				'label' => esc_html__( 'A Section', 'bananas' ),

				// The settings for this section.
				'settings' => array(

					// A setting.
					'a_setting' => array(
						'type'           => 'checkbox',
						'label'          => esc_html__( 'Check a box?', 'bananas' ),
						'description'    => esc_html__( 'When this box is checked, you have checked this box.', 'bananas' ),
						'checkbox_value' => 1,
					),

				),

			),	

		);

		$this -> fields = $out;

	}

	/**
	 * Get the values for our meta box.
	 * 
	 * @param  integer $post_id The ID of the post.
	 * @return array            A multidimensional array of values by setting and section.
	 */
	function get_values( $post_id ) {

		$out = get_post_meta( $post_id, BANANAS, TRUE );

		return $out;

	}

	/**
	 * Get the value for a meta field.
	 * 
	 * @param  integer $post_id    The post ID.
	 * @param  string  $section_id The section ID.
	 * @param  string  $setting_id The setting ID.
	 * @return mixed               The post meta value from the DB.
	 */ 
	function get_value( $post_id, $section_id, $setting_id ) {

		$values = $this -> get_values( $post_id );

		// If this section has no values, bail.
		if( ! isset( $values[ $section_id ] ) ) { return FALSE; }

		// If this setting has no value, bail.
		if( ! isset( $values[ $section_id ][ $setting_id ] ) ) { return FALSE; }

		return $values[ $section_id ][ $setting_id ];

	}

}