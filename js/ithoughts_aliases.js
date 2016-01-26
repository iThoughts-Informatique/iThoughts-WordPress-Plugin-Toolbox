/**
  * iThoughts Aliases
  *
  * Define aliases used by iThoughts plugins
  * Author: Gerkin
*/
{
	var d = document;
	gei = function(s){
		return d.getElementById(s);
	}
	qs = function(s){
		return d.querySelector(s);
	}
	qsa = function(s){
		return d.querySelectorAll(s);
	}
	if(jQuery){
		$ = jQuery;
		$doc = $(document);
		$win = $(window);
	}
}
