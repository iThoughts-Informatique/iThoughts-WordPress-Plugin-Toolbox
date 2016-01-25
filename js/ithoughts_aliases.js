function gei(s){
	return document.getElementById(s);
}
function qs(s){
	return document.querySelector(s);
}
function qsa(s){
	return document.querySelectorAll(s);
}
if(jQuery){
	$ = jQuery;
	$doc = $(document);
	$win = $(window);
}