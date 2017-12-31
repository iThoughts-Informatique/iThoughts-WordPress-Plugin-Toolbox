(function (global, factory) {
	if (typeof define === "function" && define.amd) {
		define(['module'], factory);
	} else if (typeof exports !== "undefined") {
		factory(module);
	} else {
		var mod = {
			exports: {}
		};
		factory(mod);
		global.ithoughtsCore = mod.exports;
	}
})(this, function (module) {
	/**
  * @file Minify compliant generic shorthands
  *
  * @author Alexandre Germain
  * @copyright 2016 iThoughts informatique
  * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
  * @package iThoughts-toolbox
  *
  */

	/**
  * @namespace iThoughts
  * @description iThoughts Helpers versions
  */
	/**
  * @namespace iThoughts.v5
  * @@memberof iThoughts
  * @description iThoughts Helpers
  */

	'use strict';

	/**
  * @function isNA
  * @description Check if value is null or undefined
  * @author Gerkin
  * @memberof iThoughts
  * @instance
  * @param {*} value - The value tu check.
  * @returns {boolean} True if null or undefined, false otherwise.
  */

	var isNA = function isNA(value) {
		return null === value || 'undefined' === typeof value;
	};

	var splitPath = function splitPath(path) {
		if ('string' === typeof path) {
			path = path.split('.');
		}
		if (!(path instanceof Array)) {
			return false;
		}
		path = path.filter(function (segment) {
			return segment.length > 0;
		});
		return path;
	};

	var get = function get(obj, path, defaultVal) {
		path = splitPath(path);
		if (!path) {
			return defaultVal;
		}
		var pathLen = path.length;
		for (var i = 0; i < pathLen; i++) {
			if (hop(obj, path[i])) {
				obj = obj[path[i]];
			} else {
				return defaultVal;
			}
		}
		return defaultVal;
	};

	var set = function set(obj, path, val) {
		path = splitPath(path);
		if (!path) {
			return;
		}
		var pathLen = path.length;
		for (var i = 0; i < pathLen - 1; i++) {
			if (!hop(obj, path[i])) {
				obj[path[i]] = {};
			}
			obj = obj[path[i]];
		}
		obj[path[pathLen - 1]] = val;
	};

	var deepClone = function deepClone(obj) {
		var newT = void 0;
		if (!isNA(obj)) {
			switch (obj.constructor) {
				case Object:
					{
						newT = {};
						for (var i in obj) {
							if (hop(obj, i)) {
								newT[i] = deepClone(obj[i]);
							}
						}
						return newT;
					}

				case Array:
					{
						newT = [];
						for (var _i in obj) {
							if (hop(obj, _i)) {
								newT[_i] = deepClone(obj[_i]);
							}
						}
						return newT;
					}

				default:
					{
						return obj;
					}
			}
		} else {
			return obj;
		}
	};

	var w = window;
	var d = w.document;
	var $ = void 0;
	var $d = void 0;
	var $w = void 0;

	// From https://stackoverflow.com/a/6060215/4839162:
	var $merge = function $merge() {
		for (var _len = arguments.length, ArrayOfJqueryObjects = Array(_len), _key = 0; _key < _len; _key++) {
			ArrayOfJqueryObjects[_key] = arguments[_key];
		}

		return $($.map(ArrayOfJqueryObjects, function (el) {
			return el.get();
		}));
	};

	/**
  * @function waitFor
  * @description Look into `scope` for property `prop` every `every`ms, then execute `callback` when the property exists
  * @author Gerkin
  * @memberof iThoughts.v5
  * @instance
  * @param {*} scope - The parent scope to check for property in.
  * @param {string} prop - Name of the property to wait for.
  * @param {integer} [every] - Time in ms between each checks.
  * @param {Function} callback - Function to execute once property exists.
  * @returns {undefined} Async.
  */
	var waitFor = function waitFor(scope, prop, every, callback) {
		if ('function' == typeof every) {
			callback = every;
			every = 100;
		}
		waitUntil(callback, function () {
			return hop(scope, prop);
		}, every, false);
	};

	/**
  * @function merge
  * @description Combine each object from left to right, keeping the left-most value
  * @author Gerkin
  * @memberof iThoughts.v5
  * @instance
  * @param {...(object|array)} objects - Any number of objects/arrays to merge.
  * @returns {boolean} True if null or undefined, false otherwise.
  */
	var merge = function merge() {
		for (var _len2 = arguments.length, objects = Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
			objects[_key2] = arguments[_key2];
		}

		var newObj = null;

		var recurse = function recurse(obj1, obj2) {
			var newT = void 0;
			if (!isNA(obj2)) {
				switch (obj2.constructor) {
					case Object:
						{
							if (!isNA(obj1) && obj1.constructor === Object) {
								newT = deepClone(obj1);
							} else {
								newT = {};
							}
							var _iteratorNormalCompletion = true;
							var _didIteratorError = false;
							var _iteratorError = undefined;

							try {
								for (var _iterator = obj2[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
									var i = _step.value;

									newT[i] = recurse(newT[i], obj2[i]);
								}
							} catch (err) {
								_didIteratorError = true;
								_iteratorError = err;
							} finally {
								try {
									if (!_iteratorNormalCompletion && _iterator.return) {
										_iterator.return();
									}
								} finally {
									if (_didIteratorError) {
										throw _iteratorError;
									}
								}
							}

							return newT;
						}

					case Array:
						{
							if (!isNA(obj1) && obj1.constructor === Array) {
								newT = deepClone(obj1);
							} else {
								newT = [];
							}
							var _iteratorNormalCompletion2 = true;
							var _didIteratorError2 = false;
							var _iteratorError2 = undefined;

							try {
								for (var _iterator2 = obj2[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
									var _i2 = _step2.value;

									newT[_i2] = recurse(newT[_i2], obj2[_i2]);
								}
							} catch (err) {
								_didIteratorError2 = true;
								_iteratorError2 = err;
							} finally {
								try {
									if (!_iteratorNormalCompletion2 && _iterator2.return) {
										_iterator2.return();
									}
								} finally {
									if (_didIteratorError2) {
										throw _iteratorError2;
									}
								}
							}

							return newT;
						}

					default:
						{
							return obj2;
						}
				}
			} else {
				return obj2;
			}
		};
		var _iteratorNormalCompletion3 = true;
		var _didIteratorError3 = false;
		var _iteratorError3 = undefined;

		try {
			for (var _iterator3 = objects[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
				var j = _step3.value;

				var curObj = objects[j];
				if (!isNA(curObj) && (curObj.constructor === Object || curObj.constructor === Array)) {
					newObj = recurse(newObj, curObj);
				}
			}
		} catch (err) {
			_didIteratorError3 = true;
			_iteratorError3 = err;
		} finally {
			try {
				if (!_iteratorNormalCompletion3 && _iterator3.return) {
					_iterator3.return();
				}
			} finally {
				if (_didIteratorError3) {
					throw _iteratorError3;
				}
			}
		}

		return newObj;
	};

	/**
  * @function hop
  * @description Minification shorthand for {@link https://developer.Mozilla.Org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/hasOwnProperty Object.hasOwnProperty}
  * @author Gerkin
  * @memberof iThoughts.v5
  * @instance
  * @param {Object} object The object to check.
  * @param {string} property The name of the attribute.
  * @returns {boolean} Returns the same than {@link https://developer.Mozilla.Org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/hasOwnProperty Object.hasOwnProperty}
  * @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/hasOwnProperty
  */
	var hop = function hop(object, property) {
		return object instanceof Object && object.hasOwnProperty(property);
	};

	/**
 	 * @function waitUntil
 	 * @description Minification shorthand for {@link HTMLDocument}.getElementById
 	 * @author Gerkin
 	 * @memberof iThoughts.v5
 	 * @instance
 	 * @param {Function} fct - Function executed on resolution.
 	 * @param {Function} until - Function executed on each loop.
 	 * @param {number} every - Time to wait between each test.
 	 * @param {Number|false} [max=false]  - Time after which `fct` will be executed even if `until` still returns false. Set it to false to not set max timeout.
 	 * @returns {undefined}
 	 */
	var waitUntil = function waitUntil(fct, until, every) {
		var max = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;

		if (isNA(until) || until.constructor.name !== 'Function') {
			throw TypeError('Calling "Function.waitUntil" without test function. Call setTimeout instead');
		}

		setTimeout(function () {
			until() || max !== false && max < 1 ? fct() : waitUntil(fct, until, every, max ? max - every : max);
		}, every);
	};

	if (w.jQuery) {
		$ = w.jQuery;
		$d = $(d);
		$w = $(w);
	}

	var userAgent = get(w, 'navigator.userAgent', '');
	var isIos = userAgent.match(/(iPad|iPhone|iPod)/g); // Used to enable some iOS specific piece of code to catch click on body, for example
	var baseTouch = isIos || userAgent.match(/(Android|webOS|BlackBerry)/i) ? 1 : 0;

	var initLoggers = function () {
		var generateLogArray = function generateLogArray(prefix, mode, args) {
			args.unshift('' + mode);
			args.unshift(prefix + ' => ');
			return args;
		};
		var modes = ['error', 'warn', 'info', 'log'];
		var modesLen = modes.length;
		return function (pluginCore, textPrefix, verbosity) {
			if ('string' == typeof verbosity) {
				verbosity = parseInt(verbosity);
			}

			for (var i = 0; i < modesLen; i++) {
				var mode = modes[i];
				if (!hop(pluginCore, mode)) {
					pluginCore[mode] = verbosity > i ? function (modeIn) {
						for (var _len3 = arguments.length, args = Array(_len3 > 1 ? _len3 - 1 : 0), _key3 = 1; _key3 < _len3; _key3++) {
							args[_key3 - 1] = arguments[_key3];
						}

						console[modeIn].apply(null, generateLogArray(textPrefix, modeIn.toUpperCase(), args));
					}.bind(null, mode) : function () {};
				}
			}
		};
	}();
	var makeLoader = function makeLoader() {
		var loader = $($.parseHTML('<div class="ithoughts_tt_gl-loader" data-loader-status="shown"><div class="centerer"><div class="loader"></div></div></div>'));
		loader.find('.loader,.loader:before,.loader:after').css({
			borderTopColor: $('#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, #adminmenu li.current a.menu-top, .folded #adminmenu li.wp-has-current-submenu, .folded #adminmenu li.current.menu-top, #adminmenu .wp-menu-arrow, #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head, #adminmenu .wp-menu-arrow div,#publish').css('background-color')
		});
		loader.find('.centerer').css({
			backgroundColor: $('#wpadminbar').css('background-color')
		});
		$('body').append(loader);
		return loader;
	};

	module.exports = {
		isNA: isNA,
		get: get,
		set: set,
		deepClone: deepClone,
		$merge: $merge,
		waitFor: waitFor,
		merge: merge,
		hop: hop,
		waitUntil: waitUntil,
		initLoggers: initLoggers,
		makeLoader: makeLoader,
		d: d,
		w: w,
		$: $,
		$d: $d,
		$w: $w,
		isIos: isIos,
		baseTouch: baseTouch
	};
});
//# sourceMappingURL=ithoughts-core.js.map
