<?php

/**
 * A class for creating a dashboard widget.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Dashboard_Widget {

	function __construct() {

		// Grab our plugin-wide helpers.
		global $bananas;
		$this -> meta = $bananas -> meta;

		$this -> lists = new Lists;

		add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup' ) );

	}

	/**
	 * Are we ready to do this widget?
	 * 
	 * @return boolean Returns TRUE if we are ready to do this widget, else WP_Error.
	 */
	function is_setup() {

		return $this -> meta -> has_api_key();

	}

	/**
	 * Add our widget.
	 * 
	 * @return mixed Returns FALSE if we are not ready to do this widget.
	 */
	function wp_dashboard_setup() {

		if( is_wp_error( $this -> is_setup() ) ) { return FALSE; }

		$widget_id        = BANANAS;
		$widget_name      = $this -> get_the_title();
		$callback         = array( $this, 'cb' );
		$control_callback = FALSE;
		$callback_args    = FALSE;

		return wp_add_dashboard_widget(		
            $widget_id,
            $widget_name,
            $callback,
            $control_callback,
            $callback_args
        );	

	}

	function get_the_title() {

		$root = new Root;
		$total_subscribers = '<code>' . $root -> get_total_subscribers() . '</code>';
		
		$total_lists = '<code>' . $this -> lists -> get_total_items() . '</code>';
		

		$out = sprintf( esc_html__( 'Total Subscribers: %s from %s Lists', 'bananas' ), $total_subscribers, $total_lists );

		return $out;

	}

	/**
	 * The callback function for echoing the widget.
	 */
	function cb() {

		echo $this -> get_content();

	}

	/**
	 * Build the widget content.
	 * 
	 * @return string Returns the widget content.
	 */
	function get_content() {

		// Make a graph of our MailChimp list stats.
		$graph = $this -> lists -> get_graph();

		return $graph;

	}

}