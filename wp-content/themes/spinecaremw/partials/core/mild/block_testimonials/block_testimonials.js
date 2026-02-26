jQuery(document).ready(function($) { 
    
    // console.log("successfully loaded block_testimonials.js");
	
	var speed = 5000;
	if ($('.block-testimonials').data('speed')) {
		speed = $('.block-testimonials').data('speed');
	}
	
	var arrows = false;
	if ($('.block-testimonials').data('arrows')) {
		arrows = $('.block-testimonials').data('arrows');
	}
	
	// console.log(arrows);
	
	var i = 0;
	$('.block-testimonials').each(function() {
		i++;
		$(this).addClass('slider-' + i).find('.slides').slick({
			slidesToScroll: 1,
			autoplay: true,
			autoplaySpeed: $(this).data('speed'),
			adaptiveHeight: false,
			arrows: $(this).data('arrows'),
			pauseOnFocus: false,
			pauseOnHover: false,
			dots: true,
			appendDots: $('.block-testimonials.' + 'slider-' + i).find('.dots'),
			prevArrow: $('.block-testimonials.' + 'slider-' + i + ' .arrows').find('.prev'),
			nextArrow: $('.block-testimonials.' + 'slider-' + i + ' .arrows').find('.next')
		});
	});
    
    
});