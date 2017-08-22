<?php
/**
 * Toolbox class file
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

if(!class_exists(__NAMESPACE__."\\Toolbox")){
	/**
	 * General toolbox class used across all plugins
	 *
	 * @author Gerkin
	 */
	abstract class Toolbox {
		/**
		 * Concatenate attributes to generate a string to use in HTML tags
		 * @param string[] $attrs Associative array of key/values to concatenate
		 * @return string Concatenated attributes
		 *
		 * @author Gerkin
		 */
		public static final function concat_attrs($attrs){
			$str = '';
			foreach($attrs as $key => $value){
				if(isset($value) && $value !== NULL){
					$str .= ' '.esc_attr($key).'="'.esc_attr($value).'"';
				}
			}
			return $str;
		}

		/**
		 * Generate a select input
		 * @param string $name Name (and default id) of the input
		 * @param mixed $options Options for the input
		 * @return string Input generated
		 *
		 * @uses Toolbox::concat_attrs()
		 * @todo Describe options
		 * @author Gerkin
		 */
		public static final function generate_input_select($name, $options){
			$strret = '<select name="'.$name.'"';

			// Set defaults
			if(!isset($options['attributes']))
				$options['attributes'] = array();
			if(!isset($options['attributes']['id']))
				$options['attributes']['id'] = $name;
			if(!isset($options['attributes']['autocomplete']))
				$options['attributes']['autocomplete'] = 'off';
			if(isset($options['required']) && $options['required'])
				$options['attributes']['required'] = 'required';

			// Add attributes to the HTML tag
			$strret .= Toolbox::concat_attrs($options['attributes']);
			if(isset($options['multiple']) && $options['multiple'])
				$strret .= ' multiple';
			$strret .= '>';

			// Set behavior when nothing is selected
			if(isset($options['allow_blank'])){ // Blank is allowed, so it is the default if no selection
				if(!isset($options['selected']) || $options['selected'] == NULL || $options['selected'] == "" || $options['selected'] == array()) {
					$strret .= '<option value="" selected>'.$options['allow_blank'].'</option>';
				} else {
					$strret .= '<option value="">'.$options['allow_blank'].'</option>';
				}
			}

			// Print children options
			if(isset($options['options']) && is_array($options['options'])){
				if(array_values($options['options']) === $options['options']){
					// If pure array, value & label are identical
					foreach($options['options'] as $value){
						$strret .= '<option value="'.$value.'">'.$value.'</option>';
					}
				} else {
					// If associative array, array key is the value, and array value describe options
					foreach($options['options'] as $key => $value){
						$strret .= '<option value="'.$key.'" ';
						if(is_array($value)){
							// Attributes on each options
							if(!isset($value['attributes']))
								$value['attributes'] = array();
							$strret .= Toolbox::concat_attrs($value['attributes']);
						}
						// Select option if required
						if(isset($options['selected']) && ((is_array($options['selected']) && in_array($key, $options['selected'])) || (!is_array($options['selected']) && $options['selected'] == $key)))
							$strret .= ' selected="selected"';
						$strret .= '>';
						// Text of this option
						if(is_array($value)){
							if(isset($value['text']) && $value['text']){
								$strret .= $value['text'];
							} else {
								$strret .= $key;
							}
						} else {
							$strret .= $value;
						}
						// Close the tag
						$strret .= '</option>';
					}
				}
			}

			$strret .= '</select>';
			return $strret;
		}

		/**
		 * Generate one or several check/radio inputs
		 * @param string $name Name (and default base id) of the input(s)
		 * @param mixed $options Options for the input(s)
		 * @return string|string[] Input(s) generated
		 *
		 * @uses Toolbox::concat_attrs()
		 * @todo Describe options
		 * @author Gerkin
		 */
		public static final function generate_input_check($name, $options){
			$ret = array();
			$base_id = preg_replace("/[^\w\d_]/", "", $name);
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
			return $ret;
		}

		/**
		 * Generate a color input
		 * @ignore
		 * @param string $name Name (and default id) of the input
		 * @param mixed[] $options Options for the input
		 * @return string Input generated
		 *
		 * @uses Toolbox::concat_attrs()
		 * @todo Describe options
		 * @author Gerkin
		 */
		public static final function generate_input_color($name, $value){

		}

		/**
		 * Generate a text input
		 * @param string $name Name (and default id) of the input
		 * @param mixed[] $options Options for the input
		 * @return string Input generated
		 *
		 * @uses Toolbox::concat_attrs()
		 * @todo Describe options
		 * @author Gerkin
		 */
		public static final function generate_input_text($name, $options){
			$str;
			if(isset($options["textarea"]) && $options["textarea"]){
				$str = '<textarea';
			} else {
				$str = '<input';
			}

			$attrs = array(
				"name" => $name
			);
			if(!isset($options["attributes"]))
				$options["attributes"] = array();
			if(!isset($options["attributes"]["id"]))
				$options["attributes"]["id"] = $name;
			if(isset($options["textarea"]) && $options["textarea"] == false){
				unset($options["attributes"]["autocomplete"]);
			} else {
				if(!isset($options["attributes"]["autocomplete"]) || $options["attributes"]["autocomplete"] == false)
					$options["attributes"]["autocomplete"] = "off";
				else
					unset($options["attributes"]["autocomplete"]);
			}
			if(isset($options["required"]) && $options["required"])
				$options["attributes"]["required"] = "required";
			$attrs = array_merge($attrs,$options["attributes"]);


			if(isset($options["value"]) && $options["value"] !== NULL){
				$options["value"] = strval($options["value"]);
			} else {
				$options["value"] = NULL;
			}

			if(isset($options["textarea"]) && $options["textarea"]){
				$str .= Toolbox::concat_attrs($attrs);
				$str .= '>';
				$options["value"] = esc_html($options["value"]);
				if($options["value"] !== NULL && trim($options["value"]) != "")
					$str .= $options["value"];
				$str .= "</textarea>";
			} else {
				$options["value"] = esc_attr($options["value"]);
				if(isset($options["type"]))
					$attrs["type"] = $options["type"];
				if($options["value"] !== NULL && trim($options["value"]) != "")
					$attrs["value"] = $options["value"];
				$str .= Toolbox::concat_attrs($attrs);
				$str .= '/>';
			}
			return $str;
		}

		/**
		 * Replace all characters of set `$from` by those in `$to` set.
		 * By default, this function behave to return the unaccented equivalent of the input string.
		 * @param string $text String to transform
		 * @param string $from (optional) Characters to replace
		 * @param string $to (optional) Replacement set
		 * @param string [$encoding = 'UTF-8'] Encoding used for replacement
		 * @uses mb_strpos mb_strpos for handling multiple encodings
		 * @return string Encoded string ready to be put into attribute
		 *
		 * @author Gerkin
		 */
		public static final function unaccent( $text, $from = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ", $to = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn", $encoding = "UTF-8" ){
			$l = mb_strlen($text, $encoding);
			$out = "";
			for($i = 0; $i < $l; $i++){
				$c = mb_substr($text, $i, 1, $encoding);
				$t = mb_strpos($from,$c,0,$encoding);
				if($t === false)
					$out .= $c;
				else
					$out .= mb_substr($to, $t, 1, $encoding);
			}
			return $out;
		}

		/**
		 * Decode JSON attribute to array.
		 * Encoding an object this way allow you to encode JSON to put it into HTML attributes. `"` are replaced by `&aquot;`. Note the `a` to avoid conflict with HTML entities
		 * @param string $str String to decode
		 * @param bool $ampEncoded (optional) Decode JSON encoded like with encode_json_attribute in form if true or escaped by TinyMCE if false
		 * @return string Encoded string ready to be put into attribute
		 *
		 * @see Toolbox::encode_json_attr()
		 * @todo Check behavior and usage to better explain usage
		 * @author Gerkin
		 */
		public static final function decode_json_attr($str, $ampEncoded = false){
			if($ampEncoded)
				return json_decode(str_replace("&amp;aquot;", '"', $str), true);
			else
				return json_decode(str_replace('\\"', '"', $str), true);
		}

		/**
		 * Encode array to string attribute
		 * Encoding an array this way allow you to encode JSON to put it into HTML attributes with special html-entity like escaping. `"` are replaced by `&aquot;`. Note the `a` to avoid conflict with HTML entities
		 * @param mixed $array Array to encode
		 * @return string Encoded string ready to be put into attribute
		 *
		 * @author Gerkin
		 */
		public static final function encode_json_attr($array){
			return str_replace('"', "&aquot;", json_encode($array));
		}

		/**
		 * Reduce a nested array to a single level array
		 * <p>
		 * Reduce a nested array (associative or simple) to a single level array. Simple arrays will be unwrapped and stacked at the same position. In associative array, the less deep key is kept.
		 * <pre><code>Toolbox::array_flatten(array(array(1,2),array(3,4))); // will output array(1,2,3,4)
		 * Toolbox::array_flatten(array(1 => 1,2 => array(1 => "A",3 => 3)));	// will output array(1 => 1, 3 => 3)
		 * </code></pre>
		 * @param mixed[] $array    The multilevel array to flatten
		 * @return mixed[]    Return a single level array
		 *
		 * @author Gerkin
		 */
		public static final function array_flatten($array) {
			$return = array();
			foreach ($array as $key => $value) {
				if (is_array($value)){
					$return = array_merge(Toolbox::array_flatten($value),$return);
				} else {
					$return[$key] = $value;
				}
			}

			return $return;
		}

		/**
		 * Conver a checkbox value to a boolean that indicates if it was checked
		 * @param mixed[] $values    The associative array supposed to contain the key $key
		 * @param string  $key       The key to check. Usually, it is the name of the input
		 * @param string|string[]  $truevalues The value of the checkbox
		 * @return bool    Returns true if the checkbox was checked
		 *
		 * @author Gerkin
		 */
		public static final function checkbox_to_bool($values,$key, $truevalues){
			if(is_array($truevalues)){
				$ret = array();
				if(!isset($values[$key])){
					foreach($truevalues as $truevalue){
						$ret[$truevalue] = false;
					}
				} else {
					if(!is_array($values[$key]))
						$values[$key] = array($values[$key]);
					foreach($truevalues as $truevalue){
						$ret[$truevalue] = in_array($truevalue, $values[$key]);
					}
				}
				return $ret;
			} else {
				if(!isset($values[$key])){
					return false;
				} else {
					return $values[$key] === $truevalues;
				}
			}
		}

		/**
		 * Generates the permalink for given post type depending on $post
		 * @param  \WP_Post $post				The      light post
		 * @param  {string} [$post.post_name]	The post_name (slug) of the post we generate permalink for
		 * @param  {string} [$post.ID]	        Id of the post
		 * @param  string   $post_type			The   post type of the given post
		 * @return string   The post permalink
		 *
		 * @since 1.2
		 * @author Gerkin
		 */
		public static function get_permalink_light(\WP_Post $post, $post_t){
			global $wp_rewrite;

			$post_link = $wp_rewrite->get_extra_permastruct($post_t);

			$post_type = get_post_type_object($post_t);
			if ( $post_type->hierarchical ) {
				$slug = get_page_uri( $post->ID );
			} else {
				$slug = $post->post_name;
			}
			if ( !empty($post_link)) {
				$post_link = str_replace("%$post_t%", $slug, $post_link);
				$post_link = home_url( user_trailingslashit($post_link) );
			} else {
				if ( $post_type->query_var )
					$post_link = add_query_arg($post_type->query_var, $slug, '');
				else
					$post_link = add_query_arg(array('post_type' => $post_type, 'p' => $post->ID), '');
				$post_link = home_url($post_link);
			}

			return $post_link;
		}

		public static function randomString($len = 5){
			return substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", $len)), 0, $len);
		}

		/**
		 * Check if $string ends with $test
		 * @param  string  $string Checked string
		 * @param  string  $test   String to search
		 * @return boolean True if $test is present at the end of $string
		 *
		 * @link https://stackoverflow.com/questions/619610/whats-the-most-efficient-test-of-whether-a-php-string-ends-with-another-string
		 * @since 5.0.0
		 * @author Gerkin
		 */
		public static function endswith($string, $test) {
			$strlen = strlen($string);
			$testlen = strlen($test);
			if ($testlen > $strlen) return false;
			return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
		}

		/**
		 * Join paths segments
		 * @param string $path,... Segments of path to join
		 * @return string Resulting paths
		 *
		 * @link https://stackoverflow.com/questions/1091107/how-to-join-filesystem-path-strings-in-php#answer-15575293
		 * @since 5.0.0
		 * @author Gerkin
		 */
		public static function join_paths($base) {
			$paths = array();

			$args = func_get_args();
			array_shift($args);
			foreach ($args as $arg) {
				if ($arg !== '') { $paths[] = $arg; }
			}

			$tail = preg_replace('#/+#','/',join('/', $paths));
			$result;
			if(Toolbox::endswith($base, '/')){
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
	}
}
