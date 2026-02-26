jQuery(document).ready(function($) { 
    
    console.log("successfully loaded block_gallery.js");
	
	$('.block-gallery.lightboxed .image-container .image').on('click', function(event) {
		var mygallery = $(this).siblings('.sub-gallery').html();
        $('body .pop-overlay .pop-modal').addClass('pop-gallery');
        $('body .pop-overlay .pop-modal .pop-content').html(mygallery);
        $('body .pop-overlay').css('display', 'flex');
		$('body .pop-overlay .pop-modal .pop-content .slides').slick({
			slidesToScroll: 1,
			autoplay: false,
			adaptiveHeight: false,
			arrows: true,
			pauseOnFocus: false,
			pauseOnHover: false,
			dots: true,
			appendDots: $('body .pop-overlay .pop-modal .pop-content .dots'),
			prevArrow: $('body .pop-overlay .pop-modal .pop-content .arrows').find('.prev'),
			nextArrow: $('body .pop-overlay .pop-modal .pop-content .arrows').find('.next')
		});
	});
    
    
});