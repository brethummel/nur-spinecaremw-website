jQuery(document).ready(function($) { 
    
    // console.log("successfully loaded block_tabs.js");
    
    $('.block-tabs .tab').on('click', function(event) {
		
		$(this).closest('.block-tabs').find('.tab.open').show();
		$(this).closest('.block-tabs').find('.tab-detail.open').show();
        myID = $(this).data('tab');
        myDetail = $(this).closest('.block-tabs').find('.tab-detail[data-tab="' + myID + '"]');
        console.log(myDetail);

        if (!$(this).closest('.tabs-container').hasClass('locked')) {
            if (!$(this).hasClass('open')) { // if I am not already open
                $(this).closest('.tabs-container').addClass('open').addClass('locked');
            	$(this).closest('.block-tabs').find('.tab').removeClass('open');
                $(this).addClass('open');
                if ($(this).closest('.block-tabs').find('.tab-detail.open')) {
					$(this).closest('.block-tabs').find('.tab-detail.open').fadeOut(200, function(event) {
						$(this).removeClass('open');
		                myDetail.fadeIn(600, function(event) {
		                    myDetail.addClass('open');
		                    $(this).closest('.block-tabs').find('.tabs-container').removeClass('locked');
		                });
					});
                }
            }
        } 
    });
    
});