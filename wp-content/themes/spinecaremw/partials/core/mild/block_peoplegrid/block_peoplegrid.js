jQuery(document).ready(function($) { 
    
    console.log("successfully loaded block_peoplegrid.js");
    
    $('.block-peoplegrid .person a.bio-pop').on('click', function(event) {
        // console.log('person clicked!');
        event.preventDefault();
        $('body .pop-overlay .pop-modal').addClass('pop-bio');
        var mybio = $(this).closest('.person').children('.bio').clone();
        $('body .pop-overlay .pop-modal .pop-content').html(mybio);
        $('body .pop-overlay').css('display', 'flex');
    });
	
	$('.block-peoplegrid .person .image-container').on('mouseenter', function(event) {
		$(this).addClass('over');
	});
	
	$('.block-peoplegrid .person .image-container').on('mouseleave', function(event) {
		$(this).removeClass('over');
	});
    
    
});