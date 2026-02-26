jQuery(document).ready(function($) { 
    
    console.log("successfully loaded block_alltreatments.js");

    $('.block-alltreatments .category-container .category h3').on('click', function() {
        if ($(this).css('cursor') == 'pointer') {
            if ($(this).closest('.category-container').hasClass('open')) {
                $(this).next('ul').slideUp(300, function(event) {
                    $(this).closest('.category-container').removeClass('open');
                });
            } else {
                $('.block-alltreatments .category-container .category ul').slideUp(300).closest('.category-container').removeClass('open');
                $(this).next('ul').slideDown(300, function(event) {
                    $(this).closest('.category-container').addClass('open');
                });
            }
        }
    });
    
});