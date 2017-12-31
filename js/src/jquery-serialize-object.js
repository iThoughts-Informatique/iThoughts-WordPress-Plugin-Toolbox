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

const ithoughts = require( './ithoughts-core.js' );

/*jslint plusplus: true*/

const {$} = ithoughts;

/**
	 * Send a form through ajax
	 * @method	external:"jQuery".fn.serializeObject
	 * @returns {object}	Associative object representing the form
	 */
$.fn.extend({
	serializeObject() {
		const json = {};
		$.map( $( this ).serializeArray(), formvalue => {
			const isNested = formvalue.name.contains( '[' );
			const fvn = formvalue.value || '';
			if ( isNested ) {
				let jsonLocal = json;
				const nestings = formvalue.name.replace( /\]/gi, '' ).split( '[' );
				const nestingsCount = nestings.length;
				for ( let i = 0; i < nestingsCount; i++ ) {
					if ( i === nestingsCount - 1 ) {
						if ( nestings[i] !== '' ) {
							if ( jsonLocal[nestings[i]]) {
								if ( 'string' === typeof jsonLocal[nestings[i]]) {
									jsonLocal[nestings[i]] = [ jsonLocal[nestings[i]] ];
								}
								jsonLocal[nestings[i]].push( fvn );
							} else {
								jsonLocal[nestings[i]] = fvn;
							}
						} else {
							jsonLocal.push( fvn );
						}
					} else {
						jsonLocal = jsonLocal[nestings[i]] = jsonLocal[nestings[i]] || ( '' === nestings[i + 1] ? [] : {});
					}
				}
			} else if ( json[formvalue.name] !== undefined ) {
				if ( !json[formvalue.name].push ) {
					json[formvalue.name] = [ json[formvalue.name] ];
				}
				json[formvalue.name].push( fvn );
			} else {
				json[formvalue.name] = fvn;
			}
		});
		return json;
	},
	serializeInputsObject( forceValue ) {
		const value = {};
		this.each(( index, element ) => {
			const name = element.name;
			const nestings = name.replace( /\]/gi, '' ).split( '[' );
			
			const valueThis = {};
			const nestingsCount = nestings.length;
			let valuePtr = valueThis;
			for ( let i = 0; i < nestingsCount; i++ ) {
				if ( i === nestingsCount - 1 ) {
					valuePtr[nestings[i]] = typeof forceValue !== 'undefined' ? forceValue : this.value;
				} else {
					valuePtr = ( valuePtr[nestings[i]] = {});
				}
			}
			$.extend( value, valueThis );
		});
		return value;
	},
});
