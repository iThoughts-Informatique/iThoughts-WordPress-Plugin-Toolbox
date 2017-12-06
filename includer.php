<?php

if ( ! defined( 'ABSPATH' ) ) {
	 status_header( 403 );wp_die("Forbidden");// Exit if accessed directly
}

require_once( dirname( __FILE__ ) . '/class/class-singleton.php');
require_once( dirname( __FILE__ ) . '/class/class-loglevel.php');
require_once( dirname( __FILE__ ) . '/class/class-backbone.php');
require_once( dirname( __FILE__ ) . '/class/class-toolbox.php');
require_once( dirname( __FILE__ ) . '/class/class-pseudopost.php');
require_once( dirname( __FILE__ ) . '/class/class-resource.php');
require_once( dirname( __FILE__ ) . '/class/inputs/class-input.php');
