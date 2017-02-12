<?php

/**
 * A class for creating a widget.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Widget extends \WP_Widget {

	function __construct() {

		// Grab our plugin-wide helpers.
		global $bananas;
		$this -> config = $bananas -> config;

		// Register the widget.
		add_action( 'widgets_init', array( $this, 'register' ) );

		// A slug name for our widget.
		$class = sanitize_html_class( __CLASS__ );

		// The title of the widget in wp-admin.
		$widget_title = esc_html__( 'Widget Title', 'bananas' );

		// Options for the parent constructor.
		$widget_ops = array( 
			'classname'   => $class,
			'description' => esc_html__( 'A MailChimp widget.', 'bananas' ),
		);

		// Call the parent constructor.
		parent::__construct(
			$class,
			$widget_title,
			$widget_ops
		);

	}

	/**
	 * Register our widget.
	 * 
	 * @return mixed returns FALSE if not setup or the result of register_widget();
	 */
	function register() {

		if( ! $this -> is_setup() ) { return FALSE; }

		return register_widget( __CLASS__ );

	}

	/**
	 * Are we ready to do this widget?
	 * 
	 * @return mixed Returns TRUE if we are ready to do this widget, else WP_Error.
	 */
	function is_setup() {

		return $this -> config -> has_api_key();

	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array $args     The theme settings for widget output.
	 * @param array $instance The widget options.
	 */
	public function widget( $args, $instance ) {
		
		echo $this -> get_widget( $args, $instance );

	}

	public function get_widget( $args, $instance ) {

		$content = 'hello world';

		if( empty( $content ) ) { return FALSE; }

		$before_widget = $args['before_widget'];
		$after_widget  = $args['after_widget'];

		$title = $this -> get_front_end_title( $args, $instance );

		$out = "
			$before_widget
				$title
				$content
			$after_widget
		";

		return $out;

	}

	/**
	 * Get the widget title for display on the front end.
	 * 
	 * @param  array $args     The theme values for widget display.
	 * @param  array $instance The admin-provided values for this widget instance.
	 * @return string          The widget title, wrapped and filtered.
	 */
	function get_front_end_title( $args, $instance ) {

		if( empty( $instance['labels']['title'] ) ) { return FALSE; }

		$before_title = $args['before_title'];
		$after_title  = $args['after_title'];

		$title = apply_filters( 'widget_title', $instance['labels']['title'] );

		$out = "$before_title $title $after_title";

		return $out;

	}

	/**
	 * Processing widget options on save.
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 */
	public function update( $new_instance, $old_instance ) {
	
		// Upon saving settings, we dump our transients.
		$cache  = new Cache;
		$cache -> delete();

		return $this -> sanitize( $new_instance );

	}

	/**
	 * Outputs the options form on admin.
	 *
	 * @param array $instance The widget options.
	 */
	public function form( $instance ) {
	
		echo $this -> get_form( $instance );

	}

	/**
	 * Outputs the options form on admin.
	 *
	 * @param  array $instance The widget options.
	 * @return string          The widget form.
	 */
	function get_form( $instance ) {

		$out = '';

		// Loop through the settings sections.
		$sections = $this -> get_settings_sections();
		foreach( $sections as $section_id => $section ) {

			// Will hold the fieldset for this section.
			$section_out = '';

			// Some header text for this section.
			$section_label       = $section['label'];
			$section_description = $section['description'];
			
			// Loop through the settings for this section.
			$settings = $section['settings'];
			foreach( $settings as $setting_id => $setting ) {

				$value = '';
				if( isset( $instance[ $section_id ][ $setting_id ] ) ) {
					$value = $instance[ $section_id ][ $setting_id ];
				}

				$section_out .= $this -> get_field( $section_id, $setting_id, $setting, $value );

			}

			$out .= "
				<fieldset>
					<h3>$section_label</h3>
					<p>$section_description</p>
					$section_out
				</fieldset>
			";

		}

		return $out;

	}

	/**
	 * Get the input for a widget setting.
	 *
	 * @param  string $section_id The ID for our section of settings.
	 * @param  string $setting_id The ID for our setting.
	 * @param  string $setting    The array that defines our setting.
	 * @param  mixed  $value      The current value of this setting.
	 * @return string             The form input for this setting.
	 */
	function get_field( $section_id, $setting_id, $setting, $value ) {

		$label = $setting['label'];
		$type  = $setting['type'];
		$value = esc_attr( $value );

		$description = $setting['description'];
		if( ! empty( $description ) ) {
			$description = "<p><em>$description</em></p>";
		}
	
		//widget-bananas-3-labels-title
		$id = 'widget-' . $this -> id . '-' . $section_id . '-' . $setting_id;

		//widget-bananas[3][labels][title]
		$name = 'widget-' . $this -> id_base . '[' . $this -> number . ']' . '[' . $section_id . ']' . '[' . $setting_id . ']';

		$out = "
			<div>
				<label for='$id'>
					$label
				</label> 
				<input class='widefat' id='$id' name='$name' type='$type' value='$value'>
				$description
			</div>
		";

		return $out;	

	}

	/**
	 * Get the array of sections and settings for this widget.
	 * 
	 * @return array The sections of settings for this widget.
	 */
	function get_settings_sections() {

		$settings = array(

			'labels' => array(

				'label'       => esc_html__( 'A Section', 'bananas' ),
				'description' => esc_html__( 'A section.', 'bananas' ),

				// The settings for this section.
				'settings' => array(

					// A setting.
					'title' => array(
						'type'        => 'text',
						'label'       => esc_html__( 'Title', 'bananas' ),
						'description' => esc_html__( 'The title.', 'bananas' ),
						'attrs'       => array(
							'placeholder' => esc_attr__( 'The title', 'bananas' ),
						),
					),

				),

			),

			'another_section' => array(

				'label'       => esc_html__( 'Another Section', 'bananas' ),
				'description' => esc_html__( 'Another section.', 'bananas' ),

				// The settings for this section.
				'settings' => array(

					// A setting.
					'url' => array(
						'type'        => 'url',
						'label'       => esc_html__( 'A url', 'bananas' ),
						'description' => esc_html__( 'The url you might enter.', 'bananas' ),
						'attrs'       => array(
							'placeholder' => esc_attr__( 'The url', 'bananas' ),
						),
					),

				),

			),			

		);

		return $settings;

	}

	/**
	 * Our sanitize_callback for register_setting().
	 * 
	 * @param  array  $dirty The form values, dirty.
	 * @return array  The form values, clean.
	 */
	public function sanitize( $dirty = array() ) {

		// Will hold cleaned values.
		$clean = array();

		// For each section of settings...
		foreach( $dirty as $section => $settings ) {

			// For each setting...
			foreach( $settings as $k => $v ) {

				// Let's call it good ot just to sanitize text field.
				$v = sanitize_text_field( $v );

				// Nice!  Pass the cleaned value into the array.
				$clean[ $section ][ $k ] = $v;

			}
	
		}

		return $clean;

	}

}