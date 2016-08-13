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
 * @namespace iThoughts.v3
 * @@memberof iThoughts
 * @description iThoughts v3 Helpers
 */

if(typeof Ithoughts == "undefined")
	var Ithoughts = {};

(function(s){
	'use strict';

	/**
     * @function isNA
     * @description Tests if the value is null or undefined
     * @author Gerkin
     * @param value The value to test
     * @returns {Boolean} Returns true if `value` is null or undefined
     */
	function isNA(value) {return value === null || typeof value === "undefined";}

	if(isNA(s.v3))
		s.v3 = {};
	else
		return;

	var v3 = s.v3,
		d = document,
		w = window,
		el = Element,
		et = typeof EventTarget != "undefined" && !isNA(EventTarget) ? EventTarget : document.createDocumentFragment().constructor,
		dc = v3.deepClone = function(obj){
			var newT,
				i;
			if(!isNA(obj)){
				switch(obj.constructor){
					case Object:{
						newT = {};
						for(var i in obj){
							if(hop(obj, i)){
								newT[i] = dc(obj[i]);
							}
						}
						return newT;
					} break;

					case Array:{
						newT = [];
						for(var i in obj){
							if(hop(obj, i)){
								newT[i] = dc(obj[i]);
							}
						}
						return newT;
					} break;

					default:{
						return obj;
					}
				}
			} else {
				return obj;
			}
		}
	/**
     * @function isNA
     * @description Check if value is null or undefined
     * @author Gerkin
     * @memberof iThoughts.v3
     * @instance
     * @param {*} value The value tu check
     * @returns {boolean} True if null or undefined, false otherwise
     */
	v3.isNA = isNA;



	/**
     * @function waitFor
     * @description Look into `scope` for property `prop` every `every`ms, then execute `callback` when the property exists
     * @author Gerkin
     * @memberof iThoughts.v3
     * @instance
     * @param {*} scope The parent scope to check for property in
     * @param {string} prop Name of the property to wait for
     * @param {integer} [every] Time in ms between each checks
     * @param {function} callback Function to execute once property exists
     * @returns {undefined} Async
     */
	v3.waitFor = function(scope, prop, every, callback){
		if(typeof every == "function"){
			callback = every;
			every = undefined;
		}
		if(typeof scope != "object" || typeof prop != "string" || (typeof every == "number" && typeof callback != "function") || typeof callback != "function"){
			throw TypeError("\"waitFor\" expects following types combinations:\n" +
							"\t{Object} scope\, {String} prop, {Number} every, {Function} callback\n" +
							"\t{Object} scope\, {String} prop, {Function} callback");
		}
		if(hop(scope, prop)){
			callback();
		} else {
			timer = setInterval(function(){
				if(hop(scope, prop)){
					clearInterval(timer);
					callback();
				}
			}, every || 100);
		}
		var timer = null;
	}

	/**
     * @function mergeRecursive
     * @description Combine each object from left to right, keeping the left-most value
     * @author Gerkin
     * @memberof iThoughts.v3
     * @instance
     * @param {...(object|array)} objects Any number of objects/arrays to merge
     * @returns {boolean} True if null or undefined, false otherwise
     */
	v3.mergeRecursive = function(/* Any number of Object/Array */) {
		var newObj = null,
			j,
			curObj,
			recurse = function(obj1, obj2){
				var i,
					val,
					newT;

				if(!isNA(obj2)){
					switch(obj2.constructor){
						case Object:{
							if(!isNA(obj1) && obj1.constructor == Object){
								newT = dc(obj1);
							} else {
								newT = {};
							}
							for(i in obj2){
								if(hop(obj2, i)){
									val = obj2[i];
									newT[i] = recurse(newT[i], obj2[i]);
								}
							}
							return newT;
						}break;

						case Array:{
							if(!isNA(obj1) && obj1.constructor == Array){
								newT = dc(obj1);
							} else {
								newT = [];
							}
							for(i in obj2){
								if(hop(obj2, i)){
									val = obj2[i];
									newT[i] = recurse(newT[i], obj2[i]);
								}
							}
							return newT;
						}break;

						default:{
							return obj2;
						}
					}
				} else {
					return obj2;
				}
			};
		for(j in arguments){
			curObj = arguments[j];
			if(!isNA(curObj) && (curObj.constructor == Object || curObj.constructor == Array)){
				newObj = recurse(newObj, curObj);
			}
		}
		return newObj;
	}

	v3.docWidth = function(){
		return w.innerWidth ||
			w.documentElement.clientWidth ||
			w.body.clientWidth ||
			w.body.offsetWidth;
	}
	v3.docHeight = function(){
		return w.innerHeight ||
			w.documentElement.clientHeight ||
			w.body.clientHeight ||
			w.body.offsetHeight;
	}
	/**
     * @function gei
     * @description Minification shorthand for {@link https://developer.mozilla.org/en-US/docs/Web/API/Document/getElementById Document.getElementById}
     * @author Gerkin
     * @memberof iThoughts.v3
     * @instance
     * @param {string} s The id of the searched element
     * @returns {Element|null} The Element, or null if not found
     * @see https://developer.mozilla.org/en-US/docs/Web/API/Document/getElementById
     */
	v3.gei = function(s,e) {return (e||d).getElementById(s); }
	/**
     * @function qs
     * @description Minification shorthand for {@link https://developer.mozilla.org/en-US/docs/Web/API/Element/querySelector Element.querySelector}
     * @author Gerkin
     * @memberof iThoughts.v3
     * @instance
     * @param {string} s The selector of the searched element
     * @returns {Element|null} The Element, or null if not found
     * @see https://developer.mozilla.org/en-US/docs/Web/API/Element/querySelector
     */
	v3.qs = function(s,e) {return (e||d).querySelector(s); }
	/**
     * @function qsa
     * @description Minification shorthand for {@link https://developer.mozilla.org/en-US/docs/Web/API/Element/querySelectorAll Element.querySelectorAll}
     * @author Gerkin
     * @memberof iThoughts.v3
     * @instance
     * @param {string} s The selector of the searched element
     * @returns {NodeList} The NodeList containing every elements matching the selector
     * @see https://developer.mozilla.org/en-US/docs/Web/API/Element/querySelectorAll
     */
	v3.qsa = function(s,e) {return (e||d).querySelectorAll(s); }
	/**
     * @function geiN
     * @description Like {@link Document.gei}, but returns an empty object instead of null to allow 1lvl attribute definition without tests
     * @author Gerkin
     * @memberof iThoughts.v3
     * @instance
     * @param {string} s The selector of the searched element
     * @returns {Element|{}} The Element, or an empty object if not found
     */
	v3.geiN = function(s,e) {return gei(s,e) || {}; }
	/**
     * @function qsN
     * @description Like {@link Element.qsN}, but returns an empty object instead of null to allow 1lvl attribute definition without tests
     * @author Gerkin
     * @memberof iThoughts.v3
     * @instance
     * @param {string} s The selector of the searched element
     * @returns {Element|{}} The Element, or an empty object if not found
     */
	v3.qsN = function(s,e) {return qs(s,e) || {}; }
	/**
     * @function hop
     * @description Minification shorthand for {@link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/hasOwnProperty Object.hasOwnProperty}
     * @author Gerkin
     * @memberof iThoughts.v3
     * @instance
     * @param {string} v The name of the attribute
     * @returns {Boolean} Returns the same than {@link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/hasOwnProperty Object.hasOwnProperty}
     * @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/hasOwnProperty
     */
	v3.hop = function(s,v) {return s.hasOwnProperty(v); }
	/**
     * @function waitUntil
     * @description Minification shorthand for {@link HTMLDocument}.getElementById
     * @author Gerkin
     * @memberof iThoughts.v3
     * @instance
     * @param {Function} until Function executed on each loop.
     * @param {Number} every Time to wait between each test.
     * @param {Number|false} [max=false]  Time after which `this` will be executed even if `until` still returns false. Set it to false to not set max timeout
     * @param s Minification helper. Do not use
     */
	v3.waitUntil = function(fct, until, every, max){
		if(isNA(until) || until.constructor.name !== "Function")
			throw TypeError('Calling "Function.waitUntil" without test function. Call setTimeout instead');

		max = !isNA(max) && !isNaN(parseInt(max)) ? parseInt(max) : false;
		setTimeout(function(){
			until() || (max !== false && max < 1) ? fct() : waitUntil(fct, until, every, max ? max - every : max)
		},every);
	}


	/**
     * @function on
     * @description Bind events with specified functions on specified elements
	 * @memberof iThoughts.v3
	 * @alias iThoughts.v3.attachEvent
     * @param {EventTarget|EventTarget[]}					a	EventTarget to bind
     * @param {string|string[]}					b	Events to bind
     * @param {EventFunction|EventFunction[]}	c	Functions to attach
     * @since 0.1.0
     */
	function on(a, b, c) {
		/**
         * @function _on
         * @description Same as {@link EventTarget#on}
         * @alias EventTarget.on
         * @param {string}			e Event to bind
         * @param {EventFunction}	f Function to attach
         * @private
         * @see iThoughts.v3#on
         * @since 0.1.0
         */
		function _on(s, e, f) {
			var i = e && f && (s.addEventListener || s.attachEvent).call(s, e, f);
		}
		if (isNA(a) || a.constructor.name !== "Array") {a = [a]; }
		if (isNA(b) || b.constructor.name !== "Array") {b = [b]; }
		if (isNA(c) || c.constructor.name !== "Array") {c = [c]; }
		var i = 0, j = 0, k = 0, I = a.length, J = b.length, K = c.length;
		for (i = 0; i < I; i++) { for (j = 0; j < J; j++) { for (k = 0; k < K; k++) {
			a[i] instanceof et && _on(a[i], b[j], c[k]);
		} } }
	}
	v3.on = v3.attachEvent = on;

	/**
     * @function off
     * @description Unbind events with specified functions on specified elements
	 * @memberof iThoughts.v3
	 * @alias iThoughts.v3.detachEvent
     * @param {EventTarget|EventTarget[]}					a	EventTarget to unbind
     * @param {string|string[]}					b	Events to unbind
     * @param {EventFunction|EventFunction[]}	c	Functions to detach
     * @since 0.1.0
     */
	function off(a, b, c) {
		/**
         * @function _off
         * @description Same as {@link EventTarget#off}
         * @param {string}			e Event to unbind
         * @param {EventFunction}	f Function to detach
         * @private
         * @see iThoughts.v3#off
         * @since 0.1.0
         */
		function _off(s, e, f) {
			var i = e && f && (s.removeEventListener || s.detachEvent).call(s, e, f);
		}
		if (isNA(a) || a.constructor.name !== "Array") {a = [a]; }
		if (isNA(b) || b.constructor.name !== "Array") {b = [b]; }
		if (isNA(c) || c.constructor.name !== "Array") {c = [c]; }
		var i = 0, j = 0, k = 0, I = a.length, J = b.length, K = c.length;
		for (i = 0; i < I; i++) {for (j = 0; j < J; j++) {for (k = 0; k < K; k++) {
			a[i] instanceof et && _off(a[i], b[j], c[k]);
		} } }
	}
	v3.off = v3.detachEvent = off;

	/**
     * @function go
     * @description Unbind events with specified functions on specified elements
	 * @memberof iThoughts.v3
	 * @alias iThoughts.v3.triggerEvent
     * @param {EventTarget|EventTarget[]}					a	EventTarget to trigger event on
     * @param {string|string[]}					b	Name of the events
     * @since 0.1.0
     */
	function go(a, b) {
		/**
         * @function _go
         * @description Same as {@link EventTarget#go}
         * @param {string}			b Event name
         * @param e Minification helper. Do not use
         * @private
         * @see iThoughts.v3#go
         * @since 0.1.0
         */
		function _go(s, b, e) {
			if (b) {
				if (d.createEvent) {
					e = new Event(b);
					s.dispatchEvent(e);
				} else {
					e = d.createEventObject();
					s.fireEvent("on" + b, e);
				}
			}
		}
		if (isNA(a) || a.constructor.name !== "Array") {a = [a]; }
		if (isNA(b) || b.constructor.name !== "Array") {b = [b]; }
		var i = 0, j = 0, k = 0, I = a.length, J = b.length;
		for (i = 0; i < I; i++) { for (j = 0; j < J; j++) {
			a[i] instanceof et && _go(a[i], b[j]);
		} }
	}
	v3.go = v3.triggerEvent = go;

	if(jQuery){
		v3.$ = jQuery;
		v3.$d = v3.$(document);
		v3.$w = v3.$(window);
	}

	v3.isIos = navigator.userAgent.match(/(iPad|iPhone|iPod)/g); // Used to enable some iOS specific piece of code to catch click on body, for example
	v3.baseTouch = (v3.isIos || navigator.userAgent.match(/(Android|webOS|BlackBerry)/i) ) ? 1 : 0;
}(Ithoughts));