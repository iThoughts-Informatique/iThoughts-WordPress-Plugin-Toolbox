<?php
/**
 * Script class file
 *
 * @copyright 2015-2016 iThoughts Informatique
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @package iThoughts\iThoughts WordPress Plugin Toolbox
 * @author Gerkin
 *
 * @version 5.0
 */

namespace ithoughts\v5_0;

if ( ! defined( 'ABSPATH' ) ) {
	 status_header( 403 );wp_die("Forbidden");// Exit if accessed directly
}

if(!class_exists(__NAMESPACE__.'\\Resource')){
	class ResourceException extends \Exception
	{
		// Redéfinissez l'exception ainsi le message n'est pas facultatif
		public function __construct($message, $code = 0, Exception $previous = null) {

			// traitement personnalisé que vous voulez réaliser ...

			// assurez-vous que tout a été assigné proprement
			parent::__construct($message, $code, $previous);
		}

		// chaîne personnalisée représentant l'objet
		public function __toString() {
			return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
		}
	}

	/**
	 * Abstract class to generate Script or Style class instances
	 *
	 * @author Gerkin
	 * @see Script Resouce for a js file
	 * @see Style Resource for a css file
	 */
	abstract class Resource {
		/**
		 * @var string $identifier Identifier of this resource.
		 * This identifier is used to "name" that resource. It will be used as 1st parameter of wp_register_*
		 *
		 * @author Gerkin
		 */
		public $identifier;

		/**
		 * @var string $filename Filename of this resource.
		 * Path to the resource file relative to the plugin folder. It will be used as 2nd parameter of wp_register_*
		 *
		 * @author Gerkin
		 */
		public $filename;

		/**
		 * @var string[] $dependencies List of resource identifiers to load before this one.
		 * It will be used as 3rd parameter of wp_register_*
		 *
		 * @author Gerkin
		 */
		public $dependencies = array();

		/**
		 * @var boolean $admin Flag set to true if this resource is admin only
		 *
		 * @author Gerkin
		 */
		public $admin = false;

		/**
		 * @var Backbone $backbone Backbone instance that owns that resource
		 *
		 * @author Gerkin
		 */
		protected $backbone;

		/**
		 * @var string $file_url Url to this resource
		 *
		 * @author Gerkin
		 */
		protected $file_url;

		/**
		 * Check the filename and switch to generate {@link Script} or {@link Style}
		 *
		 * @param ... See {@link Script} or {@link Style} constructors
		 *
		 * @returns Style|Script
		 * @throws ResourceException if extension can't be linked to a type
		 * @author Gerkin
		 */
		public static final function generate($backbone, $identifier, $filename, $dependencies = NULL, $admin = false, $localizeId = NULL, $localizeData = NULL){
			// First, get the class to handle this file
			$className;
			if(Toolbox::endswith($filename, 'js')){
				$className = 'Script';
			} else if(Toolbox::endswith($filename, 'css')){
				$className = 'Style';
			} else {
				$backbone->log(LogLevel::WARN, "Unable to get the type of \"$filename\"");
				return null;
			}
			// Then, get the reflection class that will be used to spawn the instance with variadic args
			$reflector = new \ReflectionClass( __NAMESPACE__.'\\'.$className );
			return $reflector->newInstanceArgs(func_get_args());
		}

		/**
		 * Should call wp_register_script or wp_register_style on this resource.
		 *
		 * @author Gerkin
		 */
		abstract public function register();

		/**
		 * Should call wp_enqueue_script or wp_enqueue_style on this resource.
		 *
		 * @author Gerkin
		 */
		abstract public function enqueue();

		/**
		 * Get the minified file if required & if the file exists
		 * @param  string $ext Extension of the resource
		 * @return string Path of the target file
		 *
		 * @author Gerkin
		 */
		protected function get_maybe_minified($ext){
			$filename = $this->filename;
			$min_suffix = '.min';
			if ($this->backbone->get_minify()){
				if(Toolbox::endswith(
					$this->filename,
					$min_suffix.$ext
				) == false){
					$filename = str_replace(
						$ext,
						$min_suffix.$ext,
						$filename
					);
				}
			}
			if($filename != $this->filename){
				if(!file_exists(
					Toolbox::join_paths(
						$this->backbone->get_base_path(),
						$filename
					)
				)){
					$this->backbone->log(LogLevel::INFO, "Minified version \"{$this->filename}\" not found, falling back to \"$filename\".");
					$filename = $this->filename;
				}
			}
			return Toolbox::join_paths(
				$this->backbone->get_base_url(),
				$filename
			);
		}

		/**
		 * Get the resource's url
		 * @return string Url to this resource
		 *
		 * @author Gerkin
		 */
		public function get_file_url(){
			return $this->file_url;
		}
	}

	class Script extends Resource {
		public $localizeId;
		public $localizeData = array();

		/**
		 * Build a new Script
		 * @private
		 * @param Backbone $backbone              Backbone plugin instance. Paths & urls of this script will be relative to this plugin
		 * @param string   $identifier            Unique identifier of this script.
		 * @param string   $filename              Path to the script relative to the plugin of `backbone`
		 * @param string[] [$dependencies = NULL] Other scripts identifiers to load before this script
		 * @param boolean  [$admin = false]       Indicates if this resource should be loaded only on admin pages
		 * @param string   [$localizeId = NULL]   Identifier of JavaScript data related to this script
		 * @param mixed    [$localizeData = NULL] Data to attach to this script. This data will be stored in window[$localizeId]
		 *
		 * @author Gerkin
		 */
		public function __construct($backbone, $identifier, $filename, $dependencies = NULL, $admin = false, $localizeId = NULL, $localizeData = NULL){
			$this->backbone = $backbone;
			$this->identifier = $identifier;
			$this->filename = $filename;
			$this->dependencies = $dependencies;
			$this->admin = $admin;
			$this->localizeId = $localizeId;
			$this->localizeData = $localizeData;
			$this->file_url = $this->get_maybe_minified('.js');
		}

		/**
		 * Register the script if admin conditions are met
		 *
		 * @see wp_register_script Wordpress function to register script
		 * @author Gerkin
		 */
		public function register(){
			if( $this->admin === true && is_admin() !== true){
				return;
			}

			wp_register_script(
				$this->identifier,
				$this->file_url,
				$this->dependencies,
				$this->backbone->get_option('version')
			);
			$this->set_localize_data($this->localizeId, $this->localizeData);
		}

		/**
		 * Attach data to this script
		 * @param string $label Identifier of JavaScript data
		 * @param mixed  $data  Data to store on the global JavaScript scope
		 *
		 * @see wp_localize_script Wordpress function to attach data to a script
		 * @author Gerkin
		 */
		public function set_localize_data($label, $data){
			if(isset($label)){
				$this->localizeId = $label;
				$this->localizeData = $data;
				wp_localize_script($this->identifier, $this->localizeId, $this->localizeData);
			}
		}

		/**
		 * Request to load this script on client side
		 *
		 * @see wp_enqueue_script Wordpress function to load a script
		 * @author Gerkin
		 */
		public function enqueue(){
			wp_enqueue_script($this->identifier);
		}
	}

	class Style extends Resource {
		/**
		 * Build a new Style
		 * @private
		 * @param Backbone $backbone              Backbone plugin instance. Paths & urls of this stylesheet will be relative to this plugin
		 * @param string   $identifier            Unique identifier of this stylesheet.
		 * @param string   $filename              Path to the stylesheet relative to the plugin of `backbone`
		 * @param string[] [$dependencies = NULL] Other stylesheets identifiers to load before this stylesheet
		 * @param boolean  [$admin = false]       Indicates if this resource should be loaded only on admin pages
		 *
		 * @author Gerkin
		 */
		public function __construct($backbone, $identifier, $filename, $dependencies = NULL, $admin = false){
			$this->backbone = $backbone;
			$this->identifier = $identifier;
			$this->filename = $filename;
			$this->dependencies = $dependencies;
			$this->admin = $admin;
			$this->file_url = $this->get_maybe_minified('.js');
		}

		/**
		 * Register the stylesheet if admin conditions are met
		 *
		 * @see wp_register_style Wordpress function to register stylesheets
		 * @author Gerkin
		 */
		public function register(){
			if( $this->admin === true && is_admin() !== true){
				return;
			}

			$file_url = $this->get_maybe_minified('.css');
			wp_register_style(
				$this->identifier,
				$this->file_url,
				$this->dependencies,
				$this->backbone->get_option('version')
			);
		}

		/**
		 * Request to load this stylesheet on client side
		 *
		 * @see wp_enqueue_style Wordpress function to load a stylesheet
		 * @author Gerkin
		 */
		public function enqueue(){
			wp_enqueue_style($this->identifier);
		}
	}
}
