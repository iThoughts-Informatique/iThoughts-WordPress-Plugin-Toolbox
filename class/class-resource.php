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
	exit; // Exit if accessed directly
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
	 * Check if $string ends with $test
	 * @param  string  $string Checked string
	 * @param  string  $test   String to search
	 * @return boolean True if $test is present at the end of $string
	 * @see https://stackoverflow.com/questions/619610/whats-the-most-efficient-test-of-whether-a-php-string-ends-with-another-string
	 */
	function endswith($string, $test) {
		$strlen = strlen($string);
		$testlen = strlen($test);
		if ($testlen > $strlen) return false;
		return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
	}

	/**
	 * Join paths segments
	 * @param string $path,... Segments of path to join
	 * @return string Resulting paths
	 * @see https://stackoverflow.com/questions/1091107/how-to-join-filesystem-path-strings-in-php#answer-15575293
	 */
	function join_paths($base) {
		$paths = array();

		$args = func_get_args();
		array_shift($args);
		foreach ($args as $arg) {
			if ($arg !== '') { $paths[] = $arg; }
		}

		$tail = preg_replace('#/+#','/',join('/', $paths));
		$result;
		if(endswith($base, '/')){
			if($tail[0] == '/'){
				$result = $base.substr($tail, 1);
			} else {
				$result = $base.$tail;
			}
		} else {
			if($tail[0] == '/'){
				$result = $base.$tail;
			} else {
				$result = $base.'/'.$tail;
			}
		}
		return $result;
	}

	/**
	 * Abstract class to generate {@link Script} or {@link Style} class instances
	 *
	 * @author Gerkin
	 */
	abstract class Resource {
		/**
		 * Identifier of this resource.
		 * This identifier is used to "name" that resource. It will be used as 1st parameter of wp_register_*
		 *
		 * @author Gerkin
		 */
		public $identifier;

		/**
		 * Filename of this resource.
		 * Path to the resource file relative to the plugin folder. It will be used as 2nd parameter of wp_register_*
		 *
		 * @author Gerkin
		 */
		public $filename;

		/**
		 * Dependencies of this resource.
		 * Dependencies to print before this resource. It will be used as 3rd parameter of wp_register_*
		 *
		 * @author Gerkin
		 */
		public $dependencies = array();

		/**
		 *  Set to true if this resource is admin only
		 *
		 * @author Gerkin
		 */
		public $admin = false;

		/**
		 * Backbone instance that owns that resource
		 *
		 * @author Gerkin
		 */
		protected $backbone;

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
			if(endswith($filename, 'js')){
				$className = 'Script';
			} else if(endswith($filename, 'css')){
				$className = 'Style';
			} else {
				$this->backbone->log(LogLevel::Warn, "Unable to get the type of \"$filename\"");
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
			$min_suffix = $this->backbone->get_minify();
			if ($this->backbone->get_minify()){
				if(endswith(
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
					join_paths(
						$this->backbone->get_base_path(),
						$filename
					)
				)){
				$this->backbone->log(LogLevel::Info, "Minified version \"{$this->filename}\" not found, falling back to \"$filename\".");
					$filename = $this->filename;
				}
			}
			return join_paths(
				$this->backbone->get_base_url(),
				$filename
			);
		}
	}

	class Script extends Resource {
		public $localizeId;
		public $localizeData = array();

		public function __construct($backbone, $identifier, $filename, $dependencies = NULL, $admin = false, $localizeId = NULL, $localizeData = NULL){
			$this->backbone = $backbone;
			$this->identifier = $identifier;
			$this->filename = $filename;
			$this->dependencies = $dependencies;
			$this->admin = $admin;
			$this->localizeId = $localizeId;
			$this->localizeData = $localizeData;
		}

		public function register(){
			if( $this->admin === true && is_admin() !== true){
				return;
			}

			$file_url = $this->get_maybe_minified('.js');
			wp_register_script(
				$this->identifier,
				$file_url,
				$this->dependencies,
				$this->backbone->get_option('version')
			);
			if($this->localizeId != NULL){
				wp_localize_script(
					$this->identifier,
					$this->localizeId,
					$this->localizeData
				);
			}
		}

		public function enqueue(){
			wp_enqueue_script($this->identifier);
		}
	}

	class Style extends Resource {
		public function __construct($backbone, $identifier, $filename, $dependencies = NULL, $admin = false){
			$this->backbone = $backbone;
			$this->identifier = $identifier;
			$this->filename = $filename;
			$this->dependencies = $dependencies;
			$this->admin = $admin;
		}

		public function register(){
			if( $this->admin === true && is_admin() !== true){
				return;
			}

			$file_url = $this->get_maybe_minified('.css');
			wp_register_style(
				$this->identifier,
				$file_url,
				$this->dependencies,
				$this->backbone->get_option('version')
			);
		}

		public function enqueue(){
			wp_enqueue_style($this->identifier);
		}
	}
}
