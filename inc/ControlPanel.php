<?php

/**
 * A class for creating a dashboard widget.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

abstract class ControlPanel {

	use Form;

	function __construct() {

		// Grab our plugin-wide helpers.
		global $bananas;
		$this -> meta     = $bananas -> meta;
		$this -> config   = $bananas -> config;
		$this -> settings = $bananas -> settings;

	}

	/**
	 * Get an asterisk in order to explain that this is an inheritable setting.
	 * 
	 * @return string An asterisk in order to explain that this is an inheritable setting.
	 */
	function get_asterisk() {

		$class = sanitize_html_class( __CLASS__ . '-' . __FUNCTION__ );

		$title = esc_attr__( 'Subsites will inherit this setting if they leave it empty.', 'bananas' );

		$out = "<span class='$class' title=''>*</span>";

		return $out;

	}

	/**
	 * Echo the description for a section.
	 * 
	 * @param array $section The weird callback info provided by core.
	 */
	function the_section_description( $section ) {

		echo $this -> get_section_description( $section );

	}

	/**
	 * Get the description for a section.
	 * 
	 * @param  array $section The weird callback info provided by core.
	 * @return string         The description of a section.
	 */
	function get_section_description( $section ) {

		$out = '';

		$section_id       = $section['id'];
		$settings_section = $this -> settings -> get_section( $section_id );

		if( isset( $settings_section['description'] ) ) {
			$out = '<p>' . wp_kses_post( $settings_section['description'] ) . '</p>';
		}

		return $out;

	}


	/**
	 * Get the slug for the parent page of our settings page.
	 * 
	 * @return string The slug for the parent page of our settings page.
	 */
	function get_parent_slug() {

		return 'admin.php';

	}

	/**
	 * Add our plugin settings page.
	 */
	function add_options_page() {

		$page_title = $this -> meta -> get_label();
		$menu_title = $this -> meta -> get_label();
		$capability = 'update_core';
		$menu_slug  = BANANAS;
		$function   = array( $this, 'the_page' );
		$icon_url   = 'dashicons-email';
		$position   = 100;

		$out = add_menu_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$icon_url,
			$position
		);

		return $out;

	}

	/**
	 * Callback function for add_options_page.
	 */
	function the_page() {

		echo $this -> get_page();

	}

	/**
	 * Get the content for the options page.
	 * 
	 * @return string The content for our options page.
	 */
	function get_page() {

		$title = '<h1>' . $this -> meta -> get_label() . '</h1>';
		$form = $this -> get_form();

		$out = "
			<div class='wrap'>
				$title
				$form
			</div>
		";

		return $out;

	}

	/**
	 * Get an HTML input of the submit type.
	 * 
	 * @return string An HTML input of the submit type.
	 */
	public function get_submit_button() {

		// Args for get_submit_button().
		$text             = esc_html__( 'Submit', 'bananas' );
		$type             = 'primary';
		$name             = 'submit';
		$wrap             = TRUE;
		$other_attributes = array();

		// Grab the submit button.
		$out = get_submit_button(
			$text,
			$type,
			$name,
			$wrap,
			$other_attributes
		);

		return $out;

	}

	/**
	 * Output an HTML form field.
	 * 
	 * @param  array $args An array of args from add_settings_field(). Contains settings section and setting.
	 */
	public function the_field( $args = array() ) {
	
		$out = $this -> get_field( $args );

		if( is_wp_error( $out ) ) {
			echo $out -> get_error_message();
		} else {
			echo $out;
		}

	}

	/**
	 * Get an HTML form field.
	 * 
	 * @param  array  $args An array of args from add_settings_field(). Contains settings section and setting.
	 * @return string An HTML form field.
	 */
	public function get_field( $args = array() ) {

		$section_id = $args['section_id'];
		$setting_id = $args['setting_id'];		
		$setting    = $args['setting'];

		$class = sanitize_html_class( __CLASS__ . '-' . __FUNCTION__ );

		// The input type.
		$type = $setting['type'];

		// The ID for the input, expected by the <label for=''> that get's printed via do_settings_sections().
		$id = BANANAS . "-$section_id-$setting_id";

		// The description for the input.
		$description = "<p class='description'>" . $setting['description'] . '</p>';

		// The name of this setting.
		$name = BANANAS . '[' . $section_id . ']' . '[' . $setting_id . ']';			
		
		// The value for this setting.
		$value = '';
		if( isset( $this -> values[ $section_id ][ $setting_id ] ) ) {
			
			$value = $this -> values[ $section_id ][ $setting_id ];

			if( is_scalar( $value ) ) {
			
				$value = esc_attr( $value );
			
			} elseif( is_array( $value ) ) {
			
				$value = array_map( 'esc_attr', $value );
			
			}

		}

		// Other various attributes.
		$attrs = $this -> get_attrs_from_array( $setting['attrs'] );

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

		// Handle selects.
		if( $type == 'select' ) {

			$input = "<select $attrs class='regular-text' id='$id' name='$name'>$options</select>";

		} elseif( $type == 'checkbox_group' ) {

			$input = "<div class='$class-$type'>$options</div>";

		// Handle any other kind of input.
		} else {

			$input = "<input $attrs class='regular-text' type='$type' id='$id' name='$name' value='$value'>";

		}

		$out = "
			$input
			$description
		";

		return $out;

	}

	/**
	 * Get the form fields.
	 * 
	 * @return string The form fields.
	 */
	function get_form_fields() {

		// Start an output buffer since some of these functions always echo.
		ob_start();

		// Dump the nonce and some other hidden form stuff into the OB.
		settings_fields( BANANAS );

		// Dump the form inputs into the OB.
		do_settings_sections( BANANAS );

		// Grab the stuff from the OB, clean the OB.
		$out = ob_get_clean();

		return $out;

	}

	/**
	 * Output our admin notices.
	 */
	function admin_notices() {

		if( ! $this -> is_current_page() ) { return FALSE; }

		echo $this -> get_admin_notices();

	}

	/**
	 * Determine if our plugin page is ready to go.
	 * 
	 * @return mixed Returns TRUE if our plugin is ready, else WP_Error.
	 */
	function is_setup() {

		return $this -> config -> has_api_key();

	}

	/**
	 * Get the admin notices for our settings page.
	 * 
	 * @return string The admin notices for our settings page.
	 */
	function get_admin_notices() {

		$is_setup = $this -> is_setup();

		// If the plugin is all set up, say so.
		if( ! is_wp_error( $is_setup ) ) {

			$message = esc_html__( 'Nice!  Your API Key is valid.', 'bananas' );
			$type = 'success';

		// Else, issue a warning.
		} else {

			$message = $is_setup -> get_error_message();
			$type = 'warning';

		}

		$out = "
			<div class='notice-$type notice is-dismissible'>
				<p>$message</p>
			</div>
		";

		return $out;

	}

	/**
	 * Get the settings form.
	 * 
	 * @return string The settings form.
	 */
	function get_form() {

		// The form fields.
		$form_fields = $this -> get_form_fields();

		// Grab a submit button.
		$submit = $this -> get_submit_button();

		$url = $this -> get_handler_url();

		$nonce = '';
		if( method_exists( $this, 'get_nonce' ) ) {
			$nonce = $this -> get_nonce();
		}

		// Nice!  Time to build the page!
		$out = "
			<form method='POST' action='$url'>
				$nonce
				$form_fields
				$submit
			</form>
		";

		return $out;

	}	

	/**
	 * Add a settings field.
	 * 
	 * @param string $section_id The section ID.
	 * @param string $setting_id The setting ID.
	 * @param array  $setting    The setting definition.
	 */
	function add_settings_field( $section_id, $setting_id, $setting ) {

		// Grab the label for the setting.
		$label = $this -> get_setting_label( $setting_id, $setting );

		// The cb to draw the input for this setting.
		$callback = array( $this, 'the_field' );

		$args = array(
			'section_id' => $section_id,
			'setting_id' => $setting_id,
			'setting'    => $setting,
		);

		// Add the settings field.
		add_settings_field(
			
			$setting_id,
			$label,
			
			// Echo the form input.
			$callback,
			
			// Matches the value in do_settings_sections().
			BANANAS,
			
			// Matches the first arg in add_settings_section().
			$section_id,

			// Passed to $callback.
			$args
		
		);

	}

	/**
	 * Register our plugin settings array.
	 * 
	 * @return mixed The result of register_setting();
	 */
	function register_setting() {

		// Designate a sanitization function for our settings.
		$sanitize_callback = array( $this, 'sanitize' );

		// Register the settings!
		return register_setting(

			// Matches the value in settings_fields().
			BANANAS,

			// The name for our option in the DB.
			BANANAS,
			
			// The callback function for sanitizing values.
			$sanitize_callback
		
		);

	}

	/**
	 * Add a settings section.
	 * 
	 * @param array  $section    The definition of a section.
	 * @param string $section_id A setting ID.
	 */
	function add_settings_section( $section, $section_id ) {

		// Grab the label.
		$section_label = $section['label'];
		
		// Add the section.
		add_settings_section(
			
			// The ID for this settings section.
			$section_id,

			// The label for this settings section.
			$section_label,

			// Could provide a cb function here to output some help text, but don't need to.
			array( $this, 'the_section_description' ),

			// Needs to match the first arg in register_setting().
			BANANAS

		);

	}

}