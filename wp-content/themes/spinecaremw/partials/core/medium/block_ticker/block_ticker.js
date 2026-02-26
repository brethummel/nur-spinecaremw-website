jQuery(document).ready(function($) { 
    
    // console.log("successfully loaded block_ticker.js");
	
	var direction = $('.block-ticker.single').data('direction');
	// console.log(direction);
	if (direction == 'right') {
		// console.log('setting to rtl');
		direction = 'rtl';
	} else if (direction == 'left') {
		// console.log('setting to ltr');
		direction = 'ltr';
	} else {
		direction = 'ltr';
	}
	
	// console.log(direction);
	
	$('.block-ticker .line-1').eocjsNewsticker({
		speed: 36,
		divider: '',
		direction: direction
	});
	
	$('.block-ticker .line-2').eocjsNewsticker({
		speed: 36,
		divider: '',
		direction: 'rtl'
	});
    
});