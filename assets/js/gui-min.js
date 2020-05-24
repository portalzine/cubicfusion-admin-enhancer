/*! 
	cubicFUSION Admin Enhancer \ GUI
	Alex @ portalZINE NMN
	https://portalzine.de/cubicfusion

*/
jQuery(document).ready((function(o){o(".gui_clipboard").on("click",(function(){window.alert("Copied to Clipboard!"),o(this).prev("input")[0].select(),document.execCommand("copy")}))}));