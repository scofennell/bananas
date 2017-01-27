<?php

/**
 * A class for creating a dashboard widget.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Subsite_Control_Panel {

	function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		global $bananas;
		$this -> meta     = $bananas -> meta;
		$this -> settings = $bananas -> settings;

	}

	function admin_menu() {

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

	function the_page() {

		echo $this -> get_page();

	}

	function get_page() {

		$title = '<h1>' . $this -> meta -> get_label() . '</h1>';
		$content = $this -> get_fields();

		$out = "
			<div class='wrap'>
				$title
				$content
			</div>
		";

		return $out;

	}

	function get_fields() {

		$settings = $this -> settings;

		wp_die( var_dump( $settings -> get_settings() ) );

	}

}