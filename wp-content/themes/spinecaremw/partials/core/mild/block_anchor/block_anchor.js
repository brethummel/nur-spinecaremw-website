jQuery(window).on('load', function($) {
	
    // console.log("successfully loaded block_anchor.js");
    
	// came from other page
    if (window.location.hash !== "") {
        var hash = window.location.hash + "-hash";
        //console.log(hash);
		var header = jQuery('header').height();
        jQuery('html, body').delay(200).animate({
            scrollTop: jQuery(hash).offset().top - header - 120
        }, 800);
    }
	
});


jQuery(document).ready(function($) { 
	
	// clicked anchor tag on page where anchor exists
	$("a").on('click', function(event) {
		
		var slug = $('body').data('slug');
		var dest = $(this).attr('href').split('/');
		
		if (dest[dest.length - 1].charAt(0) == '#' && (dest[dest.length - 1].length > 1) && (dest[dest.length - 2] == slug || dest.length == 1)) { // href is anchor tag only
			event.preventDefault();
			var hash = dest[dest.length - 1] + "-hash";
			var header = $('header').height();
			if ($.isFunction(window.closeMenu)) {
				closeMenu();
			}
			$('html, body').delay(200).animate({
				scrollTop: $(hash).offset().top - header - 120
			}, 800);
		}
	});
	
});