<?php

if ( ! defined( 'ABSPATH' ) ) {
	 status_header( 403 );wp_die("Forbidden");// Exit if accessed directly
}

require_once("class/class-singleton.php");
require_once("class/class-loglevel.php");
require_once("class/class-backbone.php");
require_once("class/class-toolbox.php");
require_once("class/class-pseudopost.php");
require_once("class/class-resource.php");
