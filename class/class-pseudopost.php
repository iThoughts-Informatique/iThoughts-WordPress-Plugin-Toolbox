<?php
/**
 * PseudoPost class file
 *
 * @copyright 2015-2016 iThoughts Informatique
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @package iThoughts\iThoughts WordPress Plugin Toolbox
 * @author Gerkin
 *
 * @version 3.0
 */

namespace ithoughts\v1_0;
if ( ! defined( 'ABSPATH' ) ) {
	 status_header( 403 );wp_die("Forbidden");// Exit if accessed directly
}
if(!interface_exists(__NAMESPACE__."\\PseudoPost")){
	/**
	 * PseudoPost interface is a way to create custom posts castable to WordPress core WP_Post
	 *
	 * @author Gerkin
	 */
	interface PseudoPost{
		function to_WP_Post();
	}
}
