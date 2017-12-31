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
const isNA = value => {
	return null === value || 'undefined' === typeof value;
};

const splitPath = path => {
	if ( 'string' === typeof path ) {
		path = path.split( '.' );
	}
	if ( !( path instanceof Array )) {
		return false;
	}
	path = path.filter( segment => segment.length > 0 );
	return path;
};

const get = ( obj, path, defaultVal ) => {
	path = splitPath( path );
	if ( !path ) {
		return defaultVal;
	}
	const pathLen = path.length;
	for ( let i = 0; i < pathLen; i++ ) {
		if ( hop( obj, path[i])) {
			obj = obj[path[i]];
		} else {
			return defaultVal;
		}
	}
	return defaultVal;
};

const set = ( obj, path, val ) => {
	path = splitPath( path );
	if ( !path ) {
		return;
	}
	const pathLen = path.length;
	for ( let i = 0; i < pathLen - 1; i++ ) {
		if ( !hop( obj, path[i])) {
			obj[path[i]] = {};
		}
		obj = obj[path[i]];
	}
	obj[path[pathLen - 1]] = val;
};

const deepClone = obj => {
	let newT;
	if ( !isNA( obj )) {
		switch ( obj.constructor ) {
			case Object: {
				newT = {};
				for ( let i in obj ) {
					if ( hop( obj, i )) {
						newT[i] = deepClone( obj[i]);
					}
				}
				return newT;
			}

			case Array: {
				newT = [];
				for ( let i in obj ) {
					if ( hop( obj, i )) {
						newT[i] = deepClone( obj[i]);
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

const w = window;
const d = w.document;
let $;
let $d;
let $w;


// From https://stackoverflow.com/a/6060215/4839162:
const $merge = ( ...ArrayOfJqueryObjects ) => $( $.map( ArrayOfJqueryObjects, el => el.get()));



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
const waitFor = ( scope, prop, every, callback ) => {
	if ( 'function' == typeof every ) {
		callback = every;
		every = 100;
	}
	waitUntil( callback, () => hop( scope, prop ), every, false );
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
const merge = ( ...objects ) => {
	let newObj = null;

	const recurse = ( obj1, obj2 ) => {
		let newT;
		if ( !isNA( obj2 )) {
			switch ( obj2.constructor ) {
				case Object: {
					if ( !isNA( obj1 ) && obj1.constructor === Object ) {
						newT = deepClone( obj1 );
					} else {
						newT = {};
					}
					for ( const i of obj2 ) {
						newT[i] = recurse( newT[i], obj2[i]);
					}
					return newT;
				}

				case Array: {
					if ( !isNA( obj1 ) && obj1.constructor === Array ) {
						newT = deepClone( obj1 );
					} else {
						newT = [];
					}
					for ( const i of obj2 ) {
						newT[i] = recurse( newT[i], obj2[i]);
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
	for ( const j of objects ) {
		const curObj = objects[j];
		if ( !isNA( curObj ) && ( curObj.constructor === Object || curObj.constructor === Array )) {
			newObj = recurse( newObj, curObj );
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
const hop = ( object, property ) => {
	return object instanceof Object && object.hasOwnProperty( property );
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
const waitUntil = ( fct, until, every, max = false ) => {
	if ( isNA( until ) || until.constructor.name !== 'Function' ) {
		throw TypeError( 'Calling "Function.waitUntil" without test function. Call setTimeout instead' );
	}

	setTimeout(() => {
		until() || ( max !== false && max < 1 ) ? fct() : waitUntil( fct, until, every, max ? max - every : max );
	}, every );
};

if ( w.jQuery ) {
	$ = w.jQuery;
	$d = $( d );
	$w = $( w );
}

const userAgent = get( w, 'navigator.userAgent', '' );
const isIos = userAgent.match( /(iPad|iPhone|iPod)/g ); // Used to enable some iOS specific piece of code to catch click on body, for example
const baseTouch = ( isIos || userAgent.match( /(Android|webOS|BlackBerry)/i )) ? 1 : 0;


const initLoggers = (() => {
	const generateLogArray = ( prefix, mode, args ) => {
		args.unshift( `${ mode  }` );
		args.unshift( `${ prefix  } => ` );
		return args;
	};
	const modes = [ 'error', 'warn', 'info', 'log' ];
	const modesLen = modes.length;
	return ( pluginCore, textPrefix, verbosity ) => {
		if ( 'string' == typeof verbosity ) {
			verbosity = parseInt( verbosity );
		}

		for ( let i = 0; i < modesLen; i++ ) {
			const mode = modes[i];
			if ( !hop( pluginCore, mode )) {
				pluginCore[mode] = verbosity > i ? (( modeIn, ...args ) => {
					console[modeIn].apply( null, generateLogArray( textPrefix, modeIn.toUpperCase(), args ));
				}).bind( null, mode ) : () => {};
			}
		}
	};
})();
const makeLoader = () => {
	const loader = $( $.parseHTML( '<div class="ithoughts_tt_gl-loader" data-loader-status="shown"><div class="centerer"><div class="loader"></div></div></div>' ));
	loader.find( '.loader,.loader:before,.loader:after' ).css({
		borderTopColor: $( '#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, #adminmenu li.current a.menu-top, .folded #adminmenu li.wp-has-current-submenu, .folded #adminmenu li.current.menu-top, #adminmenu .wp-menu-arrow, #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head, #adminmenu .wp-menu-arrow div,#publish' ).css( 'background-color' ),
	});
	loader.find( '.centerer' ).css({
		backgroundColor: $( '#wpadminbar' ).css( 'background-color' ),
	});
	$( 'body' ).append( loader );
	return loader;
};

module.exports = {
	isNA,
	get,
	set,
	deepClone,
	$merge,
	waitFor,
	merge,
	hop,
	waitUntil,
	initLoggers,
	makeLoader,
	d,
	w,
	$,
	$d,
	$w,
	isIos,
	baseTouch,
};
