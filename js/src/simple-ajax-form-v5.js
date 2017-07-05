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

( ithoughts => {

	var $ = ithoughts.$;
	$.fn.extend({
		/**
		 * Send a form through ajax
		 * @function simpleAjaxForm
		 * @memberof	external:jQuery
		 * @param   {object} opts Options
		 * @param   {boolean} [opts.validate=false] Options
		 * @param   {function} [opts.callback=false] Options
		 * @returns {undefined}
		 */
		simpleAjaxForm( opts ) {
			var defaults = {
				validate: false,
			};
			var options  = $.extend( defaults, opts );
			this.each( function bindEach() {
				var $form = $( this ),
					formopts = $.extend({
						target:   $form.data( 'target' ),
						callback: $form.data( 'callback' ),
					}, options );
				if ( formopts.target && $( `#${ formopts.target }` ).length ) {
					$( `#${ formopts.target }` ).html( '' ).hide();
				}
				$form.find( 'button[name="actionB"]' ).click( function onClick() {
					$form.find( '[name="action"]' ).val( this.value );
				});
				var postText = ( this.getAttribute( 'post_text' ) ? this.getAttribute( 'post_text' ) : 'Updating, please wait...' ),
					loader;

				$form.ajaxForm({
					beforeSubmit() {
						//if( !jqForm.valid() ) return false;
						if ( formopts.target && $( `#${ formopts.target }` ).length ) {
							$( `#${ formopts.target }` ).html( `<p>${  postText  }</p>` ).removeClass().addClass( 'clear updating' ).fadeTo( 100, 1 );
						}
						loader = ithoughts.makeLoader();
						return true;
					},
					error() {
						loader.remove();
						$( `#${ formopts.target }` ).removeClass().addClass( 'clear notice notice-error' ).html( '<p>Form submission failed.</p>' );
					},
					success( responseText, statusText, xhr, jQForm ) {
						loader.remove();
						if ( 'undefined' === typeof( jQForm )) {
							jQForm = xhr;
						}

						try {
							var res;
							if ( 'String' === responseText.constructor.name ) {
								res = JSON.parse( responseText );
							} else if ( 'Object' === responseText.constructor.name ) {
								res = responseText;
							} else {
								throw `Unhandled type ${  typeof responseText }`;
							}

							if ( '0' === res || !res ) {
								$( `#${ formopts.target }` ).removeClass().addClass( 'clear notice notice-warning' ).html( '<p>Server did not respond anything</p>' );
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
										window.location.href = `${ window.location.href  }&json-res-txt=${  window.encodeURI( res.text ) }`;
									}

									if ( formopts.target && $( `#${ formopts.target }` ).length ) {
										$( `#${ formopts.target }` ).removeClass().addClass( 'clear notice notice-success' ).html( res.text );
									}

									if ( res.redirect ) {
										if ( res.text ) {
											setTimeout(() => {
												window.location.href = res.redirect;
											}, 2500 );
										} else {
											window.location.href = res.redirect;
										}
									}
								}
								try {
									if ( 'function' == typeof $form[0].simple_ajax_callback )										{
										$form[0].simple_ajax_callback( res );
									}
									if ( 'function' == typeof formopts.callback )										{
										formopts.callback( res );
									}
								} catch ( e ) {
									$( `#${ formopts.target }` ).removeClass().addClass( 'clear notice notice-error' ).html( '<p>Error with received data</p>' );
									console.error( e );
								}
							}
						} catch ( e ) {
							$( `#${ formopts.target }` ).removeClass().addClass( 'clear notice notice-error' ).html( '<p>Invalid server response</p>' );
						}
					},
				});
			});
		},
	});

	ithoughts.$d.ready(() => {
		$( '.simpleajaxform' ).simpleAjaxForm();
	});
})( iThoughts.v5 );
