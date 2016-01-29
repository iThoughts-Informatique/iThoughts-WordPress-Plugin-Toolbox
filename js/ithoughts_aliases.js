
/**
 * @file iThoughts Aliases define shorthands used by iThoughts plugins
 * @author Gerkin
 *         
 * @version 1.0
*/

/**
 * See the {@link http://jquery.com/|jQuery Library} for full details.  This just documents the function and classes that are added to jQuery by this plug-in.
 * @see {@link http://jquery.com/|jQuery} for details about jQuery.
 * @external "jQuery"
 * @borrows external:"jQuery".fn.simpleAjaxForm as external:"jQuery".simpleAjaxForm
 */
/**
 * See (http://jquery.com/)
 * See the jQuery Library  (http://jquery.com/) for full details.  This just
 * documents the function and classes that are added to jQuery by this plug-in.
 * @name external:"jQuery".fn
 * @class
 */

{
    /**
     * Alias for document
	 * @global
	 */
	var d = document;
    /**
     * Alias for document.getElementById
	 * @global
	 */
	gei = function(s){
		return d.getElementById(s);
	}
    /**
     * Alias for document.querySelector
	 * @global
	 */
	qs = function(s){
		return d.querySelector(s);
	}
    /**
     * Alias for document.querySelectorAll
	 * @global
	 */
	qsa = function(s){
		return d.querySelectorAll(s);
	}
	if(jQuery){
		/**
		 * Alias for jQuery
		 * @type {external:"jQuery"}
		 * @global
		 */
		$ = jQuery;
		/**
		 * Alias for jQuery(document)
		 * @type {external:"jQuery"}
		 * @global
		 */
		$d = $(document);
		/**
		 * Alias for jQuery(window)
		 * @type {external:"jQuery"}
		 * @global
		 */
		$w = $(window);
	}
}
