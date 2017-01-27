<?php

/**
 * A class for defining our plugin settings.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Meta {

	function get_label() {

		return esc_html__( 'Bananas', 'bananas' );

	}

}