<?php
/**
 * Toolbox class file
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
			$attrs = array_filter($attrs, '\ithoughts\v6_0\Toolbox::filter_attrs');
			ksort($attrs);
			$str = '';
			foreach($attrs as $key => $value){
				if($value === true){
					$str .= ' '.esc_attr($key);
				} else {
					$str .= ' '.esc_attr($key).'="'.esc_attr($value).'"';
				}
			}
			return $str;
		}

		public static function filter_attrs($value){
			$ret = !(NULL === $value || false === $value);
			return $ret;
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
		 * Check if $string starts with $test
		 * @param  string  $string Checked string
		 * @param  string  $test   String to search
		 * @return boolean True if $test is present at the start of $string
		 *
		 * @link https://stackoverflow.com/questions/619610/whats-the-most-efficient-test-of-whether-a-php-string-ends-with-another-string
		 * @since 5.0.0
		 * @author Gerkin
		 */
		public static function startswith($string, $test) {
			$strlen = strlen($string);
			$testlen = strlen($test);
			if ($testlen > $strlen) return false;
			return substr_compare($string, $test, 0, $testlen) === 0;
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

		public static function sort_options($options, $sort_sets, &$unsorted = false){
			// Define our output array
			$sorted = array();
			foreach($sort_sets as $set_name => $set_content){
				$sorted[$set_name] = array();
			}
			// Sort all options
			foreach($options as $option => $value){
				// If `$unsorted` is an array, maybe we'll append the option to the unsorted array
				$was_unsorted = is_array($unsorted);
				foreach($sort_sets as $set_name => $set_content){
					if(in_array($option, $set_content, true)){
						$sorted[$set_name][$option] = $value;
						$was_unsorted = false;
					}
				}
				if($was_unsorted){
					$unsorted[$option] = $value;
				}
			}
			return $sorted;
		}

		public static function maybe_data_prefix($attr_name){
			static $html_attributes = NULL;
			// Load attributes list if required.
			if($html_attributes === NULL){
				$html_attributes = require(dirname(__FILE__).'/data-list.php');
			}
			// Prepend with prefix if required
			if(!self::startswith($attr_name, 'data-') && !in_array($attr_name, $html_attributes, true)){
				return "data-$attr_name";
			}
			return $attr_name;
		}

		public static function array_keys_exists(array $keys, array $arr) {
			return !array_diff_key(array_flip($keys), $arr);
		}
	}
}
