<?php
/**
 * CssEdit class file. Provides a serie of pre-prepared inputs properties
 *
 * @copyright 2015-2016 iThoughts Informatique
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.fr.html GPLv2
 * @package iThoughts\iThoughts WordPress Plugin Toolbox
 * @author Gerkin
 *         
 * @version 1.0
 */

namespace ithoughts\v1_0;

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

if(!class_exists(__NAMESPACE__."\\CssEdit")){
    /**
	 * Backbone used in all plugins. Should be inherited by Backbone's plugin
	 */
    class CssEdit extends \ithoughts\v1_0\Singleton{
        /**
         * Constructs a new CssEdit object. Should not be used, as every methods are static
         * @private
         */
        private function __construct(){
        }

        public static function getForm($property, $baseArray, $name, $id=NULL){
            $id = $id ? $id : $name;
            $reg = self::getRegexHelpers();

            $arr;
            switch($property){
                case "background-color":{
                    $arr = array(
                        "type" => "text",
                        "value" => null,
                        "id" => $id,
                        "attributes" => array(
                            "class" => "color-field",
                            "pattern" => "^{$reg["color"]}$"
                        )
                    );
                } break;

                case "padding":{
                    $arr = array(
                        "type" => "text",
                        "value" => null,
                        "id" => $id,
                        "attributes" => array(
                            "pattern" => "^((?:\s*{$reg["size"]}\s*){1,4}|{$reg["_defsize"]})$"
                        )
                    );
                } break;

                case "box-shadow":{
                    $arr = array(
                        "horizontal" => array(
                            "type" => "text",
                            "value" => null,
                            "id" => $id."[horizontal]",
                            "attributes" => array(
                                "pattern" => "^{$reg["sizen"]}$"
                            )
                        ),
                        "vertical" => array(
                            "type" => "text",
                            "value" => null,
                            "id" => $id."[vertical]",
                            "attributes" => array(
                                "pattern" => "^{$reg["sizen"]}$"
                            )
                        ),
                        "blur" => array(
                            "type" => "text",
                            "value" => null,
                            "id" => $id."[blur]",
                            "attributes" => array(
                                "pattern" => "^{$reg["size"]}$"
                            )
                        ),
                        "spread" => array(
                            "type" => "text",
                            "value" => null,
                            "id" => $id."[spread]",
                            "attributes" => array(
                                "pattern" => "^{$reg["size"]}$"
                            )
                        ),
                        "color" => array(
                            "type" => "text",
                            "value" => null,
                            "id" => $id."[color]",
                            "attributes" => array(
                                "class" => "color-field",
                                "pattern" => "^{$reg["color"]}$"
                            )
                        ),
                        "inset" => array(
                            "radio" => false,
                            "selected" => null,
                            "options" => array(
                                "inset" => array(
                                    "attributes" => array(
                                        "id" => $id."[inset]"
                                    )
                                )
                            ),
                        ),
                    );
                } break;

                case "border": {
                    $arr = array(
                        "width" => array(
                            "type" => "text",
                            "value" => null,
                            "id" => $id."[width]",
                            "attributes" => array(
                                "pattern" => "^{$reg["size"]}$"
                            )
                        ),
                        "style" => array(
                            "options" => array(
                                "solid",
                                "dotted",
                                "dashed",
                                "double",
                                "groove",
                                "ridge",
                                "inset",
                                "outset",
                                "none",
                                "hidden"
                            ),
                        ),
                        "color" => array(
                            "type" => "text",
                            "value" => null,
                            "id" => $id."[color]",
                            "attributes" => array(
                                "class" => "color-field",
                                "pattern" => "^{$reg["color"]}$"
                            )
                        )
                    );
                } break;

                case "font-size": {
                    $arr = array(
                        "type" => "text",
                        "value" => null,
                        "id" => $id,
                        "attributes" => array(
                            "pattern" => "^(medium|xx-small|x-small|small|large|x-large|xx-large|smaller|larger|{$reg["size"]})$"
                        )
                    );
                } break;

                case "line-height": {
                    $arr = array(
                        "type" => "text",
                        "value" => null,
                        "id" => $id,
                        "attributes" => array(
                            "pattern" => "^(medium|xx-small|x-small|small|large|x-large|xx-large|smaller|larger|{$reg["size"]})$"
                        )
                    );
                } break;

                case "color": {
                    return array(
                        "type" => "text",
                        "value" => null,
                        "id" => $id,
                        "attributes" => array(
                            "class" => "color-field",
                            "pattern" => "^{$reg["color"]}$"
                        )
                    );
                } break;

                case "font-weight": {
                    return array(
                        "options" => array(
                            "lighter",
                            "normal",
                            "bold",
                            "bolder",
                        ),
                    );
                } break;

                case "font-style": {
                    return array(
                        "options" => array(
                            "normal",
                            "italic",
                            "oblique",
                        ),
                    );
                } break;

                case "text-decoration": {
                    return array(
                        "options" => array(
                            "none",
                            "underline",
                            "overline",
                            "line-through"
                        ),
                    );
                } break;

                case "text-align": {
                    return array(
                        "options" => array(
                            "left",
                            "right",
                            "center",
                            "justify"
                        ),
                    );
                } break;
            }
            $merged = array_replace_recursive($arr,$baseArray);
            var_dump($merged);
            return $merged;
        }

        public static function getRegexHelpers(){
            $regexes = array(
                "color" => "(?:(?:#\w{3,6}|rgb(?:a\(\s*[.0-9]+\s*(?:\s*,\s*[.0-9]+){3}\)|\(\s*[.0-9]+\s*(?:\s*,\s*[.0-9]+){2}\)))*|\w{3,})",
                "eol" => "\s*;?\s*$",
                "size" => "(?:0|(?:\d*\.\d*|\d+)(?:r?em|px|%|ex|pt|(?:c|m)m|in|pc|v(?:h|w|min|max)))",
                "_defsize" => "initial|inherit"
            );
            $regexes["sizen"] = "(?:-?{$regexes["size"]})";
            $regexes["defsize"] = "(?:{$regexes["size"]}|{$regexes["_defsize"]})";
            $regexes["defsizen"] = "(?:-?{$regexes["size"]}|{$regexes["_defsize"]})";
            return $regexes;
        }
    }
}