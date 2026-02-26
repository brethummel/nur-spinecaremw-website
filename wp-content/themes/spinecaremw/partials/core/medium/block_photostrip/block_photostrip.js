jQuery(document).ready(function($) { 
    
    // console.log("successfully loaded block_photostrip.js");
    
	$('.block-photostrip.carousel .photos-container').slick({
		slidesToShow: 4,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 4000,
        speed: 900,
		adaptiveHeight: false,
        centerMode: true,
        arrows: true,
        pauseOnFocus: false,
        pauseOnHover: false,
        dots: false,
		prevArrow: $('.block-photostrip.carousel .back'),
		nextArrow: $('.block-photostrip.carousel .next'),
		responsive: [
			{
				breakpoint: 992,
				settings: {
					slidesToShow: 3
				}
			},
			{
				breakpoint: 768,
				settings: {
					slidesToShow: 2
				}
			}
		]
	});
    
    $('.block-photostrip.carousel .photos-container').on('beforeChange', function(event, slick, currentSlide, nextSlide) {
        if (nextSlide == 0) {
            // console.log("beforeChange called on " + currentSlide);
            $('.block-photostrip.carousel .photos-container .image-1.slick-cloned').addClass('slick-loop');
            // console.log('.slick-cloned added to slide 1!');
        }
    });
    
    $('.block-photostrip.carousel .photos-container').on('afterChange', function(event, slick, currentSlide) {
        if (currentSlide == 0) {
            // console.log("afterChange called on " + currentSlide);
            $('.block-photostrip.carousel .photos-container .image-1.slick-cloned').removeClass('slick-loop');
            // console.log('.slick-cloned removed from slide 1!');
        }
    });
    
    
});