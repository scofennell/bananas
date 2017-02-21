<?php

/**
 * A class for creating a post meta box.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Post_Meta_Box {

	function __construct() {

		// Grab our plugin-wide helpers.
		global $bananas;
		$this -> meta     = $bananas -> meta;
		$this -> settings = $bananas -> settings;
		$this -> config   = $bananas -> config;
		
		global $post_id;

		// Grab the list of meta fields.
		$this -> post_meta_fields = $bananas -> post_meta_fields;
		$this -> meta_fields      = $this    -> post_meta_fields -> get_fields();

		// Add our meta boxes.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		
		// Handle the saving of our meta boxes.
		add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );

	}

	/**
	 * Get the list of post types on which we're allowing our meta box.
	 * 
	 * @return array An array of post type slugs.
	 */
	function get_allowed_post_types() {

		return array( 'post' );

	}

	/**
	 * Determine if we are on the post page.
	 * 
	 * @return boolean Returns TRUE if we are on the post page, else FALSE.
	 */
	function is_current_page() {

		// If we're in network admin, bail.
		if( is_multisite() ) {
			if( is_network_admin() ) { return FALSE; }
		}

		// If we're not in admin, bail.
		if( ! is_admin() ) { return FALSE; }

		$current_screen = get_current_screen();

		// If we're not on the post screen, bail.
		$base      = $current_screen -> base;
		if( $base != 'post' ) { return FALSE; }

		// If the post type is not in our allowed array, bail.
		$post_type = $current_screen -> post_type;
		if( ! in_array( $post_type, $this -> get_allowed_post_types() ) ) { return FALSE; }
		
		return TRUE;

	}

	/**
	 * Add the meta boxes.
	 * 
	 * @param string $post_type The post type to which we're adding meta boxes.
	 */
	public function add_meta_boxes( $post_type ) {

		// If we're not on the correct page, bail.
		if( ! in_array( $post_type, $this -> get_allowed_post_types() ) ) { return FALSE; }
		if( ! $this -> is_current_page() ) { return FALSE; }

		$id            = BANANAS;
		$title         = $this -> meta -> get_label();
		$callback      = array( $this, 'the_content' );
		$screen        = $post_type;
		$context       = 'advanced';
		$priority      = 'high';
		$callback_args = array();

		add_meta_box(
			$id, 
			$title, 
			$callback, 
			$screen, 
			$context,
			$priority,
			$callback_args
		);
	
	}

	/**
	 * Echo the meta box content.
	 */
	public function the_content( $post ) {

		echo $this -> get_content( $post );

	}

	/**
	 * Get the meta box content.
	 * 
	 * @param  object $post A WP_POST.
	 * @return string       The meta box content.
	 */
	function get_content( $post ) {

		$out = '';

		$post_id = absint( $post -> ID );

		// Grab the meta values for this post.
		$values = array();
		if( ! empty( $post_id ) ) {
			$values = $this -> post_meta_fields -> get_values( $post_id );
		}

		// Grab the meta field inputs.
		$fields = $this -> meta_fields;

		// Loop through the array of sections and inputs.
		$count = count( $fields );
		$i     = 0;
		foreach( $fields as $section_id => $section ) {

			$i++;

			// The label for this section.
			$section_label = $section['label'];

			// The description for this section.
			$section_description = '';
			if( ! empty( $section['description'] ) ) {
				$section_description = '<p>' . $section['description'] . '</p>';
			}

			// Loop through the settings in this section.
			$settings     = $section['settings'];
			$settings_out = '';
			foreach( $settings as $setting_id => $setting ) {

				// Grab the value for this setting.
				$value = '';
				if( isset( $values[ $section_id ] ) ) {
					if( isset( $values[ $section_id ][ $setting_id ] ) ) {
						$value = $values[ $section_id ][ $setting_id ];

						if( is_scalar( $value ) ) {

							$value = esc_attr( $value );

						} elseif( is_array( $value ) ) {

							$value = array_map( 'esc_attr', $value );

						}
					}
				}

				// Grab the input for this setting.
				$settings_out .= $this -> get_field( $post_id, $value, $section_id, $setting_id, $setting );

			}

			// Wrap this section.
			$out .= "
				<fieldset>
					<legend><strong>$section_label</strong></legend>
					$section_description
					<div>$settings_out</div>
				</fieldset>
			";

			if( $i < $count ) {
				$out .= "<br><hr>";
			}

		}

		// Add an nonce field so we can check for it later.
		$nonce = wp_nonce_field( 'save', BANANAS . '-meta_box', TRUE, FALSE );

		$out = "
			$nonce
			$out
			$nonce
		";

		return $out;

	}

	/**
	 * Get an HTML input for a meta field.
	 * 
	 * @param  string $post_id    The post ID.
	 * @param  string $value      The database value for this input.
	 * @param  string $section_id The ID for the section that this setting is in.
	 * @param  string $setting_id The ID for this setting.
	 * @param  string $setting    The definition of this setting.
	 * @return string             An HTML input for a meta field.
	 */
	function get_field( $post_id, $value, $section_id, $setting_id, $setting ) {

		$out = '';

		// The label for this setting.
		$setting_label = $setting['label'];

		// The description for this setting.
		$setting_description = '';
		if( isset( $setting['description'] ) ) {
			$setting_description = '<p class="howto">' . $setting['description'] . '</p>';
		}
		
		// Namespace the ID for this setting.
		$id = BANANAS . '-' . $section_id . '-' . $setting_id;

		// Name the setting so it will be saved as an array.
		$name = BANANAS . '[' . $section_id . ']' .  '[' . $setting_id . ']';

		// Other various attributes.
		$attrs = '';
		if( isset( $setting['attrs'] ) ) {
			$attrs = $this -> fields -> get_attrs_from_array( $setting['attrs'] );
		}

		// Maybe get some options for this setting.
		if( isset( $setting['options_cb'] ) ) {

			// Get the options from this CB class.
			$options_class = __NAMESPACE__ . '\\' . $setting['options_cb'][0];

			// Instantiate the CB class, providing the current value of the setting.
			$options_obj = new $options_class( $value, $id, $name );

			// Grab the cb method.
			$options_method = $setting['options_cb'][1];

			// Call the cb method.
			$options = call_user_func( array( $options_obj, $options_method ) );
			if( is_wp_error( $options ) ) { return $options; }

		}

		// The type of input.
		$type = $setting['type'];
		
		// Deal with checkboxes.
		if( $type == 'checkbox' ) {

			// The value the box will have when checked.
			$checkbox_value = esc_attr( $setting['checkbox_value'] );

			// Should the box be checked?
			$checked = checked( $value, $checkbox_value, FALSE );

			// Wrap the checkbox.
			$out = "
				<div id='$id-wrap'>
					<input $attrs $checked class='' type='$type' id='$id' name='$name' value='$checkbox_value'>
					<label for='$id'>$setting_label</label>
					$setting_description
				</div>
			";

		} elseif( $type == 'checkbox_group' ) {

			$out = "
				<div id='$id-wrap'>
					$options
					$setting_description
				</div>
			";

		// All other input types.
		} else {

			// Wrap the input.
			$out = "

				<div id='$id-wrap'>
					<div>
						<label for='$id'>$setting_label</label>
					</div>
					<input $attrs class='regular-text' style='width: 100%;' type='text' id='$id' name='$name' value='$value'>
					$setting_description
				</div>

			";

		}

		return $out;

	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_post( $post_id, $post, $update ) {

		// Are we on the right page?
		if( ! $this -> is_current_page() ) { return $post_id; }

		// Was the meta box submit?
		if ( ! isset( $_POST[ BANANAS . '-meta_box' ] ) ) {
			return $post_id;
		}

		// Check the nonce.
		$nonce = $_POST[ BANANAS . '-meta_box' ];
		if ( ! wp_verify_nonce( $nonce, 'save' ) ) {
			return $post_id;
		}

		// Is this an autosave?
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Are we in ajax-land?
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return $post_id;
		}

		// Is this a revision?
		if( wp_is_post_revision( $post_id ) ) {
			return $post_id;
		}

		// Is this an allowed post type?
		$post_type = $post -> post_type;
		$allowed   = $this -> get_allowed_post_types();
		if( ! in_array( $post_type, $allowed ) ) {
			return $post_id;
		}

		// Is the user allowed to do this?
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Check if there was a multisite switch before saving.
		if ( is_multisite() && ms_is_switched() ) {
			return $post_id;
		}

		$old_values = $this -> post_meta_fields -> get_values( $post_id );

		// Grab and sanitize the data.
		$posted_data = $_POST[ BANANAS ];
		$posted_data = $this -> sanitize( $posted_data );
		
		// Finally!  Update the data.
		update_post_meta( $post_id, BANANAS, $posted_data );

		return $post_id;

	}

	/**
	 * Our sanitization function for out meta box.
	 * 
	 * @param  array  $dirty_sections The form values, dirty.
	 * @return array  The form values, clean.
	 */
	public function sanitize( $dirty = array() ) {

		// Grab the definition of all of our settings.
		$meta_fields = $this -> meta_fields;

		// Will hold cleaned values.
		$clean = array();

		// For each section of settings...
		foreach( $dirty as $section => $settings ) {

			// For each setting...
			foreach( $settings as $k => $v ) {

				// Let's call it good to just to sanitize text field.
				if( is_scalar( $v ) ) {
			
					$v = sanitize_text_field( $v );
			
				} elseif( is_array( $v ) ) {
			
					$v = array_map( 'sanitize_text_field', $v );
			
				}

				// Nice!  Pass the cleaned value into the array.
				$clean[ $section ][ $k ] = $v;

			}
	
		}

		return $clean;

	}

}