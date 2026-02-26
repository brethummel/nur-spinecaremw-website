// JavaScript Document


jQuery(document).ready(function($) { 	
	
	// console.log('footer.js loaded successfully!');
	
    $('.pop-overlay .pop-modal .pop-close').on('click', function(event) {
        event.stopPropagation();
        $('.pop-overlay').hide();
        $('.pop-overlay .pop-modal .pop-content').empty();
    });
    
    $('.pop-overlay .pop-modal').on('click', function(event) {
        event.stopPropagation();
    });
    
    $('.pop-overlay').on('click', function(event) {
        $('.pop-overlay').hide();
        $('.pop-overlay .pop-modal .pop-content').empty();
    });
	
});