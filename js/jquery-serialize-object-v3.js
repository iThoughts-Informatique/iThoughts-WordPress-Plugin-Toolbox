/**
 * @file jQuery extension to help serializing forms
 *
 * @author Gerkin
 * @copyright 2016 
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @package iThoughts-toolbox
 *
 */

(function (ithoughts) {
	'use strict';

	/*jslint plusplus: true*/
	/*globals Ithoughts*/

	var $ = ithoughts.$;

	/**
     * Send a form through ajax
	 * @method	external:"jQuery".fn.serializeObject
     * @returns {object}	Associative object representing the form
     */
	$.fn.extend({
		serializeObject: function () {
			var json = {};
			$.map($(this).serializeArray(), function (formvalue) {
				var nestingIndex = formvalue.name.indexOf('['),
					nestingsCount,
					jsonLocal,
					nestings,
					i,
					fvn = formvalue.value || '';
				if (nestingIndex > -1) {
					jsonLocal = json;
					nestings = formvalue.name.replace(/\]/gi, '').split('[');
					for (i = 0, nestingsCount = nestings.length; i < nestingsCount; i++) {
						if (i === nestingsCount - 1) {
							if(nestings[i] != ""){
								if (jsonLocal[nestings[i]]) {
									if (typeof jsonLocal[nestings[i]] === 'string') {
										jsonLocal[nestings[i]] = [jsonLocal[nestings[i]]];
									}
									jsonLocal[nestings[i]].push(fvn);
								} else {
									jsonLocal[nestings[i]] = fvn;
								}
							} else {
								jsonLocal.push(fvn);
							}
						} else {
							jsonLocal = jsonLocal[nestings[i]] = jsonLocal[nestings[i]] || (nestings[i + 1] === "" ? [] : {});
						}
					}
				} else {
					if (json[formvalue.name] !== undefined) {
						if (!json[formvalue.name].push) {
							json[formvalue.name] = [json[formvalue.name]];
						}
						json[formvalue.name].push(fvn);
					} else {
						json[formvalue.name] = fvn;
					}
				}
			});
			return json;
		},
		serializeInputsObject: function (forceValue) {
			var value = {};
			this.each(function(){
				var valueThis = {},
					valuePtr = valueThis,
					name = this.name,
					nestings = name.replace(/\]/gi, '').split('['),
					i = 0,
					nestingsCount = nestings.length;
				for (i; i < nestingsCount; i++) {
					if (i === nestingsCount - 1) {
						valuePtr[nestings[i]] = typeof forceValue !== "undefined" ? forceValue : this.value;
					} else {
						valuePtr = (valuePtr[nestings[i]] = {});
					}
				}
				$.extend(value, valueThis);
			});
			return value;
		}
	});
}(Ithoughts.v4));