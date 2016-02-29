<?php
/**
 * Backbone class file
 *
 * @copyright 2015-2016 iThoughts Informatique
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.fr.html GPLv2
 * @package iThoughts\iThoughts WordPress Plugin Toolbox
 * @author Gerkin
 *         
 * @version 1.1.0
 */

namespace ithoughts\v1_1;

if(!class_exists(__NAMESPACE__."\\Backbone")){
	/**
	 * Backbone used in all plugins. Should be inherited by Backbone's plugin
	 */
	abstract class Backbone extends \ithoughts\v1_0\Singleton{		
		/**
		 * @var	mixed	$options			Plugin options
		 */
		protected $options;
		/**
		 * @var	string	$optionsName		Identifier of the plugin options
		 */
		protected $optionsName;
		/**
		 * @var	string	$base_class_path_path	Path to the root directory of the plugin, eg the one containing readme.txt
		 */
		protected $base_path;
		/**
		 * @var	string	$base_lang_path		Path to the lang directory
		 */
		protected $base_lang_path;
		/**
		 * @var	string	$base_class_path	Path to the class directory
		 */
		protected $base_class_path;
		/**
		 * @var	string	$base_url			URL to the root directory
		 */
		protected $base_url;
		/**
		 * @var string	$minify				Minifying prefix
		 */
		protected $minify;
		/**
		 * @var string[]	$scripts		Scripts to enqueue
		 */
		protected $scripts;

		/**
		 * Construct the generic backbone. Registers global scripts It MUST be called by child backbones.
		 */
		protected function __construct(){
			add_action( 'init',			array(&$this,	'backbone_enqueue_scripts_hight_priority'),	0 );
		}

		public function backbone_enqueue_scripts_hight_priority(){
			wp_register_script('ithoughts_aliases', $this->get_base_url() . '/submodules/iThoughts-WordPress-Plugin-Toolbox/js/ithoughts_aliases'.$this->get_minify().'.js',									array('jquery'), "1.0.0", false);

			wp_register_script(
				'ithoughts-simple-ajax',
				$this->get_base_url() . '/submodules/iThoughts-WordPress-Plugin-Toolbox/js/simple-ajax-form'.$this->get_minify().'.js',
				array('jquery-form',"ithoughts_aliases"),
				"1.1.0"
			);
		}




		/**
		 * Returns the plugin options
		 * @param boolean $defaults = false Return only default options of the plugin
		 *                                                                  
		 * @return array Options
		 */
		public function get_options($onlyDefaults = false){
			if($onlyDefaults)
				return $this->defaults;

			return $this->options;
		}

		/**
		 * Returns the desired plugin option
		 * @param boolean $name				Name of the option
		 * @param boolean $defaults = false Return only default value of this option in the plugin
		 *                          
		 * @return mixed   Option
		 */
		public function get_option($name, $onlyDefaults = false){
			$arr = $this->options;
			if($onlyDefaults)
				return $this->defaults;

			if(isset($arr[$name]))
				return $arr[$name];
			else
				return NULL;
		}

		/**
		 * Set plugin options
		 * @param array options		Set options of the plugin
		 * @param boolean $update = true Update options stored in base
		 *                        
		 * @return array Options
		 */
		public function set_options($options, $update = true){
			$this->options = array_merge($this->options, $options);
			if($update){
				update_option( $this->optionsName, $this->options );
			}
			return $this->options;
		}

		/**
		 * Set plugin options
		 * @param array options		Set options of the plugin
		 * @param boolean $update = true Update options stored in base
		 *                        
		 * @return array Options
		 */
		public function set_option($name, $value, $update = true){
			$this->options[$name] = $value;
			if($update){
				update_option( $this->optionsName, $this->options );
			}
			return $this->options;
		}

		/**
		 * Get plugin base path
		 *                        
		 * @return string Path to the root directory
		 */
		public function get_base_path(){
			return $this->base_path;
		}

		/**
		 * Get plugin base path to langs
		 *                        
		 * @return string Path to the root directory
		 */
		public function get_base_lang_path(){
			return $this->base_lang_path;
		}

		/**
		 * Get plugin base path to classes
		 *                        
		 * @return string Path to the root directory
		 */
		public function get_base_class_path(){
			return $this->base_class_path;
		}

		/**
		 * Get plugin base url to access resources
		 *                        
		 * @return string Path to the root directory
		 */
		public function get_base_url(){
			return $this->base_url;
		}

		/**
		 * Prepare enqueue of plugin script
		 * @param string $scriptName Name of the script
		 *                                       
		 * @return NULL
		 */
		public function add_script($scriptName){
			$this->scripts[$scriptName] = true;
		}

		/**
		 * Prepare enqueue of several plugin scripts
		 * @param string[] $scriptName Name of the script
		 *                                       
		 * @return NULL
		 */
		public function add_scripts($scriptNames){
			foreach($scriptNames as $scriptName){
				$this->scripts[$scriptName] = true;
			}
		}

		/**
		 * Get the minifying prefix
		 *                                       
		 * @return string The minifying suffix
		 */
		public function get_minify(){
			return $this->minify;
		}
	}
}