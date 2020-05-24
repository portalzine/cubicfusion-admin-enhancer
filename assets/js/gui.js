/*! 
	cubicFUSION Admin Enhancer \ GUI
	Alex @ portalZINE NMN
	https://portalzine.de/cubicfusion

*/


jQuery(document).ready(function($) {
    
	$(".show_change").on("click", function(){
		$(".changelog").toggleClass("hidden");
	});
	
	$(".changelog").on("click", function(){
		$(".changelog").toggleClass("hidden");
	});
	
	$("[data-link-color]").each(function(){
		
		$(this).find("a").css({"color":$(this).attr("data-link-color")});
	});
	
		$("[data-color]").each(function(){
		
		$(this).find("*").not("a").css({"color":$(this).attr("data-color")});
	});
	
	$(".gui_clipboard").on("click", function(){
		window.alert("Copied to Clipboard!");		
		var copyText = $(this).prev("input")[0];  
  		copyText.select();
  		document.execCommand("copy");
		
	});
	
}); 
