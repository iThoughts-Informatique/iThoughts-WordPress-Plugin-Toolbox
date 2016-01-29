<?php
/**
 * Singleton class file
 *
 * @copyright 2015-2016 iThoughts Informatique
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.fr.html GPLv2
 * @package iThoughts\iThoughts WordPress Plugin Toolbox
 * @author Gerkin
 *         
 * @version 1.0
 */

namespace ithoughts;

if(!interface_exists("\\ithoughts\\Singleton")){
	/**
	 * Singleton interface
	 */
	interface Singleton{
		/**
		 * Returns the instance
		 * @author Gerkin
		 *           
		 * @return mixed The class instance
		 */
		public static function get_instance();
	}
}