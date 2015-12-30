<?php

if(!class_exists("ithoughts_toolbox")){
	class ithoughts_toolbox {
		public static function concat_attrs($attrs){
			$str = "";
			foreach($attrs as $key => $value)
				$str .= ' '.$key.'="'.$value.'"';
			return $str;
		}
		public static function generate_input_select($name, $options){
			$strret = '<select name="'.$name.'"';
			if(!isset($options["attributes"]))
				$options["attributes"] = array();
			if(!isset($options["attributes"]["id"]))
				$options["attributes"]["id"] = $name;

			$strret .= ithoughts_toolbox::concat_attrs($options["attributes"]);
			if(isset($options["multiple"]) && $options["multiple"])
				$strret .= " multiple";
			$strret .= ">";

			if(isset($options["options"]) && is_array($options["options"])){
				if(array_values($options["options"]) === $options["options"]){
					foreach($options["options"] as $value){
						$strret .= '<option value="'.$value.'">'.$value.'</option>';
					}
				} else {
					foreach($options["options"] as $key => $value){
						$strret .= '<option value="'.$key.'"';
						if(isset($options["selected"]) && ((is_array($options["selected"]) && in_array($key, $options["selected"])) || (!is_array($options["selected"]) && $options["selected"] == $key)))
							$strret .= ' selected="selected"';
						$strret .= '>';
						if(is_array($value)){
							if(isset($value["text"]) && $value["text"]){
								$strret .= $value["text"];
							} else {
								$strret .= $key;
							}
						} else {
							$strret .= $value;
						}
						$strret .= '</option>';
					}
				}
			}

			$strret .= "</select>";
			return $strret;
		}
		public static function generate_input_check($name, $options){
			$ret = array();
			if(!isset($options["options"]))
				return $ret;

			if(!is_array($options["options"]))
				$options["options"] = array($options["options"]);

			foreach($options["options"] as $option => $data){
				$str = '<input name="'.$name.'"';
				if(isset($options["radio"]) && $options["radio"])
					$str .= ' type="radio"';
				else
					$str .= ' type="checkbox"';
				$str .= ' value="'.$option.'"';
				if(!isset($data["attributes"]))
					$data["attributes"] = array();
				if(!isset($data["attributes"]["id"]))
					$data["attributes"]["id"] = $option;

				$str .= ithoughts_toolbox::concat_attrs($data["attributes"]);
				if(isset($options["selected"]) && ((is_array($options["selected"]) && in_array($option, $options["selected"])) || (!is_array($options["selected"]) && $options["selected"] == $option)))
					$str .= ' checked="checked"';
				$str .= '/>';

				$ret[$option] = $str;
			}
			return $ret;
		}
		function generate_input_color($name, $value){

		}
		public static function generate_input_text($name, $options){
			$str = '<input name="'.$name.'"';
			if(isset($options["type"]))
				$str .= ' type="'.$options["type"].'"';
			if(isset($options["value"]) && $options["value"] !== NULL && trim($options["value"]) != "")
				$str .= ' value="'.$options["value"].'"';

			if(!isset($options["attributes"]))
				$options["attributes"] = array();
			if(!isset($options["attributes"]["id"]))
				$options["attributes"]["id"] = $name;

			$str .= ithoughts_toolbox::concat_attrs($options["attributes"]);
			$str .= '/>';
			return $str;
		}
	}
}