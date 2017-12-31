/**
 * @file Simple AJAX Form
 * @description jQuery extension to make forms AJAX enabled. Mainly used in WordPress projects
 *
 * @author Gerkin, tcbarrett
 * @copyright 2016
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @package iThoughts-toolbox
 *
 */

'use strict';

/* globals jqForm: false */

const {
	w, $, $d, isNA, makeLoader,
} = require( 'ithoughts-core' );

const DEFAULT_FORM_OPTS = {
	validate: false,
};

$.fn.extend({
	/**
		 * Send a form through ajax.
   * 
		 * @function simpleAjaxForm
		 * @memberof	external:jQuery
		 * @param   {Object} opts - Options.........................................................................................................................................................................................
		 * @param   {boolean} [opts.validate=false] Options
		 * @param   {Function} [opts.callback=false] Options
		 * @returns {undefined}
		 */
	simpleAjaxForm( opts ) {
		var options  = $.extend( DEFAULT_FORM_OPTS, opts );
		this.each( function bindEach() {
			const $form = $( this );
			const formopts = $.extend({
				target:   $form.data( 'target' ),
				callback: $form.data( 'callback' ),
			}, options );
			if ( formopts.target && $( `#${ formopts.target }` ).length ) {
				$( `#${ formopts.target }` ).html( '' ).hide();
			}
			$form.find( 'button[name="actionB"]' ).click( function onClick() {
				$form.find( '[name="action"]' ).val( this.value );
			});
			const postText = ( this.getAttribute( 'post_text' ) ? this.getAttribute( 'post_text' ) : 'Updating, please wait...' );
			let loader;

			$form.ajaxForm({
				beforeSubmit() {
					if ( options.validate && !jqForm.valid()) {
						return false; 
					}
					if ( formopts.target && $( `#${ formopts.target }` ).length ) {
						$( `#${ formopts.target }` ).html( `<p>${  postText  }</p>` ).removeClass().addClass( 'clear updating' ).fadeTo( 100, 1 );
					}
					loader = makeLoader();
					return true;
				},
				error( error ) {
					loader.remove();
					if ( formopts.target ) {
						$( `#${ formopts.target }` ).removeClass().addClass( 'clear notice notice-error' ).html( '<p>Form submission failed.</p>' );
					}
					if ( 'function' == typeof formopts.error ) {
						formopts.error( error );
					}
				},
				success( responseText, statusText, xhr, jQForm ) {
					loader.remove();
					if ( 'undefined' === typeof ( jQForm )) {
						jQForm = xhr;
					}

					try {
						let res;
						if ( 'String' === responseText.constructor.name ) {
							res = JSON.parse( responseText );
						} else if ( 'Object' === responseText.constructor.name ) {
							res = responseText;
						} else {
							throw `Unhandled type ${  typeof responseText }`;
						}

						if ( '0' === res || !res ) {
							const str = 'Server did not respond anything';
							$( `#${ formopts.target }` ).removeClass().addClass( 'clear notice notice-warning' ).html( `<p>${ str }</p>` );

							if ( 'function' == typeof formopts.error ) {
								formopts.error( new Error( str ));
							}
						} else {
							if ( typeof res.success != 'undefined' && res.success != null && typeof res.data != 'undefined' && res.data != null ) { // handle wp_send_json_{success|error}
								res.data.valid = res.success;
								res = res.data;
							}
							// If a nonce refresh token is present, try to update it
							if ( res.nonce_refresh ) {
								$form.find( '[name="_wpnonce"]' ).val( res.nonce_refresh );
							}
							// Handle raw response
							if ( !res.valid ) {
								if ( formopts.target && $( `#${ formopts.target }` ).length ) {
									$( `#${ formopts.target }` ).removeClass().addClass( 'clear notice notice-error' ).html( res.text );
								}
							} else {
								if ( res.reload ) {
									let args;
									if ( w.location.href.includes( '?' )) {
										args = '&';
									} else {
										args = '?';
									}
									if ( res.hasOwnProperty( 'text' ) && !isNA( res.text )) {
										args += `json-res-txt=${  encodeURI( res.text ) }`;
									}
									w.location.href += args;
								}

								if ( formopts.target && $( `#${ formopts.target }` ).length ) {
									$( `#${ formopts.target }` ).removeClass().addClass( 'clear notice notice-success' ).html( res.text );
								}

								if ( res.redirect ) {
									if ( res.text ) {
										setTimeout(() => {
											w.location.href = res.redirect;
										}, 2500 );
									} else {
										w.location.href = res.redirect;
									}
								}
							}
							try {
								if ( 'function' == typeof $form[0].simple_ajax_callback ) {
									$form[0].simple_ajax_callback( res );
								}
								if ( 'function' == typeof formopts.callback ) {
									formopts.callback( res );
								}
							} catch ( error ) {
								$( `#${ formopts.target }` ).removeClass().addClass( 'clear notice notice-error' ).html( '<p>Error with received data</p>' );
								if ( 'function' == typeof formopts.error ) {
									formopts.error( error );
								}
							}
						}
					} catch ( error ) {
						$( `#${ formopts.target }` ).removeClass().addClass( 'clear notice notice-error' ).html( '<p>Invalid server response</p>' );
						if ( 'function' == typeof formopts.error ) {
							formopts.error( error );
						}
					}
				},
			});
		});
	},
});

$d.ready(() => {
	$( '.simpleajaxform' ).simpleAjaxForm();
});
