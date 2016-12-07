<?php
/**
 * Module class file
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

if(!class_exists(__NAMESPACE__."\\Module")){
	/**
	 * Backbone used in all plugins. Should be inherited by Backbone's plugin
	 */
	abstract class Module extends Singleton{
		/**
		 * @var	mixed	$options			Plugin options
		 */
		static protected $options;
		/**
		 * @var	string	$base_class_path_path	Path to the root directory of the plugin, eg the one containing readme.txt
		 */
		static protected $base_class_path_path;
		/**
		 * @var	string	$base_lang_path		Path to the lang directory
		 */
		static protected $base_lang_path;
		/**
		 * @var	string	$base_class_path	Path to the class directory
		 */
		static protected $base_class_path;
		/**
		 * @var	string	$base_url			URL to the root directory
		 */
		static protected $base_url;
		/**
		 * @var string	$minify				Minifying prefix
		 */
		static protected $minify;

		/**
		 * Returns the plugin options
		 * @param bool $defaults = false Return only default options of the plugin
		 *
		 * @return array Options
		 */
		abstract static protected function get_options($defaults = false);
	}
}