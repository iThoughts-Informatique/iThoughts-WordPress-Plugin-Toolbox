<?php
/**
 * Select input generator class file
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

if ( ! class_exists( __NAMESPACE__ . '\\InputSelect' ) ) {
	/**
	 * 
	 *
	 * @author Gerkin
	 */
	class InputSelect extends \ithoughts\v6_0\Input {

		protected $selected;

		protected $options;

		/**
		 * Construct a new instance of Select input
		 * @param string $name     Name of the input
		 * @param array $settings Configuration of the input
		 */
		public function __construct($name, $settings){
			// Store selection
			$this->selected = isset($settings['selected']) ? $settings['selected'] : NULL;

			// Transform name and store it in class properties
			$this->id = self::name_to_id($name);
			
			// Set <select> tag options
			$this->attributes = array_merge(
				array_filter(array(
					'id' => $this->id,
					'required' => isset($settings['required']) && $settings['required'] ? 'required' : NULL,
					'multiple' => isset($settings['multiple']) && $settings['multiple'] ? true : NULL
				)),
				isset($settings['attributes']) ? $settings['attributes'] : array(),
				array(
					'name' => $name,
				)
			);
			// Cast autocomplete to a valid value
			if(!isset($this->attributes['autocomplete']) || false === $this->attributes['autocomplete'])
				$this->attributes['autocomplete'] = 'off';
			// Add <select> attributes to generated list
			$this->generated_htmlattrs['select'] = array_combine(
				array_keys($this->attributes),
				array_fill(0, count($this->attributes), true)
			);

			// Set options.
			// Define default/blank.
			$headOpt;
			if(isset($settings['allow_blank'])){
				$headOpt = array(
					array(
						'text' => $settings['allow_blank'],
						'attributes' => array(
							'value' => ''
						),
					)
				);

				if($this->has_selected('')) {
					$headOpt['']['attributes']['selected'] = true;
				}
			} else {
				$headOpt = array();
			}
			// Standardize other options
			$options;
			if(isset($settings['options']) && is_array($settings['options'])){
				$options_temp = $settings['options'];
				if(array_values($options_temp) === $options_temp){ // If provided options are not an associative array...
					$options = array();
					// If pure array, value & label are identical.
					foreach($options_temp as $value){
						$new_opt = array(
							'text' => $value,
							'attributes' => array(
								'value' => $value,
							)
						);
						if($this->has_selected($value)){
							$new_opt['attributes']['selected'] = true;
						}
						$options[] = $new_opt;
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
							if(!isset($new_opt['text']))
								$new_opt['text'] = $key;
						} else {
							$new_opt = array(
								'text' => $value,
								'attributes' => array(),
							);
						}
						$new_opt['attributes']['value'] = $key;
						// Select option if required.
						if($this->has_selected($key))
							$new_opt['attributes']['selected'] = true;
						$options[] = $new_opt;
					}
				}
			} else {
				$options = array();
			}

			$this->options = $headOpt + $options;
			// Add <option> attributes to generated list
			$this->generated_htmlattrs['option'] = array();
			foreach($this->options as $opt){
				$this->generated_htmlattrs['option'] = array_merge(
					$this->generated_htmlattrs['option'],
					array_combine(
						array_keys($opt['attributes']),
						array_fill(0, count($opt['attributes']), true)
					)
				);
			}
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

		/**
		 * Converts the class to its html string representation
		 * @return string HTML of the input
		 */
		public function to_html(){
			$strret = '<select'.Toolbox::concat_attrs($this->attributes).'>';
			foreach($this->options as $opt){
				$strret .= '<option'.Toolbox::concat_attrs($opt['attributes']).'>'.$opt['text'].'</option>';
			}
			$strret .= '</select>';
			return $strret;
		}
	}
}