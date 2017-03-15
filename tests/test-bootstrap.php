<?php

/**
 * A class which extends the WP_UnitTestCase class with functionality common to all of the tests in our plugin.
 * 
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

abstract class Tests extends \WP_UnitTestCase {

	public function setUp() {

		$new_value = array(
			'mailchimp_account_setup' => array(
				'list_id' => 'b1af9779dd',
				'api_key' => 'eaba6e47cf5433f6c06198e32f1a9922-us10',
			)
		);
		
		global $bananas;
		$this -> settings = $bananas -> settings;
		$this -> settings -> network_values = $new_value;
		$this -> settings -> subsite_values = $new_value;

	}
	
}