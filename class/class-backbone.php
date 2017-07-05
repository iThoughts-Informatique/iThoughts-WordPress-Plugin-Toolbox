<?php
/**
 * Backbone class file
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
	 status_header( 403 );wp_die("Forbidden");// Exit if accessed directly.
}

if ( ! class_exists( __NAMESPACE__ . '\\Backbone' ) ) {
	/**
	 * Backbone used in all plugins. Should be inherited by Backbone's plugin
	 *
	 * @author Gerkin
	 */
	abstract class Backbone extends \ithoughts\v1_0\Singleton {
		/**
		 * Plugin options.
		 * This value is set by first call to `get_option` or `get_options` with `onlyDefaults` set to false
		 *
		 * @var	mixed	$options.
		 *
		 * @see Backbone::get_option	Retrieve a single option
		 * @see Backbone::get_options	Retrieve all options
		 * @author Gerkin
		 * @category Options
		 */
		protected $options = null;

		/**
		 * Plugin default options.
		 * This value is used as options when calling `get_option` or `get_options` with `onlyDefaults` set to true
		 *
		 * @var	mixed	$defaultOptions
		 *
		 * @see Backbone::get_option	Retrieve a single option
		 * @see Backbone::get_options	Retrieve all options
		 * @author Gerkin
		 * @category Options
		 */
		protected $defaultOptions;

		/**
		 * Identifier of the plugin options.
		 * Used in the wordpress option table, as the `option_name` column value
		 *
		 * @var	string	$optionsName
		 *
		 * @author Gerkin
		 * @category Options
		 */
		protected $optionsName;

		/**
		 * Path to the root directory of the plugin, eg the one containing readme.txt
		 *
		 * @var	string	$base_class_path_pat
		 *
		 * @author Gerkin
		 * @category paths & urls
		 */
		protected $base_path;

		/**
		 * Path to the lang directory
		 *
		 * @var	string	$base_lang_path
		 *
		 * @author Gerkin
		 * @category paths & urls
		 */
		protected $base_lang_path;

		/**
		 * Path to the class directory
		 *
		 * @var	string	$base_class_pat
		 *
		 * @author Gerkin
		 * @category paths & urls
		 */
		protected $base_class_path;

		/**
		 * URL to the root directory
		 *
		 * @var	string	$base_url
		 *
		 * @author Gerkin
		 * @category paths & urls
		 * @category resources
		 */
		protected $base_url;

		/**
		 * Minifying prefix
		 *
		 * @var boolean	$minify
		 *
		 * @author Gerkin
		 * @category resources
		 */
		protected $minify;

		/**
		 * Scripts to enqueue
		 *
		 * @var string[]	$scripts
		 *
		 * @author Gerkin
		 */
		protected $scripts = array();

		/**
		 * Resources managed by this plugin
		 *
		 * @var Resource[]	$resources
		 *
		 * @author Gerkin
		 * @category resources
		 */
		protected $resources = array();

		/**
		 * Construct the generic backbone. Registers global scripts It MUST be called by child backbones.
		 *
		 * @throws \Exception if basepath is not set before calling the constructor.
		 * @author Gerkin
		 */
		protected function __construct() {
			if ( null === $this->base_path ) {
				throw new \Exception( 'Missing definition of Backbone::$basepath' );
			}
			// If `base_url` is not set, define it by getting the url to this plugin.
			if ( null === $this->base_url ) {
				$this->base_url = plugins_url() . '/' . dirname( plugin_basename( $this->base_path ) );
			}
			// Then change to parent directory.
			$this->base_path = dirname( $this->base_path );
			// If `base_class_path` is not set, define it.
			if ( null === $this->base_class_path ) {
				$this->base_class_path = $this->base_path . '/class';
			}
			// If `base_lang_path` is not set, define it.
			if ( null === $this->base_lang_path ) {
				$this->base_lang_path = $this->base_path . '/lang';
			}

			// Set the minify flag depending on debug constants.
			if ( (defined( 'WP_DEBUG' ) && WP_DEBUG) || (defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG) ) {
				$this->minify = false;
			} else {
				$this->minify = true;
			}

			add_action( 'init',			array( &$this, 'declare_scripts' ),	0 );
		}

		/**
		 * Register toolbox scripts.
		 *
		 * @author Gerkin
		 * @uses Backbone::declare_resource
		 * @action init
		 * @category resources
		 */
		public function declare_scripts() {
			$this->declare_resource(
				'ithoughts-core-v5',
				'submodules/iThoughts-WordPress-Plugin-Toolbox/js/dist/ithoughts-core-v5.js',
				array( 'jquery' )
			);

			$this->declare_resource(
				'ithoughts-simple-ajax-v5',
				'submodules/iThoughts-WordPress-Plugin-Toolbox/js/dist/simple-ajax-form-v5.js',
				array(
					'jquery-form',
					'ithoughts-core-v5',
				)
			);
			$this->declare_resource(
				'ithoughts-serialize-object-v5',
				'submodules/iThoughts-WordPress-Plugin-Toolbox/js/dist/jquery-serialize-object-v5.js',
				array( 'ithoughts-core-v5' )
			);
		}


		/**
		 * Log the message to the plugin log file with the specified level `$level`
		 *
		 * @param LogLevel $level Log level of this message. Must be one of the constants of LogLevel class.
		 * @param mixed    $data Datas to log. You can put unlimited number of parameters..
		 *
		 * @author Gerkin
		 * @category Debug
		 */
		public function log( $level, $data ) {
			// Check if the log request is valid.
			$verbosity = $this->get_option( 'verbosity' );
			if ( $verbosity < $level || 0 === $verbosity ) {
				return;
			}

			// Get level label.
			switch ( $level ) {
				case LogLevel::SILENT: {
					$mode_name = 'SILENT';
				} break;

				case LogLevel::ERROR: {
					$mode_name = 'Error';
				} break;

				case LogLevel::WARN: {
					$mode_name = 'Warn ';
				} break;

				case LogLevel::INFO: {
					$mode_name = 'Info ';
				} break;

				case LogLevel::SILLY: {
					$mode_name = 'Silly';
				} break;

				default: {
					$this->log( LogLevel::ERROR, "Trying to log with unexistent mode \"$mode\": ", debug_backtrace() );
					return;
				}
			}

			// Get the log path & args.
			$filename = "{$this->base_path}/{$this->optionsName}.log";
			$file = fopen( $filename, 'a+' );
			if ( false === $file ) {
				return;
			}
			$args = func_get_args();
			array_shift( $args );

			// Do the log.
			$date = gmdate( 'Y-m-d H:i:s' );
			fwrite( $file, "$date [$mode_name] => " );
			foreach ( $args as $arg ) {
				$str = $arg;
				$type = gettype( $arg );
				if ( in_array( $type, array( 'object', 'array', 'resource', 'unknown type' ) ) ) {
					$str = print_r( $arg, true );
				}
				fwrite( $file, $str . ' ' );
			}
			fwrite( $file, "\n" );
			fclose( $file );
		}


		/**
		 * Returns the plugin options. If it's the first time that this function is called, it also auto-init the options, retrieving them from the DB
		 *
		 * @param boolean $defaults = false Return only default options of the plugin
		 * @return array Options
		 *
		 * @author Gerkin
		 * @category Options
		 */
		public function get_options( $onlyDefaults = false ) {
			if ( $onlyDefaults ) {
				return $this->defaultOptions;
			}

			// If options are not set, retrieve from DB
			$this->define_options();
			return $this->options;
		}

		/**
		 * Returns the desired plugin option. If it's the first time that this function is called, it also auto-init the options, retrieving them from the DB
		 *
		 * @param string  $name              Name of the option
		 * @param boolean $defaults = false Return only default value of this option in the plugin
		 * @return mixed   Option
		 *
		 * @author Gerkin
		 * @category Options
		 */
		public function get_option( $name, $onlyDefaults = false ) {
			if ( $onlyDefaults ) {
				return (isset( $this->defaultOptions[ $name ] ) ? $this->defaultOptions[ $name ] : null);
			}

			$this->define_options();
			if ( isset( $this->options[ $name ] ) ) {
				return $this->options[ $name ];
			} else { return (isset( $this->defaultOptions[ $name ] ) ? $this->defaultOptions[ $name ] : null);
			}
		}

		/**
		 * Try to define options by merging with default if not set
		 *
		 * @author Gerkin
		 * @category Options
		 */
		private function define_options() {
			if ( null === $this->options ) {
				$this->options = array_merge( $this->defaultOptions, get_option( $this->optionsName, $this->get_options( true ) ) );
			}
		}

		/**
		 * Set plugin options
		 *
		 * @param array $options		Set options of the plugin
		 * @param boolean $update = true Update options stored in base
		 * @return array Options
		 *
		 * @author Gerkin
		 * @category Options
		 */
		public function set_options( $options, $update = true ) {
			$this->options = array_merge( $this->options, $options );
			if ( $update ) {
				wp_cache_delete( 'alloptions', 'options' );
				update_option( $this->optionsName, $this->options );
				wp_cache_delete( 'alloptions', 'options' );
			}
			return $this->options;
		}

		/**
		 * Set plugin options
		 *
		 * @param array options		Set options of the plugin
		 * @param boolean                                     $update = true Update options stored in base
		 * @return array Options
		 *
		 * @author Gerkin
		 * @category Options
		 */
		public function set_option( $name, $value, $update = true ) {
			$this->options[ $name ] = $value;
			if ( $update ) {
				update_option( $this->optionsName, $this->options );
			}
			return $this->options;
		}

		/**
		 * Get plugin base path
		 *
		 * @return string Path to the root directory
		 *
		 * @author Gerkin
		 * @category paths & urls
		 */
		public function get_base_path() {
			return $this->base_path;
		}

		/**
		 * Get plugin base path to langs
		 *
		 * @return string Path to the root directory
		 *
		 * @author Gerkin
		 * @category paths & urls
		 */
		public function get_base_lang_path() {
			return $this->base_lang_path;
		}

		/**
		 * Get plugin base path to classes
		 *
		 * @return string Path to the root directory
		 *
		 * @author Gerkin
		 * @category paths & urls
		 */
		public function get_base_class_path() {
			return $this->base_class_path;
		}

		/**
		 * Get plugin base url to access resources
		 *
		 * @return string Path to the root directory
		 *
		 * @author Gerkin
		 * @category paths & urls
		 */
		public function get_base_url() {
			return $this->base_url;
		}

		/**
		 * Add the described resource to the catalog of resources managed by this plugin.
		 *
		 * @param string $resourceName Identifier for this resource. Will be used in wp_register_* or wp_enqueue_*
		 * @see Resource::generate Called with $this as first argument. Check it for arguments list
		 *
		 * @author Gerkin
		 * @category Resources
		 */
		public function declare_resource( $resourceName ) {
			$args = func_get_args();
			array_unshift( $args, $this );
			$resource = call_user_func_array( array( __NAMESPACE__ . '\\Resource', 'generate' ), $args );
			if ( isset( $resource ) ) {
				$resource->register();
				$this->resources[ $resourceName ] = $resource;
			}
		}

		/**
		 * Retrieve the resource with the provided identifier, if any
		 *
		 * @param  string $resourceName Identifier of the resource
		 * @return Resource|null Resource found
		 *
		 * @author Gerkin
		 * @category Resources
		 */
		public function get_resource( $resourceName ) {
			if ( isset( $this->resources[ $resourceName ] ) ) {
				return $this->resources[ $resourceName ];
			}
			return null;
		}

		/**
		 * Enqueue the resource with the provided identifier, if any
		 *
		 * @param  string $resourceName Identifier of the resource
		 *
		 * @author Gerkin
		 * @category Resources
		 */
		public function enqueue_resource( $resourceName ) {
			$resource = $this->get_resource( $resourceName );
			if ( isset( $resource ) ) {
				$resource->enqueue();
			} else {
				$this->log( LogLevel::INFO, "Trying to enqueue unexistent resource \"$resourceName\"" );
			}
		}

		/**
		 * Enqueue the resources with the provided identifiers
		 *
		 * @param  string[] $resourceNames Identifier of the resource
		 *
		 * @author Gerkin
		 * @uses Backbone::enqueue_resource
		 * @category Resources
		 */
		public function enqueue_resources( $resourceNames ) {
			foreach ( $resourceNames as $resourceName ) {
				$this->enqueue_resource( $resourceName );
			}
		}

		/**
		 * Prepare enqueue of plugin script
		 *
		 * @param string $scriptName Name of the script
		 *
		 * @author Gerkin
		 */
		public function add_script( $scriptName ) {
			$this->scripts[ $scriptName ] = true;
		}

		/**
		 * Prepare enqueue of several plugin scripts
		 *
		 * @param string[] $scriptName Name of the script
		 *
		 * @author Gerkin
		 */
		public function add_scripts( $scriptNames ) {
			foreach ( $scriptNames as $scriptName ) {
				$this->scripts[ $scriptName ] = true;
			}
		}

		/**
		 * Return true if script was enqueued with {@link add_script} or {@link add_scripts}
		 *
		 * @param string $scriptName Name of the script.
		 * @return boolean
		 *
		 * @author Gerkin
		 */
		public function get_script( $scriptName ) {
			return (isset( $this->scripts[ $scriptName ] ) && true === $this->scripts[ $scriptName ]);
		}

		/**
		 * Return an associative array with values set to true if script was enqueued with {@link add_script} or {@link add_scripts}
		 *
		 * @param string[] $scriptNames Name of scripts.
		 * @return boolean[]
		 *
		 * @author Gerkin
		 */
		public function get_scripts( $scriptNames = null ) {
			if ( null === $scriptName ) {
				return $this->scripts;
			}
			$ret = array();
			foreach ( $scriptNames as $scriptName ) {
				$ret[ $scriptName ] = $this->get_script($scriptNames);
			}
			return $ret;
		}

		/**
		 * Get the minifying prefix
		 *
		 * @return string The minifying suffix
		 *
		 * @author Gerkin
		 */
		public function get_minify() {
			return $this->minify;
		}
	}
}// End if().
