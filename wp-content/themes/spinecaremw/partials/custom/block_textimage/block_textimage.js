jQuery(document).ready(function($) { 
    
    // console.log("successfully loaded block_textimage.js");
    
	$('.block-textimage.carousel .full-carousel .slides').each(function() {
		$(this).slick({
			slidesToScroll: 1,
			autoplay: true,
			autoplaySpeed: 5000,
			adaptiveHeight: false,
			arrows: true,
			pauseOnFocus: false,
			pauseOnHover: false,
			dots: true,
			appendDots: $(this).siblings('.dots.full'),
			prevArrow: $(this).siblings('.arrows.full').children('.prev'),
			nextArrow: $(this).siblings('.arrows.full').children('.next')
		});
	});
    
	$('.block-textimage.carousel .carousel-container .slides').each(function() {
		$(this).slick({
			slidesToScroll: 1,
			autoplay: true,
			autoplaySpeed: 5000,
			adaptiveHeight: false,
			arrows: true,
			pauseOnFocus: false,
			pauseOnHover: false,
			dots: true,
			appendDots: $(this).siblings('.dots.main'),
			prevArrow: $(this).siblings('.arrows.main').children('.prev'),
			nextArrow: $(this).siblings('.arrows.main').children('.next')
		});
	});
    
    
});