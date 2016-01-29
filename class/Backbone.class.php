<?php
/**
 * Backbone class file
 *
 * @copyright 2015-2016 iThoughts Informatique
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.fr.html GPLv2
 * @package iThoughts\iThoughts WordPress Plugin Toolbox
 * @author Gerkin
 *         
 * @version 1.0
 */

namespace ithoughts;

if(!class_exists("\\ithoughts\\Backbone")){
	/**
	 * Backbone used in all plugins. Should be inherited by Backbone's plugin
	 */
	abstract class Backbone implements Singleton{
		/**
		 * @var	mixed	$options			Plugin options
		 */
		static protected $options;
		/**
		 * @var	string	$plugin_base_path	Path to the root directory of the plugin, eg the one containing readme.txt
		 */
		static protected $plugin_base_path;
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
		 * Returns the plugin options
		 * @param bool $defaults = false Return only default options of the plugin
		 *                                                                  
		 * @return array Options
		 */
		abstract static protected function get_options($defaults = false){}
	}
}