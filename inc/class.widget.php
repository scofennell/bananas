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

		$this -> lists = new Lists;

		$class = sanitize_html_class( __CLASS__ );

		add_action( 'widgets_init', array( $this, 'register' ) );

		$widget_ops = array( 
			'classname'   => $class,
			'description' => esc_html__( 'A MailChimp widget.', 'bananas' ),
		);
		parent::__construct( 'my_widget', 'My Widget', $widget_ops );

	}

	function register() {

		if( ! $this -> is_setup() ) { return FALSE; }

		register_widget( __CLASS__ );
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

		if( ! empty( $instance['labels']['title'] ) ) {

			$before_title = $args['before_title'];
			$after_title  = $args['after_title'];

			$title = apply_filters( 'widget_title', $instance['labels']['title'] );

			$title = "$before_title $title $after_title";

		}

		$out = "
			$before_widget
				$title
				$content
			$after_widget
		";

		return $out;

	}

	/**
	 * Outputs the options form on admin.
	 *
	 * @param array $instance The widget options.
	 */
	public function form( $instance ) {
	
		echo $this -> get_form( $instance );

	}

	function get_form( $instance ) {

		$out = '';

		$sections = $this -> get_settings();
		foreach( $sections as $section_id => $section ) {

			$section_out = '';

			$section_label       = $section['label'];
			$section_description = $section['description'];
			$settings            = $section['settings'];
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

	function get_field( $section_id, $setting_id, $setting, $value ) {

		$label = $setting['label'];

		$id = BANANAS . '-' . $section_id . '-' . $setting_id;

		$name = BANANAS . '[' . $section_id . ']' . '[' . $setting_id . ']';

		$out = "
			<p>
				<label for='$id'>
					$label
				</label> 
				<input class='widefat' id='$id' name='$name' type='text' value='$value'>
			</p>
		";

		return $out;	

	}

	/**
	 * Processing widget options on save.
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 */
	public function update( $new_instance, $old_instance ) {
	
		$instance = array();

		$sections = $this -> get_settings();
		foreach( $sections as $section_id => $section ) {

			$settings = $section['settings'];
			foreach( $settings as $setting_id => $setting ) {

			}

		}

		return $instance;

	}

	function get_settings() {

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

		);

		return $settings;

	}

}