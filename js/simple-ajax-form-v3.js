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
(function(ithoughts){
    'use strict';
    
    var $ = ithoughts.$;
	$.fn.extend({
		/**
         * Send a form through ajax
		 * @method	external:"jQuery".fn.simpleAjaxForm
         * @param   {object} opts Options to be merged with { validate: false } and jQuery elements selected attributes ["target","callback","validate"]
         * @returns {array}	Forms handlers
         */
		simpleAjaxForm: function( opts ){
			var defaults = { validate: false };
			var options  = $.extend( defaults, opts );
			return this.each(function(){
				var $form = $(this);
				var formopts = $.extend({
					target:   $form.data('target'),
					callback: $form.data('callback'),
				}, options);
				if( formopts.target && $('#'+formopts.target).length ){
					$('#'+formopts.target).html('').hide();
				}
				$form.find("button[name=\"actionB\"]").click(function(){
					$form.find("[name=\"action\"]").val(this.getAttribute("value"));
				});
				var post_text = (this.getAttribute("post_text") ? this.getAttribute("post_text") : 'Updating, please wait...');

				$form.ajaxForm({
					beforeSubmit: function(formData, jqForm, options) {
						//if( !jqForm.valid() ) return false;
						if( formopts.target && $('#'+formopts.target).length ){
							$('#'+formopts.target).html('<p>' + post_text + '</p>').removeClass().addClass('clear updating').fadeTo(100,1);
						}
						return true;
					},
					error: function(){
						$('#'+formopts.target).removeClass().addClass('clear notice notice-error').html('<p>Form submission failed.</p>');
					},
					success: function(responseText, statusText, xhr, jQForm){
						if( typeof(jQForm) === 'undefined' )
							jQForm = xhr;

						if( typeof(jQForm) === 'undefined' ){
							$('#'+formopts.target).removeClass().addClass('clear notice notice-error').html(res.text);
							$form.append('<div class="error"><p>Cannot handle response properly</p></div>');
							return;
						}

						try{
							var res;
							if(responseText.constructor.name == "String"){
								res = JSON.parse(responseText);
							} else if(responseText.constructor.name == "Object"){
								res = responseText;
							} else {
								throw "Unhandled type " + typeof responseText;
							}

							if(res == "0" || !res){
								$('#'+formopts.target).removeClass().addClass('clear notice notice-warning').html("<p>Server did not respond anything</p>");
							} else {
								if(typeof res.success != "undefined" && res.success != null && typeof res.data != "undefined" && res.data != null){ // handle wp_send_json_{success|error}
									res.data.valid = res.success;
									res = res.data
								}
								// Handle raw response
								if(!res.valid){
									if( formopts.target && $('#'+formopts.target).length ){
										$('#'+formopts.target).removeClass().addClass('clear notice notice-error').html(res.text);
									}
								} else {
									if(res.reload){
										window.location.href = window.location.href + "&json-res-txt=" + window.encodeURI(res.text);
									}

									if( formopts.target && $('#'+formopts.target).length ){
										$('#'+formopts.target).removeClass().addClass('clear notice notice-success').html(res.text);
									}

									if(res.redirect){
										if(res.text){
											setTimeout(function(){
												window.location.href = res.redirect;
											}, 2500);
										} else {
											window.location.href = res.redirect;
										}
									}
								}
								if(typeof $form[0].simple_ajax_callback == "function")
									$form[0].simple_ajax_callback(res);
								if(typeof formopts.callback == "function")
									formopts.callback(res);
							}
						} catch(e){
							$('#'+formopts.target).removeClass().addClass('clear notice notice-error').html("<p>Invalid server response</p>");
						}
					}	
				});
			});
		}
	});
    
	ithoughts.$d.ready(function(){
		$('.simpleajaxform').simpleAjaxForm();
	});
})(Ithoughts.v4);