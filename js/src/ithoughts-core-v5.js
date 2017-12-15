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
 * @description iThoughts v5 Helpers
 */

'use strict';

if ( 'undefined' == typeof iThoughts )	{
	window.iThoughts = {};
}

( s => {
	/**
	 * @function isNA
	 * @description Tests if the value is null or undefined
	 * @author Gerkin
	 * @param {*} value The value to test
	 * @returns {Boolean} Returns true if `value` is null or undefined
	 */
	const isNA = value => {
		return null === value || 'undefined' === typeof value;
	};

	if ( isNA( s.v5 ))		{
		s.v5 = {};
	}	else		{
		return;
	}

	var v5 = s.v5,
		d = document,
		w = window,
		//		el = Element,
		et = typeof EventTarget != 'undefined' && !isNA( EventTarget ) ? EventTarget : document.createDocumentFragment().constructor,
		dc = v5.deepClone = ( obj ) => {
			let newT;
			if ( !isNA( obj )) {
				switch ( obj.constructor ) {
					case Object: {
						newT = {};
						for ( let i in obj ) {
							if ( v5.hop( obj, i )) {
								newT[i] = dc( obj[i]);
							}
						}
						return newT;
					}

					case Array: {
						newT = [];
						for ( let i in obj ) {
							if ( v5.hop( obj, i )) {
								newT[i] = dc( obj[i]);
							}
						}
						return newT;
					}

					default: {
						return obj;
					}
				}
			} else {
				return obj;
			}
		};
	/**
	 * @function isNA
	 * @description Check if value is null or undefined
	 * @author Gerkin
	 * @memberof iThoughts.v5
	 * @instance
	 * @param {*} value The value tu check
	 * @returns {boolean} True if null or undefined, false otherwise
	 */
	v5.isNA = isNA;


	// From https://stackoverflow.com/a/6060215/4839162:
	v5.$merge = ( ...ArrayOfJqueryObjects ) => $( $.map( ArrayOfJqueryObjects, el => el.get()));



	/**
	 * @function waitFor
	 * @description Look into `scope` for property `prop` every `every`ms, then execute `callback` when the property exists
	 * @author Gerkin
	 * @memberof iThoughts.v5
	 * @instance
	 * @param {*} scope The parent scope to check for property in
	 * @param {string} prop Name of the property to wait for
	 * @param {integer} [every] Time in ms between each checks
	 * @param {function} callback Function to execute once property exists
	 * @returns {undefined} Async
	 */
	v5.waitFor = ( scope, prop, every, callback ) => {
		if ( 'function' == typeof every ) {
			callback = every;
			every = 100;
		}
		v5.waitUntil( callback, () => v5.hop( scope, prop ), every, false );
	};

	/**
	 * @function mergeRecursive
	 * @description Combine each object from left to right, keeping the left-most value
	 * @author Gerkin
	 * @memberof iThoughts.v5
	 * @instance
	 * @param {...(object|array)} objects Any number of objects/arrays to merge
	 * @returns {boolean} True if null or undefined, false otherwise
	 */
	v5.mergeRecursive = ( ...objects ) => {
		var newObj = null,
			j,
			curObj,
			recurse = ( obj1, obj2 ) => {
				var i,
					newT;

				if ( !isNA( obj2 )) {
					switch ( obj2.constructor ) {
						case Object: {
							if ( !isNA( obj1 ) && obj1.constructor === Object ) {
								newT = dc( obj1 );
							} else {
								newT = {};
							}
							for ( i in obj2 ) {
								if ( v5.hop( obj2, i )) {
									newT[i] = recurse( newT[i], obj2[i]);
								}
							}
							return newT;
						}

						case Array: {
							if ( !isNA( obj1 ) && obj1.constructor === Array ) {
								newT = dc( obj1 );
							} else {
								newT = [];
							}
							for ( i in obj2 ) {
								if ( v5.hop( obj2, i )) {
									newT[i] = recurse( newT[i], obj2[i]);
								}
							}
							return newT;
						}

						default: {
							return obj2;
						}
					}
				} else {
					return obj2;
				}
			};
		for ( j in objects ) {
			curObj = objects[j];
			if ( !isNA( curObj ) && ( curObj.constructor === Object || curObj.constructor === Array )) {
				newObj = recurse( newObj, curObj );
			}
		}
		return newObj;
	};

	v5.docWidth = () => {
		return w.innerWidth ||
			w.documentElement.clientWidth ||
			w.body.clientWidth ||
			w.body.offsetWidth;
	};

	v5.docHeight = () => {
		return w.innerHeight ||
			w.documentElement.clientHeight ||
			w.body.clientHeight ||
			w.body.offsetHeight;
	};

	/**
	 * @function gei
	 * @description Minification shorthand for {@link https://developer.mozilla.org/en-US/docs/Web/API/Document/getElementById Document.getElementById}
	 * @author Gerkin
	 * @memberof iThoughts.v5
	 * @instance
	 * @param {string} s The id of the searched element
	 * @param {Element} [e=document] Element to search in
	 * @returns {Element|null} The Element, or null if not found
	 * @see https://developer.mozilla.org/en-US/docs/Web/API/Document/getElementById
	 */
	v5.gei = ( s, e ) => {
		return ( e||d ).getElementById( s );
	};

	/**
	 * @function qs
	 * @description Minification shorthand for {@link https://developer.mozilla.org/en-US/docs/Web/API/Element/querySelector Element.querySelector}
	 * @author Gerkin
	 * @memberof iThoughts.v5
	 * @instance
	 * @param {string} s The selector of the searched element
	 * @param {Element} [e=document] Element to search in
	 * @returns {Element|null} The Element, or null if not found
	 * @see https://developer.mozilla.org/en-US/docs/Web/API/Element/querySelector
	 */
	v5.qs = ( s, e ) => {
		return ( e||d ).querySelector( s );
	};

	/**
	 * @function qsa
	 * @description Minification shorthand for {@link https://developer.mozilla.org/en-US/docs/Web/API/Element/querySelectorAll Element.querySelectorAll}
	 * @author Gerkin
	 * @memberof iThoughts.v5
	 * @instance
	 * @param {string} s The selector of the searched element
	 * @param {Element} [e=document] Element to search in
	 * @returns {NodeList} The NodeList containing every elements matching the selector
	 * @see https://developer.mozilla.org/en-US/docs/Web/API/Element/querySelectorAll
	 */
	v5.qsa = ( s, e ) => {
		return ( e||d ).querySelectorAll( s );
	};

	/**
	 * @function geiN
	 * @description Like {@link Document.gei}, but returns an empty object instead of null to allow 1lvl attribute definition without tests
	 * @author Gerkin
	 * @memberof iThoughts.v5
	 * @instance
	 * @param {string} s The selector of the searched element
	 * @param {Element} [e=document] Element to search in
	 * @returns {Element|{}} The Element, or an empty object if not found
	 */
	v5.geiN = ( s, e ) => {
		return v5.gei( s, e ) || {};
	};

	/**
	 * @function qsN
	 * @description Like {@link Element.qsN}, but returns an empty object instead of null to allow 1lvl attribute definition without tests
	 * @author Gerkin
	 * @memberof iThoughts.v5
	 * @instance
	 * @param {string} s The selector of the searched element
	 * @param {Element} [e=document] Element to search in
	 * @returns {Element|{}} The Element, or an empty object if not found
	 */
	v5.qsN = ( s, e ) => {
		return v5.qs( s, e ) || {};
	};

	/**
	 * @function hop
	 * @description Minification shorthand for {@link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/hasOwnProperty Object.hasOwnProperty}
	 * @author Gerkin
	 * @memberof iThoughts.v5
	 * @instance
	 * @param {Object} s The object to check
	 * @param {string} v The name of the attribute
	 * @returns {Boolean} Returns the same than {@link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/hasOwnProperty Object.hasOwnProperty}
	 * @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/hasOwnProperty
	 */
	v5.hop = ( s, v ) => {
		return s.hasOwnProperty( v );
	};

	/**
	 * @function waitUntil
	 * @description Minification shorthand for {@link HTMLDocument}.getElementById
	 * @author Gerkin
	 * @memberof iThoughts.v5
	 * @instance
	 * @param {Function} fct Function executed on resolution.
	 * @param {Function} until Function executed on each loop.
	 * @param {Number} every Time to wait between each test.
	 * @param {Number|false} [max=false]  Time after which `fct` will be executed even if `until` still returns false. Set it to false to not set max timeout
	 * @returns {undefined}
	 */
	v5.waitUntil = ( fct, until, every, max = false ) => {
		if ( isNA( until ) || until.constructor.name !== 'Function' ) {
			throw TypeError( 'Calling "Function.waitUntil" without test function. Call setTimeout instead' );
		}

		setTimeout(() => {
			until() || ( max !== false && max < 1 ) ? fct() : v5.waitUntil( fct, until, every, max ? max - every : max );
		}, every );
	};


	/**
	 * @function on
	 * @description Bind events with specified functions on specified elements
	 * @memberof iThoughts.v5
	 * @alias iThoughts.v5.attachEvent
	 * @param {EventTarget|EventTarget[]}					a	EventTarget to bind
	 * @param {string|string[]}					b	Events to bind
	 * @param {EventFunction|EventFunction[]}	c	Functions to attach
	 * @returns {undefined}
	 * @since 0.1.0
	 */
	v5.on = v5.attachEvent = ( a, b, c ) => {
		/**
		 * @function _on
		 * @description Same as {@link EventTarget#on}
		 * @alias EventTarget.on
		 * @param {Element}			s Subject of the binding
		 * @param {string}			e Event to bind
		 * @param {EventFunction}	f Function to attach
		 * @returns {undefined}
		 * @private
		 * @see iThoughts.v5#on
		 * @since 0.1.0
		 */
		function _on( s, e, f ) {
			e && f && ( s.addEventListener || s.attachEvent ).call( s, e, f );
		}
		if ( isNA( a ) || a.constructor.name !== 'Array' ) {
			a = [ a ];
		}
		if ( isNA( b ) || b.constructor.name !== 'Array' ) {
			b = [ b ];
		}
		if ( isNA( c ) || c.constructor.name !== 'Array' ) {
			c = [ c ];
		}
		var i = 0, j = 0, k = 0, I = a.length, J = b.length, K = c.length;
		for ( i = 0; i < I; i++ ) {
			for ( j = 0; j < J; j++ ) {
				for ( k = 0; k < K; k++ ) {
					a[i] instanceof et && _on( a[i], b[j], c[k]);
				}
			}
		}
	};

	/**
	 * @function off
	 * @description Unbind events with specified functions on specified elements
	 * @memberof iThoughts.v5
	 * @alias iThoughts.v5.detachEvent
	 * @param {EventTarget|EventTarget[]}					a	EventTarget to unbind
	 * @param {string|string[]}					b	Events to unbind
	 * @param {EventFunction|EventFunction[]}	c	Functions to detach
	 * @returns {undefined}
	 * @since 0.1.0
	 */
	v5.off = v5.detachEvent = ( a, b, c ) => {
		/**
		 * @function _off
		 * @description Same as {@link EventTarget#off}
		 * @param {Element}			s Subject of the unbinding
		 * @param {string}			e Event to unbind
		 * @param {EventFunction}	f Function to detach
		 * @returns {undefined}
		 * @private
		 * @see iThoughts.v5#off
		 * @since 0.1.0
		 */
		function _off( s, e, f ) {
			e && f && ( s.removeEventListener || s.detachEvent ).call( s, e, f );
		}
		if ( isNA( a ) || a.constructor.name !== 'Array' ) {
			a = [ a ];
		}
		if ( isNA( b ) || b.constructor.name !== 'Array' ) {
			b = [ b ];
		}
		if ( isNA( c ) || c.constructor.name !== 'Array' ) {
			c = [ c ];
		}
		var i = 0, j = 0, k = 0, I = a.length, J = b.length, K = c.length;
		for ( i = 0; i < I; i++ ) {
			for ( j = 0; j < J; j++ ) {
				for ( k = 0; k < K; k++ ) {
					a[i] instanceof et && _off( a[i], b[j], c[k]);
				}
			}
		}
	};

	/**
	 * @function go
	 * @description Unbind events with specified functions on specified elements
	 * @memberof iThoughts.v5
	 * @alias iThoughts.v5.triggerEvent
	 * @param {EventTarget|EventTarget[]}					a	EventTarget to trigger event on
	 * @param {string|string[]}					b	Name of the events
	 * @returns {undefined}
	 * @since 0.1.0
	 */
	v5.go = v5.triggerEvent = ( a, b ) => {
		/**
		 * @function _go
		 * @description Same as {@link EventTarget#go}
		 * @param {Element}			s Subject of the trigger
		 * @param {string}			b Event name
		 * @returns {undefined}
		 * @private
		 * @see iThoughts.v5#go
		 * @since 0.1.0
		 */
		function _go( s, b ) {
			let e;
			if ( b ) {
				if ( d.createEvent ) {
					e = new Event( b );
					s.dispatchEvent( e );
				} else {
					e = d.createEventObject();
					s.fireEvent( `on${  b }`, e );
				}
			}
		}
		if ( isNA( a ) || a.constructor.name !== 'Array' ) {
			a = [ a ];
		}
		if ( isNA( b ) || b.constructor.name !== 'Array' ) {
			b = [ b ];
		}
		for ( var i = 0, I = a.length; i < I; i++ ) {
			for ( var j = 0, J = b.length; j < J; j++ ) {
				a[i] instanceof et && _go( a[i], b[j]);
			}
		}
	};

	if ( window.jQuery ) {
		var $ = v5.$ = window.jQuery;
		v5.$d = $( document );
		v5.$w = $( window );
	}

	v5.isIos = navigator.userAgent.match( /(iPad|iPhone|iPod)/g ); // Used to enable some iOS specific piece of code to catch click on body, for example
	v5.baseTouch = ( v5.isIos || navigator.userAgent.match( /(Android|webOS|BlackBerry)/i )) ? 1 : 0;


	v5.initLoggers = (() => {
		const generateLogArray = ( prefix, mode, args ) => {
			args.unshift( `${ mode  }` );
			args.unshift( `${ prefix  } => ` );
			return args;
		};
		return ( pluginCore, textPrefix, verbosity ) => {
			if ( 'string' == typeof verbosity ) {
				verbosity = parseInt( verbosity );
			}

			var modes = [ 'error', 'warn', 'info', 'log' ],
				mode;
			for ( var i = 0, I = modes.length; i < I; i++ ) {
				mode = modes[i];
				if ( !pluginCore.hasOwnProperty( mode )) {
					pluginCore[mode] = verbosity > i ? (( modeIn, ...args ) => {
						console[modeIn].apply( null, generateLogArray( textPrefix, modeIn.toUpperCase(), args ));
					}).bind( null, mode ) : () => {};
				}
			}
		};
	})();
	v5.makeLoader = () => {
		var loader = $( $.parseHTML( '<div class="ithoughts_tt_gl-loader" data-loader-status="shown"><div class="centerer"><div class="loader"></div></div></div>' ));
		loader.find( '.loader,.loader:before,.loader:after' ).css({
			borderTopColor: $( '#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, #adminmenu li.current a.menu-top, .folded #adminmenu li.wp-has-current-submenu, .folded #adminmenu li.current.menu-top, #adminmenu .wp-menu-arrow, #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head, #adminmenu .wp-menu-arrow div,#publish' ).css( 'background-color' ),
		});
		loader.find( '.centerer' ).css({
			backgroundColor: $( '#wpadminbar' ).css( 'background-color' ),
		});
		$( 'body' ).append( loader );
		return loader;
	};
})( iThoughts );
