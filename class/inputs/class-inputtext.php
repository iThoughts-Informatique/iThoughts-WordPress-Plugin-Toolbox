<?php
/**
 * Text input generator class file
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

require_once(dirname(__FILE__).'/class-input.php');

if ( ! class_exists( __NAMESPACE__ . '\\InputText' ) ) {
	/**
	 * 
	 *
	 * @author Gerkin
	 */
	class InputText extends \ithoughts\v6_0\Input {
		protected $is_textarea;

		protected $value;

		public function __construct($name, $settings){
			// Set a default value for the type.
			if(!isset($settings["type"])){
				$settings["type"] = 'text';
			}
			// Check if the input is a textarea.
			if('textarea' === $settings["type"]){
				$this->is_textarea = true;
				unset($settings["type"]);
			} else {
				$this->is_textarea = false;
			}

			// Transform name and store it in class properties
			$this->id = self::name_to_id($name);

			// Set <input> or <textarea> tag options
			$this->attributes = array_merge(
				array_filter(array(
					'id' => $this->id,
					'required' => isset($settings['required']) && $settings['required'] ? 'required' : NULL,
				)),
				isset($settings['attributes']) ? $settings['attributes'] : array(),
				array(
					'name' => $name,
					'type' => $settings["type"],
				)
			);

			$this->value = isset($settings['value']) ? $settings['value'] : NULL;
			if($this->value === null && isset($settings['default'])){
				$this->value = $settings['default'];
			}

			// Do specific action if textarea
			if(true === $this->is_textarea){
				unset($this->attributes['autocomplete']);
			} else {
				if(!isset($this->attributes['autocomplete']) || false === $this->attributes['autocomplete']) {
					$this->attributes['autocomplete'] = 'off';
				}
				$this->attributes['value'] = $this->value;
			}
			$this->generated_htmlattrs[$this->is_textarea ? 'textarea' : 'input'] = array_combine(
				array_keys($this->attributes),
				array_fill(0, count($this->attributes), true)
			);
		}

		public function to_html(){
			$attrs_str = Toolbox::concat_attrs($this->attributes);

			$strret;
			if($this->is_textarea){
				$strret = "<textarea $attrs_str>${esc_html($this->value)}</textarea>";
			} else {
				$strret = "<input $attrs_str/>";
			}
			return $strret;
		}
	}
}