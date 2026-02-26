console.log("successfully loaded api_copy_blocks_ajax.js");

jQuery(function($) {
	
	
	$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-post .posts-actions a.acf-button[data-name="posts-next"]').on('click', function(event) {
		var sourcepost = $('#poststuff #post-body #post-body-content .spr-select-source').data('sourcepost');
		spr_load_blocks(sourcepost);
	});
	
	$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-actions a.button[data-name="blocks-copy"]').on('click', function(event) {
		var destpost = $('#wpbody #wpbody-content .wrap form input#post_ID').val();
		var sourcepost = $('#poststuff #post-body #post-body-content .spr-select-source').data('sourcepost');
		var blocks = $('.spr-select-source .select-container.blocks .select-blocks .blocks-list .blocks-container .layout.selected');
		var selectedblocks = [];
		blocks.each(function(index, data) {
			selectedblocks.push($(this).data('id'));
		});
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list').fadeOut(300, function(event) {
			var themeurl = $('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list').data('themeurl');
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list').html('<div class="loading"><p><img src="' + themeurl + '/images/spinner_admin.gif" alt="loading..."/></p><p class="message">Copying blocks...</p></div>').fadeIn(300);
		});
		spr_copy_blocks(destpost, sourcepost, selectedblocks);
	});
	
	function spr_load_blocks(sourcepost) {
		console.log('spr_load_blocks called with sourcepost = ' + sourcepost + '!');
        var data = {
            'action': 'spr_load_blocks_by_ajax',
            'sourcepost': sourcepost,
            'security': blocks.security
        };
        $.post(blocks.ajaxurl, data, function(response) {
            if($.trim(response) != '') {
				// console.log('appending response to .projects-container!');
				console.log(response);
				if (response.indexOf('layout') > 0) {
					response = response.slice(0,-1);
				} else {
					response = response.slice(0,-1) + '<p class="no-results">Oops. No blocks were found. <span class="back">Try again.</span></p>';
				}
				$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list').html(response);
				$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list .blocks-container').fadeIn(300, function(event) {

				});
			}
        });
	}
	
	function spr_copy_blocks(destpost, sourcepost, selectedblocks) {
		console.log('spr_copy_blocks called with destpost = ' + destpost + ', sourcepost = ' + sourcepost + ', selectedblocks = ' + selectedblocks + '!');
        var data = {
            'action': 'spr_copy_blocks_by_ajax',
            'destpost': destpost,
            'sourcepost': sourcepost,
            'selectedblocks': selectedblocks,
            'security': blocks.security
        };
        $.post(blocks.ajaxurl, data, function(response) {
            if($.trim(response) != '') {
				console.log(response);
				if ($.trim(response) == 'success') {
					$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list .loading p.message').text('Reloading page...');
					location.reload(true);
				}
			}
		});
	}
	
});