/**
 * @file Simple AJAX Form
 * @description  jQuery extension to make forms AJAX enabled. Mainly used in WordPress projects
 * @author tcbarrett
 *
 * @version 2.0
 */
(function(){
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
				})

				$form.ajaxForm({
					beforeSubmit: function(formData, jqForm, options) {
						//if( !jqForm.valid() ) return false;
						if( formopts.target && $('#'+formopts.target).length ){
							$('#'+formopts.target).html('<p>Updating, please wait...</p>').removeClass('updated').addClass('updating').fadeTo(100,1);
						}
						return true;
					},
					success: function(responseText, statusText, xhr, jQForm){
						if( typeof(jQForm) === 'undefined' )
							jQForm = xhr;

						if( typeof(jQForm) === 'undefined' ){
							$form.append('<div class="error"><p>Cannot handle response properly</p></div>');
							return;
						}

						try{
							var res = JSON.parse(responseText);
							if(!res.valid){
								if( formopts.target && $('#'+formopts.target).length ){
									$('#'+formopts.target).removeClass('updating').removeClass("updated").addClass('error').html(res.text);
								}
							} else {
								if(res.reload){
									window.location.href = window.location.href + "&json-res-txt=" + window.encodeURI(res.text);
								}

								if( formopts.target && $('#'+formopts.target).length ){
									$('#'+formopts.target).removeClass('updating').removeClass("error").addClass('updated').html(res.text);
								}
							}
							if(typeof $form[0].simple_ajax_callback == "function")
								$form[0].simple_ajax_callback(res);
							if(typeof formopts.callback == "function")
								formopts.callback(res);
						} catch(e){
							$('#'+formopts.target).removeClass('updating').removeClass("updated").addClass('error').html("<p>Invalid server response</p>");
						}
					}	
				});
			});
		}
	});
})();

(function(){
	$d.ready(function(){
		$('.simpleajaxform').simpleAjaxForm();
	});
})();