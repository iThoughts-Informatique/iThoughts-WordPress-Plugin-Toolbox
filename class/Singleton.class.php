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

namespace ithoughts\v1_0;

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

if(!class_exists(__NAMESPACE__."\\Singleton")){
	/**
	 * Singleton abstract class
	 */
	abstract class Singleton{

		private static $singletons = array();

		private function __construct(){}

		/**
		 * Returns the instance
		 * @author Gerkin
		 *           
		 * @return mixed The class instance
		 */
		public static final function get_instance(){
			$class = get_called_class();
			if( !isset( self::$singletons[ $class ] ) ) {
				$reflect  = new \ReflectionClass($class);
				self::$singletons[ $class ] = $reflect->newInstanceArgs(array_values(func_get_args()));
			}
			return self::$singletons[ $class ];
		}
	}
}