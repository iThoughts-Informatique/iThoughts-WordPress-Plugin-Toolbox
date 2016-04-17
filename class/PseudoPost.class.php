<?php
/**
 * PseudoPost class file
 *
 * @copyright 2015-2016 iThoughts Informatique
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.fr.html GPLv2
 * @package iThoughts\iThoughts WordPress Plugin Toolbox
 * @author Gerkin
 *         
 * @version 1.0
 */

namespace ithoughts\v1_0;

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

if(!interface_exists(__NAMESPACE__."\\PseudoPost")){
	/**
	 * PseudoPost interface is a way to create custom WP_Post castable to WordPress core
	 */
	interface PseudoPost{
		function to_WP_Post();
	}
}