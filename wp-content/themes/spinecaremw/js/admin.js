jQuery(document).ready(function($) { 
    
    // console.log("successfully loaded admin.js");


	// $('#acf-group_66be5148cd849').hide(); // hide File Transcript fieldset

	
	// ======================================= //
	//          ADMIN-ONLY BLOCK TAB           //
	// ======================================= //
    
	$('.acf-field-flexible-content .acf-input .acf-flexible-content .layout .acf-fields .acf-tab-wrap ul.acf-tab-group li').each(function(index) {
		if ($(this).children('a').text() == "Admin") {
			$(this).addClass('admin-tab');
		}
	});
	
	$('.acf-field-610ac7050d4ff>.acf-input>.acf-flexible-content>.values>.layout').each(function() {
		$(this).find('.acf-field-true-false').each(function() {
			if ($(this).data('name') == 'display_block') {
				if (!$(this).find('.acf-switch').hasClass('-on')) {
					$(this).closest('.layout').addClass('-off');
				}
			}
		});
	});
	
	$('body').on('click', 'td.acf-field-true-false[data-name="display_block"] .acf-input .acf-true-false .acf-switch', function(event) {
		// console.log('display_block toggle has been clicked!');
		if ($(this).closest('.layout').hasClass('-off')) {
			$(this).closest('.layout').removeClass('-off');
		} else {
			$(this).closest('.layout').addClass('-off');
		}
	});
	
	var keys = [];
	
	$('body').keydown(function(e) {
		keys[e.which] = true;
		checkKeys();
	});
	
	$('body').keyup(function(e) {
		delete keys[e.which];
		checkKeys();
	});
	
	function checkKeys() { // option-shift-a
		if (16 in keys && 18 in keys && 65 in keys) {
			$('body').addClass('spr-show-admin-only');
		} else {
			$('body').removeClass('spr-show-admin-only');
		}
	}
	
	
	// ======================================= //
	//           COLLAPSE ALL BLOCKS           //
	// ======================================= //
	
	$('.acf-field-610ac7050d4ff>.acf-label>label>').on('click', 'span.collapse-all', function() {
		// console.log('Collapse All clicked!');
		$('.acf-field-610ac7050d4ff>.acf-input>.acf-flexible-content>.values>.layout').each(function() {
			$(this).addClass('-collapsed');
		});
	});
	
	
	// ======================================= //
	//           ADD TINYMCE BUTTONS           //
	// ======================================= //
	
	
	// ******* ADD "ADD BUTTON" BUTTON ******* //
	if (typeof(tinyMCE) != 'undefined') {
		
		tinymce.create('tinymce.plugins.spr_addbutton_plugin', {
			init : function(editor, url) { // Register command for when button is clicked
				editor.addCommand('spr_addbutton', function() {
					content = '<a class="button medium primary" href="../relative-path-to-your-page"><span class="button-text">Learn More</span></a>';
					tinymce.execCommand('mceInsertContent', false, content);
				});

			// register buttons - trigger above command when clicked
			editor.addButton('spr_addbutton_button', {title : 'Add Button', cmd : 'spr_addbutton', image: url + '/../images/add_button.svg' });

			},   
		});

		// register tinymce plugin: first parameter is button ID, second must match first parameter of tinymce.create() function above
		tinymce.PluginManager.add('spr_addbutton_button', tinymce.plugins.spr_addbutton_plugin);
		
	}
	
	
	// ======================================= //
	//          SECTION BLOCK HANDLER          //
	// ======================================= //
	
	if (typeof(acf) != 'undefined') {
		acf.addAction('ready_field/key=field_610ac7050d4ff', function($el) {
			// console.log('Content Blocks is ready!');
			spr_manageACFUI('init', $el);
		});
		acf.addAction('append', function($el) {
			// console.log('A new row was added!');
			// console.log($el);
			spr_manageACFUI('append', $el);
		});
		acf.addAction('remove', function($el) {
			// console.log('A row was removed!');
			// console.log($el);
			spr_manageACFUI('remove', $el);
		});
		acf.addAction('sortstop', function($el) {
			// console.log('A row was sorted!');
			// console.log($el);
			spr_manageACFUI('sortstop', $el);
		});
	}
	
	function spr_manageACFUI(action, element) {
		sectioncount = 0;
		indent = ''; // initialize indent flag
		even = '';
		$('.acf-field-610ac7050d4ff > .acf-input > .acf-flexible-content > .values > .layout').each(function(index) {
			// console.log($(this).data('id'), $(this).data('layout'));
			// if (action == 'remove' && $(this).data('id') == element.data('id')) { console.log("I'm the removed element and I'm a block_section"); }
			if ($(this).data('layout') == 'block_section' && !(action == 'remove' && $(this).data('id') == element.data('id'))) {
				sectioncount++;
				if (sectioncount % 2 !== 0) { // odd
					// console.log('index = ' + index, ": I'm odd!");
					even = true;
					// console.log('removing -indent!');
					$(this).removeClass('-indent');
					indent = true;
					// console.log('indent = true!');
					$(this).addClass('section-start');
				} else { // even
					// console.log('index = ' + index, ": I'm even!");
					$(this).addClass('section-end -indent -collapsed');
					// console.log('adding -indent!');
					even = false;
					// console.log('indent = false!');
					indent = false;
				}
				if ($(this).find('.block-type').length == 1) {
					if (even) {
						$(this).find('.block-type').text('(Section Start)');
					} else {
						$(this).find('.block-type').text('(Section End)');
					}
				} else {
					$(this).addClass('show-label');
				}
			} else {
				if (indent) {
					// console.log('adding -indent!');
					$(this).addClass('-indent');
				} else {
					$(this).removeClass('-indent');
					// console.log('removing -indent!');
				}
			}
		});
		if (sectioncount % 2 !== 0) {
			// console.log('Houston, we have a problem!', sectioncount);
			unresolved = $('.acf-field-610ac7050d4ff .acf-input .acf-flexible-content .values .layout[data-layout="block_section"]').eq(sectioncount - 1);
			$('<div class="section-unresolved">Missing section end!</div>').insertBefore(unresolved.children('.acf-fc-layout-controls'));
		} else {
			$('.section-unresolved').remove();
		}
		// console.log('======================');
	}
	
	
	// ======================================= //
	//           COPY BLOCKS HANDLERS          //
	// ======================================= //
	
	$('.acf-field-610ac7050d4ff .acf-input .acf-flexible-content .acf-actions a.acf-button[data-name="copy-layouts"]').on('click', function(event) {
		if ($('#poststuff #post-body #post-body-content #acf-form-data input#_acf_changed').val() == 1) {
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-post').addClass('unsaved-changes');
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .posts-search input').prop( "disabled", true );
		}
		$('body').css('overflow','hidden');
		$('#poststuff #post-body #post-body-content .spr-select-source .tinter').css({'transition-delay':'0s', 'opacity':'0.55', 'pointer-events':'auto'});
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container').css({'transition-delay':'.15s', 'opacity':'1', 'margin-top':'0', 'pointer-events':'auto'});
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-post').css({'pointer-events':'auto'});
	});
	
	$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-post .posts-actions a.acf-button[data-name="posts-cancel"], #poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-actions a.acf-button[data-name="blocks-cancel"], #poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .posts-list .unsaved-changes input#publish').on('click', function(event) {
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container').clearQueue();
		$('#poststuff #post-body #post-body-content .spr-select-source .tinter').css({'transition-delay':'.15s', 'opacity':'0', 'pointer-events':'none'});
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container').css({'transition-delay':'0s', 'opacity':'0', 'margin-top':'40px', 'pointer-events':'none'});
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container').delay(300).queue(function() {
			// reset everything to defaults
			$(this).removeClass('blocks').addClass('post');
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .posts-list ul.select-list li').removeClass('selected').removeClass('unselected');
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .posts-actions a.acf-button[data-name="posts-next"]').addClass('disabled'); // disable button
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks').css({'pointer-events':'none'}).hide();
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list').empty();
			var themeurl = $('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list').data('themeurl');
			$('.spr-select-source .select-container .select-blocks .blocks-list').html('<div class="loading"><p><img src="' + themeurl + '/images/spinner_light.gif" alt="loading..."/></p></div>');
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-post').css({'pointer-events':'none'}).show();
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .postbox-header .select-type select').val('').change();
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .posts-list ul.select-list').children('li').show();
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .posts-list').scrollTop(0);
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .posts-search input').val('').change();
			$('body').css('overflow','visible');
		});
	});
	
	var sourcepost = '';
	var posttype = '';
	
	// *********** DROPDOWN FILTER *********** //
	$('#poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .postbox-header .select-type select').on('change', function(event) {
		posttype = $(this).children("option:selected").val();
		// console.log(posttype);
		if (posttype != '') {
			// console.log('searchterm: ' + searchterm);
			$(this).closest('.select-post').find('ul.select-list').children('li').hide().removeClass('selected');
			$(this).closest('.select-post').find('a[data-name="posts-next"]').addClass('disabled'); // disable button
			if (searchterm.length > 1) {
				spr_showhide_posts(searchterm);
			} else {
				$('#poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .posts-search input').val('');
				$(this).closest('.select-post').find('ul.select-list').children('li[data-posttype="' + posttype + '"]').show();
			}
		} else {
			if (searchterm.length > 1) {
				spr_showhide_posts(searchterm);
			} else {
				$(this).closest('.select-post').find('ul.select-list').children('li').show();
			}
		}
	});
	
	// ********* LIST ITEM MANAGEMENT ******** //
	$('#poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .posts-list ul.select-list li').on('click', function(event) {
		event.stopImmediatePropagation();
		if ($(this).hasClass('selected')) {
			$(this).removeClass('selected').addClass('unselected');
			$(this).closest('.select-post').find('a[data-name="posts-next"]').addClass('disabled'); // disable button
			sourcepost = '';
		} else {
			$(this).siblings().removeClass('selected');
			$(this).addClass('selected');
			$(this).closest('.select-post').find('a[data-name="posts-next"]').removeClass('disabled'); // enable button
			sourcepost = $(this).data('postid');
			$(this).closest('.spr-select-source').data('sourcepost', sourcepost);
			// console.log('post #' + $(this).closest('.spr-select-source').data('sourcepost') + ' will be the source!');
		}
	});
	
	$('#poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .posts-list ul.select-list li').on('mouseleave', function(event) {
		$(this).removeClass('unselected');
	});
	
	// ************* SEARCH FIELD ************ //
	var typingTimeout;
	var ignoreKeys = [13, 16, 17, 18, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 44, 45, 91, 92, 93, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 144, 145, ];
	var searchterm = '';
    $('#poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .posts-search input').on('keyup', function(event) {
		var key = event.which || event.keyCode;
		if ($.inArray(key, ignoreKeys) < 0) {
			searchterm = $(this).val();
			searchterm = $.trim(searchterm).toLowerCase();
			if (searchterm.length > 1) {
				spr_showhide_posts(searchterm);
			} else {
				$('#poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .posts-list ul.select-list li[data-posttype="' + posttype + '"]').show();
			}
		}
	});
	
	function spr_showhide_posts(searchterm) {
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container.post .select-post .posts-list ul.select-list li').each(function(index) {
			if (!$(this).html().toLowerCase().includes(searchterm)) {
				$(this).hide();
			} else {
				// console.log('posttype: ' + posttype);
				// console.log('this posttype: ' + $(this).data('posttype'));
				if (posttype == '' || $(this).data('posttype') == posttype) {
					// console.log('found term in: ' + $(this).html());
					// console.log('showing: ' + $(this).html());
					if (!$(this).hasClass('post-type-label')) {
						$(this).show();
					} else {
						$(this).hide();
					}
				} else {
					$(this).hide();
				}
			}
		});
	}
	
	$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-post .posts-actions a.acf-button[data-name="posts-next"]').on('click', function(event) {
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-post').css({'display':'none', 'pointer-events':'none'});
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container').css({'transition-delay':'0s'}).removeClass('post').addClass('blocks');
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks').clearQueue();
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks').css({'transition-delay':'0s', 'opacity':'0'}).show();
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks').delay(300).queue(function() {
			$(this).css({'opacity':'1', 'pointer-events':'auto'});
			$(this).find('.blocks-list').css({'pointer-events':'auto'});
		});
	});
	
	$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list').on('click', 'p.no-results span.back', function(event) {
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-actions a[data-name="blocks-back"]').trigger('click');
	});
	
	$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-actions a[data-name="blocks-back"]').on('click', function(event) {
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks').css({'display':'none', 'pointer-events':'none'});
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list').empty();
		var themeurl = $('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list').data('themeurl');
		$('.spr-select-source .select-container .select-blocks .blocks-list').html('<div class="loading"><p><img src="' + themeurl + '/images/spinner_light.gif" alt="loading..."/></p></div>');
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container').css({'transition-delay':'0s'}).removeClass('blocks').addClass('post');
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-post').clearQueue();
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-post').css({'transition-delay':'0s', 'opacity':'0'}).show();
		$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-post').delay(300).queue(function() {
			$(this).css({'opacity':'1', 'pointer-events':'auto'});
		});
	});
	
	var blockcount = 0; // how many blocks are selected to copy?
	
	$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list').on('click', '.blocks-container .layout', function(event) {
		event.stopImmediatePropagation();
		if (!$(this).hasClass('selected')) {
			$(this).addClass('selected').find('input[type="checkbox"]').prop('checked', true);
			blockcount++;
			if ($(this).hasClass('section-start')) {
				$(this).nextAll('.-indent').each(function(index) {
					$(this).addClass('selected').find('input[type="checkbox"]').prop('checked', true);
					if ($(this).hasClass('section-end')) {
						return false;
					}
				});
			}
		} else {
			$(this).removeClass('selected').find('input[type="checkbox"]').prop('checked', false);
			blockcount--;
		}
		spr_copy_blocks_button_handler();
	});
	
	$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list').on('click', '.blocks-container h3 span.select-controls span', function(event) {
		if ($(this).hasClass('all')) {
			blockcount = 0;
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list .blocks-container .layout').each(function(index) {
				$(this).addClass('selected').find('input[type="checkbox"]').prop('checked', true);
				blockcount++;
			});
		} else {
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-list .blocks-container .layout').each(function(index) {
				$(this).removeClass('selected').find('input[type="checkbox"]').prop('checked', false);
			});
			blockcount = 0;
		}
		spr_copy_blocks_button_handler();
	});
	
	function spr_copy_blocks_button_handler() {
		if (blockcount > 0) {
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-actions a.button[data-name="blocks-copy"]').removeClass('disabled');
		} else {
			$('#poststuff #post-body #post-body-content .spr-select-source .select-container .select-blocks .blocks-actions a.button[data-name="blocks-copy"]').addClass('disabled');
		}
	}
	
});