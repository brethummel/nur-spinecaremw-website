jQuery(document).ready(function($) { 
    
    // console.log("successfully loaded block_bio.js");
    
    $('p.read-more a').on('click', function(event) {
        event.preventDefault();
        if ($(this).parent().prev().hasClass('bio-container')) {
            // console.log('Need to remove the container height!');
            var target = $(this).parent().prev();
            if (!$(this).hasClass('open')) {
                if (target.data('height')) {
                    // console.log('there is a height!');
                    target.css('height', $(this).parent().prev().data('height'));
                } else {
                    // console.log('there is NO height!');
                    var mymaxheight = target.height() + 'px';
                    target.data('max-height', mymaxheight);
                    // console.log(mymaxheight);
                    setTimeout(function() {
                        target.css('height', target.height() + 'px');
                        // console.log(target);
                        // console.log('Timed out!');
                    }, 1000);
                }
                target.addClass('open');
                $(this).addClass('open');
                $(this).text('show less');
            } else {
                var myheight = $(this).parent().prev().height() + 'px';
                // console.log(myheight);
                $(this).parent().prev().data('height', myheight);
                $(this).parent().prev().css('height', $(this).parent().prev().data('max-height'));
                $(this).parent().prev().removeClass('open');
                $(this).removeClass('open');
                $(this).text('read more');
            }
        } else {
            // console.log('Need to expand the container!');
            var target = $(this).parent().prev();
            if (!$(this).hasClass('open')) {
                target.slideDown(300);
                $(this).addClass('open');
                $(this).text('show less');
            } else {
                target.slideUp(300);
                $(this).removeClass('open');
                $(this).text('read more');
            }
        }
    });
    
});