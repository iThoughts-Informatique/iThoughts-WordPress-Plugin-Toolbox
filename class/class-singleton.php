<?php
/**
 * Singleton class file
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
	exit; // Exit if accessed directly
}

if(!class_exists(__NAMESPACE__."\\Singleton")){
	/**
	 * Singleton abstract class
	 */
	abstract class Singleton{

		/**
		 * @var \array<string,Singleton> $singletons Storage for singletons. This is where `get_instance` search for called class.
		 * @see Singleton::get_instance
		 *
		 * @author Gerkin
		 * @category singleton
		 */
		private static $singletons = array();

		protected abstract function __construct();

		/**
		 * Get the singleton instance.
		 * If the singleton of that class does not exists, it is created. Else, it is simply returned
		 * @return self The class instance
		 *
		 * @author Gerkin
		 * @category singleton
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
