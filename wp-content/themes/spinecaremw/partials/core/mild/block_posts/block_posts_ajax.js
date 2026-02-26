// console.log("successfully loaded block_posts_ajax.js");

var page = 1;

jQuery(function($) {
	
    $('.block-posts').on('click', '.load-more', function() {
    	var blockid = $(this).closest('.block-posts').attr('id');
    	var searchfields = $(this).closest('.block-posts').find('.search-bar').data('searchfields');
    	var searchterms = $(this).closest('.block-posts').find('.posts-container').data('searchterms');
    	// console.log(searchterms);
		// console.log(blockid);
		if (!$('.block-posts#' + blockid + ' .load-more').hasClass('hide')) {
			$('.block-posts#' + blockid + ' .load-more').addClass('loading').fadeIn(300);
			// console.log('.load-more clicked!');
			page++;
			// console.log('page: ' + page);
			if (searchterms) {
				spr_load_posts(blockid, 'store', page, searchfields);
			} else {
				spr_load_posts(blockid, true, page, searchfields);
			}
		}
    });
	
	$('.block-posts .filter-row .filters ul li').on('click', function(event) {
		// console.log('Filters changed!');
		var blockid = $(this).closest('.block-posts').attr('id');
    	var searchfields = $(this).closest('.block-posts').find('.search-bar').data('searchfields');
		// console.log(blockid);
		if ($('.block-posts#' + blockid + ' .posts-container').data('current-page') == 0) { // no results
			$('.block-posts#' + blockid + ' .no-results').fadeOut(300, function(event) {
				// console.log('Faded out .no-results!');
				$('.block-posts#' + blockid + ' .load-more').fadeIn();
				page = 1;
				spr_load_posts(blockid, false, page, searchfields);
			});
		} else {
			$('.block-posts#' + blockid + ' .posts-container').data('current-page', 0);
			$('.block-posts#' + blockid + ' .posts-container .results-page').fadeOut(300, function(event) {
				// console.log('Faded out .results-page!');
				page = 1;
				$('.block-posts#' + blockid + ' .posts-container').html('');
				spr_load_posts(blockid, false, page, searchfields);
			});
		}
	});

	$('.block-posts .sidebar .tag-cloud ul.tags li a').on('click', function(event) {
		var blockid = $(this).closest('.block-posts').attr('id');
    	var searchfields = $(this).closest('.block-posts').find('.search-bar').data('searchfields');
		// console.log(blockid);
		if ($(this).attr('href') == '#' && !$(this).parent().hasClass('current')) {
			event.preventDefault();
			$('.block-posts#' + blockid + ' .sidebar .tag-cloud ul.tags li').removeClass('current');
			$(this).parent().addClass('current');
			var slug = $(this).parent().data('slug');
			$('.block-posts#' + blockid + ' .active-tags h3').fadeTo(300, 0, function(event) {
				$('.block-posts#' + blockid + ' .active-tags h3 a').html('#' + slug);
			});
			$('.block-posts#' + blockid + ' .posts-container').data('current-page', 0);
			$('.block-posts#' + blockid + ' .posts-container').data('tags', $(this).parent().data('id'));
			$('.block-posts#' + blockid + ' .posts-container .post-container').fadeOut(300, function(event) {
				page = 1;
				$(this).remove();
	            $('.block-posts#' + blockid + ' .load-more').addClass('loading').fadeTo(300, 1);
			});
			setTimeout(function(){
	            // console.log('about to call load_posts!');
				spr_load_posts(blockid, false, page, searchfields);
			}, 400);
		}
	});


	$('.block-posts .sidebar .search-bar input#search').on('focus', function(event) {
		var blockid = $(this).closest('.block-posts').attr('id');
    	var searchfields = $(this).closest('.block-posts').find('.search-bar').data('searchfields');
		// console.log(blockid);
		// console.log('about to fetch stuff!');
		if ($(this).next('.search-results').find('ul').attr('data-loaded') != 'true') {
			// console.log('getting searchable stuff!');
			spr_load_posts(blockid, 'store', page, searchfields);
		}
	});
	
	
	function spr_load_posts(blockid, append, page, searchfields) {

		// console.log('spr_load_posts called with append = ' + append + ' and page = ' + page + ' and blockid = ' + blockid + '!');

		if (append == 'store') {
			var per_page = '-1';
			var template = 'list';
			var cats = '';
			var tags = '';
		} else {
			var per_page = $('.block-posts#' + blockid + ' .posts-container').data('per-page');
			var template = $('.block-posts#' + blockid + ' .posts-container').data('template');
			var cats = $('.block-posts#' + blockid + ' .posts-container').data('cats');
			var tags = $('.block-posts#' + blockid + ' .posts-container').data('tags');
		}

        var data = {
            'action': 'spr_load_posts_by_ajax',
            'page': page,
			'posts_per_page': per_page,
			'post-type': $('.block-posts#' + blockid + ' .posts-container').data('post-type'),
			'template': template,
			'date': $('.block-posts#' + blockid + ' .posts-container').data('date'),
			'excerpt': $('.block-posts#' + blockid + ' .posts-container').data('excerpt'),
			'columns': $('.block-posts#' + blockid + ' .posts-container').data('columns'),
			'filters': $('.block-posts#' + blockid + ' .posts-container').data('filters'),
			'cats': cats,
			'tags': tags,
			'orderby': $('.block-posts#' + blockid + ' .posts-container').data('orderby'),
			'order': $('.block-posts#' + blockid + ' .posts-container').data('order'),
			'searchfields': searchfields,
            'security': posts.security
        };

        // console.log(data);
  
        $.post(posts.ajaxurl, data, function(response) {
			// console.log('page: ' + page);
            if($.trim(response) != '') {
				if (append == true) {
					// console.log('appending response to .posts-container!');
					// console.log(response);
					var loadmore = $('.block-posts#' + blockid + ' .load-more').detach();
                	$('.block-posts#' + blockid + ' .posts-container').append(response);
	                loadmore.appendTo($('.block-posts#' + blockid + ' .posts-container'));
					// console.log('fading in .results-page!');
					setTimeout(function(){
						// console.log('window loaded: loading more posts...');
						$('.block-posts#' + blockid + ' .load-more').trigger('click');
					}, 300);
					$('.block-posts#' + blockid + ' .posts-container .post-container[data-page="' + page + '"]').fadeIn(300, function(event) {
						$('.block-posts#' + blockid + ' .posts-container .post-container[data-page="' + page + '"]').removeClass('just-loaded');
						$('.block-posts#' + blockid + ' .load-more').removeClass('loading');
						$('.block-posts#' + blockid + ' .posts-container').attr('data-current-page', page).data('current-page', page);
						// console.log('Current page: ' + $('.block-posts .posts-container').data('current-page'));
					});
				} else if (append == false) {
					// console.log('replacing .posts-container with response!');
					// console.log(response);
					$('.block-posts#' + blockid + ' .load-more').fadeOut(300, function(event) {
						var loadmore = $('.block-posts#' + blockid + ' .load-more').detach();
	                	$('.block-posts#' + blockid + ' .posts-container').html(response);
	                	loadmore.appendTo($('.block-posts#' + blockid + ' .posts-container'));
	                	$('.block-posts#' + blockid + ' .load-more').removeClass('loading').fadeOut(300);
						// console.log('about to fade new ones in!');
						var count = 0;
						// console.log('page = ' + page);
						$('.block-posts#' + blockid + ' .posts-container .post-container[data-page="' + page + '"]').fadeIn(300, function(event) {
							// console.log('new ones faded in!');
							$(this).removeClass('just-loaded');
							$('.block-posts#' + blockid + ' .load-more').removeClass('loading');
							$('.block-posts#' + blockid + ' .posts-container').attr('data-current-page', page).data('current-page', page);
							count++;
							// console.log(count);
						});
						setTimeout(function(){
							$('.block-posts#' + blockid + ' .active-tags h3 span.count').text(count);
							$('.block-posts#' + blockid + ' .active-tags h3').fadeTo(300, 1);
						}, 100);
					});
					// console.log('fading in .results-page!');
					setTimeout(function(){
						// console.log('window loaded: loading more posts...');
						$('.block-posts#' + blockid + ' .load-more').trigger('click');
					}, 300);
				} else if (append == 'store') {
					// console.log(response);
					$('.block-posts#' + blockid + ' .sidebar .search-bar .search-results ul').html(response).attr('data-loaded', 'true');
					$('.block-posts#' + blockid + ' .sidebar .search-bar .search-results .loading').hide();
					$('.block-posts#' + blockid + ' .sidebar .search-bar .search-results ul li').each(function(index) {
						$(this).data('search', $(this).attr('data-search')).removeAttr('data-search');
					});
					if ($(this).closest('.block-posts').find('.posts-container').data('searchterms') === null) {
						$('.block-posts#' + blockid + ' .sidebar .search-bar input#search').keyup();
					}
				}
				$('.block-posts#' + blockid + ' .load-more').fadeOut(300);
            } else {
				// console.log('No results!');
                $('.block-posts#' + blockid + ' .load-more').fadeOut(300, function(event) {
					$('.block-posts#' + blockid + ' .load-more').removeClass('loading');
					// console.log('Adding .hide');
					$('.block-posts#' + blockid + ' .load-more').addClass('hide');
					if ($('.block-posts#' + blockid + ' .posts-container').data('current-page') == 0) {
						$('.block-posts#' + blockid + ' .no-results').fadeIn();
					}
				});
				// console.log('Current page: ' + $('.block-posts .posts-container').data('current-page'));
            }
			// console.log('<========================>');
        });
		
	}
	
});