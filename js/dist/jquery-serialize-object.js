(function (global, factory) {
	if (typeof define === "function" && define.amd) {
		define(['./ithoughts-core.js'], factory);
	} else if (typeof exports !== "undefined") {
		factory(require('./ithoughts-core.js'));
	} else {
		var mod = {
			exports: {}
		};
		factory(global.ithoughtsCore);
		global.jquerySerializeObject = mod.exports;
	}
})(this, function (ithoughts) {
	/**
  * @file jQuery extension to help serializing forms
  *
  * @author Gerkin
  * @copyright 2016
  * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
  * @package iThoughts-toolbox
  *
  */

	'use strict';

	var $ = ithoughts.$;


	/**
 	 * Send a form through ajax
 	 * @method	external:"jQuery".fn.serializeObject
 	 * @returns {object}	Associative object representing the form
 	 */
	$.fn.extend({
		serializeObject: function serializeObject() {
			var json = {};
			$.map($(this).serializeArray(), function (formvalue) {
				var isNested = formvalue.name.contains('[');
				var fvn = formvalue.value || '';
				if (isNested) {
					var jsonLocal = json;
					var nestings = formvalue.name.replace(/\]/gi, '').split('[');
					var nestingsCount = nestings.length;
					for (var i = 0; i < nestingsCount; i++) {
						if (i === nestingsCount - 1) {
							if (nestings[i] !== '') {
								if (jsonLocal[nestings[i]]) {
									if ('string' === typeof jsonLocal[nestings[i]]) {
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
							jsonLocal = jsonLocal[nestings[i]] = jsonLocal[nestings[i]] || ('' === nestings[i + 1] ? [] : {});
						}
					}
				} else if (json[formvalue.name] !== undefined) {
					if (!json[formvalue.name].push) {
						json[formvalue.name] = [json[formvalue.name]];
					}
					json[formvalue.name].push(fvn);
				} else {
					json[formvalue.name] = fvn;
				}
			});
			return json;
		},
		serializeInputsObject: function serializeInputsObject(forceValue) {
			var _this = this;

			var value = {};
			this.each(function (index, element) {
				var name = element.name;
				var nestings = name.replace(/\]/gi, '').split('[');

				var valueThis = {};
				var nestingsCount = nestings.length;
				var valuePtr = valueThis;
				for (var i = 0; i < nestingsCount; i++) {
					if (i === nestingsCount - 1) {
						valuePtr[nestings[i]] = typeof forceValue !== 'undefined' ? forceValue : _this.value;
					} else {
						valuePtr = valuePtr[nestings[i]] = {};
					}
				}
				$.extend(value, valueThis);
			});
			return value;
		}
	});
});
//# sourceMappingURL=jquery-serialize-object.js.map
