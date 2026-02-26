jQuery(document).ready(function($) { 
    
    console.log("successfully loaded block_fullimage.js");
    
	$('.block-fullimage.carousel .slides').slick({
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 5000,
		adaptiveHeight: false,
        arrows: false,
        pauseOnFocus: false,
        pauseOnHover: false,
        dots: true,
        appendDots: $(".block-fullimage.carousel .dots")
	});
    
    
});