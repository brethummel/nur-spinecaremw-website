jQuery(document).ready(function($) { 
    
    console.log("successfully loaded block_related.js");
    
    // console.log(arrows);
    
    var i = 0;
    $('.block-related').each(function() {
        i++;
        $(this).addClass('slider-' + i).find('.slides').slick({
            autoplay: true,
            slidesToShow: $(this).data('columns'),
            slidesToScroll: $(this).data('columns'),
            autoplaySpeed: $(this).data('speed'),
            adaptiveHeight: false,
            arrows: $(this).data('arrows'),
            pauseOnFocus: false,
            pauseOnHover: false,
            dots: true,
            infinite: true,
            appendDots: $('.block-related.' + 'slider-' + i).find('.dots'),
            prevArrow: $('.block-related.' + 'slider-' + i + ' .arrows').find('.prev'),
            nextArrow: $('.block-related.' + 'slider-' + i + ' .arrows').find('.next'),
            responsive: [
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    });

});