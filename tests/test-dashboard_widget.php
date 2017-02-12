<?php

/**
 * @package WordPress
 * @subpackage Bananas
 * @since Bananas 0.1
 */

namespace Bananas;

class Dashboard_Widget_Tests extends Tests {

	public function __construct() {

		parent::__construct();

	}

	 public function setUp() {
	
		$this -> dw = new Dashboard_Widget;
	
	}


	function test() {

		#$get_content = $this -> dw -> get_content();
		#$this -> assertNotEmpty( $get_content );

		#$get_the_title = $this -> dw -> get_the_title();
		#$this -> assertNotEmpty( $get_the_title );		

	}
	
}