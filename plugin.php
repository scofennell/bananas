<?php

/**
 * A MailChimp analytics widget for WordPress.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 * 
 * Plugin Name: Bananas
 * Plugin URI: http://www.scottfennell.com
 * Description: A MailChimp analytics widget for WordPress.
 * Author: Scott Fennell
 * Version: 0.1
 * Author URI: http://www.scottfennell.com
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
	
// Peace out if you're trying to access this up front.
if( ! defined( 'ABSPATH' ) ) { exit; }

// Watch out for plugin naming collisions.
if( defined( 'BANANAS' ) ) { exit; }
if( isset( $bananas ) ) { exit; }

// Our master plugin object, which will own instances of various classes in our plugin.
$bananas = FALSE;

// A slug for our plugin.
define( 'BANANAS', 'bananas' );

// Establish a value for plugin version to bust file caches.
define( 'BANANAS_VERSION', '0.1' );

// A constant to define the paths to our plugin folders.
define( 'BANANAS_FILE', __FILE__ );
define( 'BANANAS_PATH', trailingslashit( plugin_dir_path( BANANAS_FILE ) ) );

// A constant to define the urls to our plugin folders.
define( 'BANANAS_URL', trailingslashit( plugin_dir_url( BANANAS_FILE ) ) );

// A class for loading our plugin files.
require_once( BANANAS_PATH . 'inc/class.bootstrap.php' );