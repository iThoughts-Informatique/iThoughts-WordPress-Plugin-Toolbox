<?php
/**
 * Check input generator class file
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

if ( ! class_exists( __NAMESPACE__ . '\\InputCheck' ) ) {
	/**
	 * 
	 *
	 * @author Gerkin
	 */
	class InputCheck extends \ithoughts\v6_0\Input {
		protected $selected;

		protected $radio;

		protected $options;

		public function __construct($name, $settings){
			// Store selection.
			$this->selected = isset($settings['selected']) ? $settings['selected'] : NULL;

			// Check if radio
			$this->radio = isset($settings['radio']) ? $settings['radio'] : true;

			// Transform name and store it in class properties
			$this->id = self::name_to_id($name);
			if(isset($settings['options']) && is_array($settings['options'])){
				$options_temp = $settings['options'];
				if(array_values($options_temp) === $options_temp){ // If provided options are not an associative array...
					$this->options = array();
					// If pure array, value & label are identical.
					foreach($options_temp as $value){
						$new_opt = array(
							'attributes' => array(
								'value' => $value,
							)
						);
						$this->options[] = $this->configure_opt($new_opt, $name, $value);
					}
				} else {
					// If associative array, array key is the value, and array value describe options
					foreach($options_temp as $key => $value){
						$new_opt;
						if(is_array($value)){
							$new_opt = $value;
							// Add attributes array on each options
							if(!isset($new_opt['attributes']))
								$new_opt['attributes'] = array();
						} else {
							$new_opt = array(
								'text' => $value,
								'attributes' => array(),
							);
						}
						$new_opt['attributes']['value'] = $key;
						$this->options[] = $this->configure_opt($new_opt, $name, $key);
					}
				}
			} else {
				$this->options = array();
			}
			/*
			$allLabeled = true;
			if(!isset($options["options"]))
				return $ret;

			if(!is_array($options["options"]))
				$options["options"] = array($options["options"]);

			foreach($options["options"] as $option => $data){
				$str = "";
				$strLabel = NULL;
				if(isset($data["label"]) && $data["label"]){
					if($data["label"] != null && is_array($data["label"])){
						if(isset($data["label"]["text"])){
							$strLabel = $data["label"]["text"];
							$attrs = "";
							if(isset($data["label"]["attributes"]) && is_array($data["label"]["attributes"])){
								$attrs = Toolbox::concat_attrs($data["label"]["attributes"]);
							}
							$str .= '<label for="'.$name."_".$option.' '.$attrs.'">&nbsp;';
						}
					} else {
						$str .= '<label for="'.$name."_".$option.'">&nbsp;';
						$strLabel = $data["label"];
					}
				} else {
					$allLabeled = false;
				}
				$str .= '<input name="'.$name.'"';
				if(isset($options["radio"]) && $options["radio"])
					$str .= ' type="radio"';
				else
					$str .= ' type="checkbox"';
				$str .= ' value="'.$option.'"';
				if(!isset($data["attributes"]))
					$data["attributes"] = array();
				if(!isset($data["attributes"]["id"]))
					$data["attributes"]["id"] = $base_id."_".$option;
				if(!isset($data["attributes"]["autocomplete"]))
					$data["attributes"]["autocomplete"] = "off";
				if(isset($data["required"]) && $data["required"])
					$data["attributes"]["required"] = "required";

				$str .= Toolbox::concat_attrs($data["attributes"]);
				if(isset($options["selected"]) && ((is_array($options["selected"]) && in_array($option, $options["selected"], true)) || (!is_array($options["selected"]) && $options["selected"] == $option)))
					$str .= ' checked="checked"';
				$str .= ' />';
				if($strLabel != NULL){
					$str .= '&nbsp;'.$strLabel.'</label>';
				}

				$ret[$option] = $str;
			}
			if($allLabeled && isset($options["implode"])){
				$ret = implode($options["implode"], $ret);
			} else if(count($ret) == 1){
				$keys = array_keys($ret);
				return $ret[$keys[0]];
			}
			return $ret;*/

			$this->generated_htmlattrs['input'] = array();
			foreach($this->options as $opt){
				$this->generated_htmlattrs['input'] = array_merge(
					$this->generated_htmlattrs['input'],
					array_combine(
						array_keys($opt['attributes']),
						array_fill(0, count($opt['attributes']), true)
					)
				);
			}
		}

		protected function configure_opt($option, $name, $value){
			// Select option if required.
			if($this->has_selected($value)){
				$option['attributes']['checked'] = 'checked';
			}
			$option['attributes']['type'] = $this->radio ? 'radio' : 'checkbox';
			$option['attributes']['name'] = $name;
			return $option;
		}

		/**
		 * Check if the option key provided is selected
		 * @param  string $opt_key   Key of the option to check
		 * @return boolean  True if selected, false otherwise
		 */
		protected function has_selected($opt_key){
			if($this->selected !== NULL){
				if(is_array($this->selected)){
					return in_array($opt_key, $this->selected, true);
				} else {
					return $this->selected === $opt_key;
				}
			}
			return false;
		}


		public function to_html(){
			$strret = '';
			foreach($this->options as $opt){
				$strret .= '<input'.Toolbox::concat_attrs($opt['attributes']).'/>';
			}
			return $strret;
		}
	}
}