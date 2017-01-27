<?php

/**
 * A class for calling MailChimp.
 *
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Call {

	function __construct( $args = array() ) {

		// Grab our plugin-wide helpers.
		global $bananas;
		$this -> meta     = $bananas -> meta;
		$this -> settings = $bananas -> settings;

		// Store the args that were passed in.
		$this -> set_args( $args );

		// Store our api key.
		$this -> set_api_key();

		// Store the datacenter.
		$this -> set_datacenter();

		// Store the api version.
		$this -> set_api_version();

		// Store the base url for the API.
		$this -> set_base();

		// Store the HTTP request method.
		$this -> set_method();	
		
		// Store the API endpoint.
		$this -> set_resource();

		// Store the params that we want to pass to MailChimp.
		$this -> set_params();

		// Store our authentication.
		$this -> set_auth();

		// Store the URL for our request.
		$this -> set_url();						

		// Store the args for our request.
		$this -> set_request_args();

		// Store a transient key for this request.
		$this -> set_transient_key();	

		// Store the response to our request.
		$this -> set_response();

	}

	/**
	 * Store the args that were passed in to our class.
	 */
	function set_args( $args ) {

		$this -> args = $args;

	}

	/**
	 * Get the API Key.
	 * 
	 * @return string The API key.
	 */
	function get_api_key() {

		return $this -> api_key;

	}

	/**
	 * Store the API key from the database.
	 */
	function set_api_key() {

		$this -> api_key = $this -> settings -> get_subsite_value( 'mailchimp_account_setup', 'api_key' );

	}

	/**
	 * Get the datacenter for our request.
	 * 
	 * @return string The datacenter for our request.
	 */
	function get_datacenter() {

		return $this -> datacenter;

	}

	/**
	 * Store the datacenter for our request.
	 */
	function set_datacenter() {

		$api_array          = explode( '-', $this -> get_api_key() );
		$this -> datacenter = array_pop( $api_array );

	}		

	/**
	 * Get the API version string for this call.
	 * 
	 * @return string The API version string for this call.
	 */
	function get_api_version() {

		return $this -> api_version;

	}

	/**
	 * Store the API version string.
	 */
	function set_api_version() {

		// let's assume we are always using version 3.
		$api_version = '3.0';

		// But we can opt into a different version.
		if( isset( $this -> args['api_version'] ) ) {
			$api_version = $this -> args['api_version'];
		}
		
		$this -> api_version = $api_version;

	}

	/**
	 * Get the base url for the MailChimp API.
	 * 
	 * @return string The base url for the MailChimp API.
	 */
	function get_base() {

		return $this -> base;

	}

	/**
	 * Set the base url for the MailChimp API.
	 */
	function set_base() {

		$base  = 'https://';
		$base .= $this -> get_datacenter();
		$base .= trailingslashit( '.api.mailchimp.com' );
		$base .= trailingslashit( $this -> get_api_version() );

		$this -> base = $base;

	}

	/**
	 * Get the HTTP request method.
	 * 
	 * @return string The HTTP request method.
	 */
	function get_method() {

		return $this -> method;

	}

	/**
	 * Store the HTTP request method.
	 */
	function set_method() {

		// We are almost always doing a GET.
		$method = 'GET';

		// But we can opt into any verb.
		if( isset( $this -> args['method'] ) ) {
			$method = $this -> args['method'];
		}
		
		$this -> method = $method;

	}

	/**
	 * Get the endpoint for this request.
	 * 
	 * @return string The endpoint for this request.
	 */
	function get_resource() {

		return $this -> resource;

	}

	/**
	 * Store the resource for this request.
	 */
	function set_resource() {

		$resource = '';
		if( isset( $this -> args['resource'] ) ) {
			$resource = $this -> args['resource'];
		}
		
		$this -> resource = $resource;

	}		

	/**
	 * Get the maximum number of results for this request.
	 * 
	 * @return integer The maximum number of results for this request.
	 */
	function get_max() {
		return 100;
	}

	/**
	 * Get the list of params we're passing to MC in our request.
	 * 
	 * @return array The list of params we're passing to MC in our request.
	 */
	function get_params() {

		return $this -> params;

	}

	/**
	 * Store the params that we're passing to MailChimp.
	 */
	function set_params() {

		$params = array();
		if( isset( $this -> args['params'] ) ) {
			$params = $this -> args['params'];
		}

		// If we passed in 'max' for our count, parse that into the actual integer maximum.
		$max = $this -> get_max();
		if( isset( $params['count'] ) ) {
			if( $params['count'] == 'max' ) {
				$params['count'] = $max;
			}
		}
		
		// Not sure why this sneaks in or why it would be a problem, but it's not needed and we've always stripped it out.
		unset( $params['id'] );

		$this -> params = $params;

	}

	/**
	 * Get the basic auth string.
	 * 
	 * @return string The basic auth string.
	 */
	function get_auth() {

		return $this -> auth;

	}

	/**
	 * Store the basic auth string.
	 */
	function set_auth() {

		// It's weird but yeah, this can literally be any string.
		$this -> auth = 'Basic ' . base64_encode( 'any_string' . ':' . $this -> get_api_key() );

	}				

	/**
	 * Get the url for our request.
	 * 
	 * @return string The url for our request.
	 */
	function get_url() {

		return $this -> url;

	}

	/**
	 * Store the url for our request.
	 */
	function set_url() {

		$out  = trailingslashit( $this -> get_base() );

		$out .= trailingslashit( $this -> get_resource() );

		if( $this -> method == 'GET' ) {

			foreach( $this -> params as $k => $v ) {

				$out = add_query_arg( array( $k => $v ), $out );

			}

		}


		$this -> url = $out;

	}

	/**
	 * Get the args for our HTTP request.
	 * 
	 * @return array An array of args for wp_remote_*.
	 */
	function get_request_args() {

		return $this -> request_args;

	}

	/**
	 * Store the array of args for wp_remote_*.
	 */
	function set_request_args() {

		$method = $this -> get_method();

		// Starting building args for wp_remote_request().
		$request_args = array(
			
			'method'      => $method,
		    'timeout'     => 60,
		    'redirection' => 60,
		    'headers'     => array(
				'Authorization' => $this -> get_auth(),
			),
		
		);

		// Maybe add data to our request.
		if( $method == 'POST' || $method == 'PATCH' || $method == 'PUT' ) {
			$request_args['body'] = json_encode( $this -> get_params() );
		}

		$this -> request_args = $request_args;

	}

	/**
	 * Grab the transient key for this request.
	 * 
	 * @return string The transient key for this request.
	 */
	function get_transient_key() {

		return $this -> transient_key;

	}

	/**
	 * Store the transient key for this request.
	 */
	function set_transient_key() {

		// Here are the ingredients for our key.
		$key = array(
			$this -> get_url(),
			$this -> get_request_args(),
			BANANAS_VERSION,
			$this -> get_auth(),
		);

		// Make the ingredients into a string.
		$key = json_encode( $key );

		// Compress the string so it's not super long.
		$key = md5( $key );

		// Prefix the string so we can find it in the database later.
		$key = BANANAS . '-' . $key;

		$this -> transient_key = $key;

	}

	/**
	 * Get the result of our api call.
	 * 
	 * @return mixed The result of our api call.
	 */
	function get_response() {

		return $this -> response;

	}

	/**
	 * Store the result of our API call.
	 */
	function set_response() {

		// If it's a GET request, we can look for a cached version.
		$method = $this -> get_method();
		if( $method == 'GET' ) {

			$transient = get_transient( $this -> get_transient_key() );
			if( ! empty( $transient ) ) {
				$this -> response = $transient;
				return;
			}

		}

		// Call MC.
		$result = wp_remote_request( $this -> url, $this -> get_request_args() );

		// Was the request unsuccessful?
		if( $this -> is_bad_response( $result ) ) {
			$this -> response = $this -> get_errors( $result );
			return;			
		}

		// Turn the result into an array.
		$result_json = json_decode( $result['body'], TRUE );

		// Store the result.
		$this -> response = $result_json;

		// If it's a GET request, we can store the result as a transient.
		if( $method == 'GET' ) {
			set_transient( $this -> get_transient_key(), $result_json, HOUR_IN_SECONDS );
		}
		
	}

	/**
	 * Determine if an API call went poorly, by looking at the response code.
	 * 
	 * @param  array $response An HTTP response.
	 * @return boolean         Returns TRUE for 40x and 50x, else FALSE.
	 */
	function is_bad_response( $response ) {

		// Was there an error?
		if( is_wp_error( $response ) ) {
			return TRUE;
		}

		if( ! isset( $response['response'] ) ) { return TRUE; }

		$code = $response['response']['code'];

		$first_two = substr( $code, 0, 2 );

		if( ( $first_two == 40 ) || ( $first_two == 50 ) ) {

			return TRUE;
		}

		return FALSE;

	}

	/**
	 * Get the errors for this request.
	 * 
	 * @param  array $response An HTTP response.
	 * @return mixed           Returns FALSE if no errors, else a WP_Error.
	 */
	function get_errors( $result ) {

		if( is_wp_error( $result ) ) {

			return $result;

		} else {

			$result_json = json_decode( $result['body'], TRUE );

			// This is how the MC API ships errors.
			$out = new \WP_Error( $result_json['title'], $result_json['detail'], $result_json['status'] );


		}

		return $out;

	}

}