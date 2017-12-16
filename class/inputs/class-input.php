<?php
/**
 * Input class file
 *
 * @copyright 2015-2016 iThoughts Informatique
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @package iThoughts\iThoughts WordPress Plugin Toolbox
 * @author Gerkin
 *
 * @version 6.0
 */

namespace ithoughts\v6_0;

if ( ! defined( 'ABSPATH' ) ) {
	status_header( 403 );
	wp_die("Forbidden");// Exit if accessed directly.
}

if ( ! class_exists( __NAMESPACE__ . '\\Input' ) ) {
	/**
	 * 
	 *
	 * @author Gerkin
	 */
	abstract class Input extends \ithoughts\v1_0\Singleton {
		protected $generated_htmlattrs = array();
		
		protected $attributes;
		
		protected $id;

		public function do_print(){
			echo wp_kses($this->to_html(), $this->generated_htmlattrs);
		}
		
		public function get_id(){
			return $this->id;
		}

		abstract public function to_html();

		public static function create_text_input($name, $options){
			return new InputText($name, $options);
		}

		public static function create_select_input($name, $options){
			return new InputSelect($name, $options);
		}

		public static function create_check_input($name, $options){
			return new InputCheck($name, $options);
		}
		
		protected static function name_to_id($name){
			return preg_replace("/[^\w\d_]/", "", $name);
		}
	}
}

require_once(dirname(__FILE__).'/class-inputtext.php');
require_once(dirname(__FILE__).'/class-inputselect.php');
require_once(dirname(__FILE__).'/class-inputcheck.php');