jQuery(document).ready(function($) { 
    
    // console.log("successfully loaded block_accordion.js");
    
    $('.block-accordion .category').on('click', function(event) {
		
		multiple = false;
		if ($(this).closest('.block-accordion').hasClass('multiple')) {
			multiple = true;
		}
		$(this).closest('.block-accordion').find('.category').removeClass('init');
		$(this).closest('.block-accordion').find('.category.open').show();
		$(this).closest('.block-accordion').find('.category-detail').removeClass('init');
		$(this).closest('.block-accordion').find('.category-detail.open').show();
        myID = $(this).data('cat');
        myDetail = $(this).closest('.block-accordion').find('.category-detail[data-cat="' + myID + '"]');

        if (!$(this).closest('.accordion-container').hasClass('locked')) {
            if (!$(this).hasClass("open")) { // if I am not already open
                $(this).closest('.accordion-container').addClass('open');
                $(this).closest('.accordion-container').addClass('locked');
				if (!multiple) {
                	$(this).closest('.block-accordion').find('.category').removeClass('open');
				}
                $(this).addClass('open');
                if ($(this).closest('.block-accordion').find('.category-detail.open')) {
					if (!multiple) {
						$(this).closest('.block-accordion').find('.category-detail.open').slideUp(400, function(event) {
							$(this).removeClass('open');
							$(this).find('.carousel-container, .full-carousel').removeClass('repositioned'); // carousel
						});
						$(this).closest('.block-accordion').find('.subcategory-detail.open').slideUp(400, function(event) {
							$(this).removeClass('open');
							$(this).closest('.subcategory').removeClass('open');
							$(this).closest('.subcategories-container').removeClass('locked');
							$(this).closest('.subcategories-container').removeClass('open');
							$(this).find('.carousel-container, .full-carousel').removeClass('repositioned'); // carousel
							$(this).find('.carousel-container, .full-carousel').css({'opacity' : 0}); // carousel
						});
					}
                }
                myDetail.slideDown(400, function(event) {
					$(this).find('.carousel-container, .full-carousel').children('.slides').slick('setPosition'); // reposition any slick sliders on open
					if (!$(this).find('.carousel-container, .full-carousel').hasClass('repositioned')) { // carousel
						$(this).find('.carousel-container, .full-carousel').css({'opacity' : 0}); // carousel
						$(this).find('.carousel-container, .full-carousel').fadeTo(300, 1, function() { // carousel
							$(this).addClass('repositioned'); // carousel
						}); // carousel
					} // carousel
                    myDetail.addClass('open');
                    $(this).closest('.accordion-container').removeClass('locked');
                });
            } else { // just close me
				$(this).closest('.accordion-container').addClass('locked');
				if (!multiple) {
					$(this).closest('.accordion-container').removeClass('open');
				}
                $(this).removeClass('open');
				$(this).next('.category-detail').slideUp(400, function(event) {
					$(this).removeClass('open');
					$(this).closest('.accordion-container').removeClass('locked');
					if ($(this).closest('.accordion-container').find('.category.open').length == 0) {
						$(this).closest('.accordion-container').removeClass('open');
					}
					$(this).find('.carousel-container, .full-carousel').removeClass('repositioned'); // carousel
					$(this).find('.carousel-container, .full-carousel').css({'opacity' : 0}); // carousel
				});
				if (!multiple) {
					$('.category-detail.open').slideUp(400, function(event) {
						$('.category-detail.open').removeClass('open');
						$(this).closest('.accordion-container').removeClass('locked');
						$(this).find('.carousel-container, .full-carousel').removeClass('repositioned'); // carousel
						$(this).find('.carousel-container, .full-carousel').css({'opacity' : 0}); // carousel
					});
					$('.subcategory-detail.open').slideUp(400, function(event) {
						$('.subcategory-detail.open').removeClass('open');
						$(this).closest('.subcategory').removeClass('open');
						$(this).closest('.subcategories-container').removeClass('locked');
						$(this).closest('.subcategories-container').removeClass('open');
						$(this).find('.carousel-container, .full-carousel').removeClass('repositioned'); // carousel
						$(this).find('.carousel-container, .full-carousel').css({'opacity' : 0}); // carousel
					});
				}
            }
        } 
    });
    
    
    $('.block-accordion .category-detail .subcategory').on('click', function(event) {
        
        myID = $(this).data('cat');
        myDetail = $(this).children('.subcategory-detail');

        if (!$(this).closest('.subcategories-container').hasClass('locked')) {
            if (!$(this).hasClass("open")) { // if I am not already open
                $(this).closest('.subcategories-container').addClass('open');
                $(this).closest('.subcategories-container').addClass('locked');
                $('.subcategory').removeClass('open');
                $(this).addClass('open');
                if ($('.subcategory-detail.open')) {
                    $('.subcategory-detail.open').slideUp(400, function(event) {
                        $(this).removeClass('open');
						$(this).find('.carousel-container, .full-carousel').removeClass('repositioned'); // carousel
						$(this).find('.carousel-container, .full-carousel').css({'opacity' : 0}); // carousel
                    });
                }
                myDetail.slideDown(400, function(event) {
                    myDetail.addClass('open');
                    $(this).closest('.subcategories-container').removeClass('locked');
					if (!$(this).find('.carousel-container, .full-carousel').hasClass('repositioned')) { // carousel
						$(this).find('.carousel-container, .full-carousel').css({'opacity' : 0}); // carousel
						$(this).find('.carousel-container, .full-carousel').fadeTo(300, 1, function() { // carousel
							$(this).addClass('repositioned'); // carousel
						}); // carousel
					} // carousel
                });
            } else { // just close me
                $(this).closest('.subcategories-container').removeClass('open');
                $(this).closest('.subcategories-container').addClass('locked');
                $(this).removeClass('open');
                $('.subcategory-detail.open').slideUp(400, function(event) {
                    $('.subcategory-detail.open').removeClass('open');
                    $(this).closest('.subcategories-container').removeClass('locked');
					$(this).find('.carousel-container, .full-carousel').removeClass('repositioned'); // carousel
					$(this).find('.carousel-container, .full-carousel').css({'opacity' : 0}); // carousel
                });
            }
        } 
        
    });
    
});