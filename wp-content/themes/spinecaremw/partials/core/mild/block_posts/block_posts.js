jQuery(document).ready(function($) { 
    
    // console.log("successfully loaded block_posts.js");

    $('.block-posts .posts-container .post-container').removeClass('just-loaded');
	
	// console.log('setting timeout to check for load mores...');
	setTimeout(function() {
		if ($('.block-posts .load-more').length > 0) {
			// console.log('found some load mores! Setting timeout.');
			setTimeout(function(){
				// console.log('running timeout load more...');
				$('.block-posts .load-more').trigger('click');
			}, 1500);
		}
	}, 500);
    
    $('.block-posts .filter-row .filters ul li').on('click', function(event) {
    	var blockid = $(this).closest('.block-posts').attr('id');
		// console.log(blockid);
		$('.block-posts#' + blockid + ' .load-more').removeClass('hide');
		$('.block-posts#' + blockid + ' .no-results').removeClass('show');
		$('.block-posts#' + blockid + ' .load-more').addClass('loading');
		if (!$(this).hasClass('active')) {
			$(this).addClass('active');
		} else {
			$(this).removeClass('active');
		}
		active = [];
		inactive = [];
		$(this).parent().children('li').each(function(index) {
			if ($(this).hasClass('active')) {
				active.push($(this).attr('id'));
			} else {
				inactive.push($(this).attr('id'));
				viewing = 'some';
			}
			if (inactive.length == 0) {
				viewing = 'all';
			}
		});
		if (active.length == 0) {
			active = inactive;
			viewing = 'all';
		}
		// console.log(active);
		activeList = active.join(', ');
		$(this).closest('.filters').data('active', activeList);
		$(this).closest('.filters').data('viewing', viewing);
		// console.log($(this).closest('.filters').data('active'));
		// console.log($(this).closest('.filters').data('viewing'));
		
		activeTitle = '';
		if (viewing == 'all') {
			activeTitle = 'All Resources';
		} else {
			$.each(active, function(index, value) {
				if (index > 0) {
					if (index < (active.length - 1)) {
						activeTitle += ', ';
					} else {
						activeTitle += ' & ';
					}
				}
				activeTitle += $('.block-posts#' + blockid + ' .filter-row .filters ul li#' + value).text();
				// console.log(value);
				// console.log($('.block-posts .filter-row .filters ul li#' + value).text());
			});
		}
		// console.log(activeTitle);
		$(this).closest('.filters').siblings('h2.viewing').html(activeTitle);
		
    });

	function escapeRegExp(str) {
		return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // $& means the whole matched string
	}

	const searchInput = document.querySelector('.block-posts .sidebar .search-bar input#search');

	if (searchInput != null) {
		searchInput.addEventListener('search', function(event) {
			if (!event.target.value) {
				var blockid = $(this).closest('.block-posts').attr('id');
				$('.block-posts#' + blockid).find('.search-results').hide();
			}
		});
	}

    $('.block-posts .sidebar .search-bar input#search').off('keyup').on('keyup', function(event) {
		var blockid = $(this).closest('.block-posts').attr('id');
        searchterms = $(this).val().toLowerCase().split(' ');
        searchlength = $(this).val().length;
        // console.log('===================================================== < key up');
        if (searchlength >= 3) {
        	// console.log('keyup event w >= 3 chars!');
			$('.block-posts#' + blockid).find('.search-results').show();
			if ($(this).next('.search-results').find('ul').attr('data-loaded') == 'true') {
				searchterms = searchterms.filter(item => item); // gets rid of empty search terms
	        	// console.log(searchterms);
	        	var matches = 0;
	        	$.each($('.block-posts#' + blockid + ' .search-results ul li'), function(index, value) {
	        		// console.log($(this).data('search'));
	        		var result = $(this);
		        	var title = result.find('a').text();
		        	var newtitle = title;
	        		var resultmatches = 0;
	        		var term;
	        		$.each(searchterms, function(i, v) {
	        			if (v.length >= 3) {
			        		if (result.data('search').indexOf(v) >= 0) {
								// console.log('found a content match!');
								contentmatches = (result.data('search').match(new RegExp(escapeRegExp(v), 'g')) || []).length;
								resultmatches = resultmatches + contentmatches;
								result.find('.content-match').show();
			        		}
			        		if (result.data('title').toLowerCase().indexOf(v) >= 0) {
	        					// console.log('========== NEW ARTICLE =============');
	        					// console.log(title);
			        			var start = result.data('title').toLowerCase().indexOf(v);
								// console.log('found a title match!');
			        			term = title.substring(start, start + v.length);
			        			// console.log(term);
			        			newtitle = newtitle.replace(new RegExp(escapeRegExp(term), 'g'), '<strong>' + term + '</strong>');
			        			newtitle = newtitle.replace(new RegExp(escapeRegExp(v), 'g'), '<strong>' + v + '</strong>');
								resultmatches++;
								result.find('.content-match').hide();
			        		}
	        			}
	        		});
	        		if (resultmatches > 0) {
		        		result.find('a').html(newtitle);
		        		title = newtitle;
	        		} else {
		        		result.find('a').html(result.data('title'));
	        		}
	        		result.attr('data-relevance', resultmatches);
	    			// console.log('result matches: ' + resultmatches);
	        		if (resultmatches > 0) {
	        			result.addClass('match').show();
	        			matches++;
	        		} else {
	        			result.hide();
	        		}
	        	});
	        	// console.log(matches);
	    		if (matches > 0) {
	    			if (matches > 3) {
	    				$('.block-posts#' + blockid + ' .search-results .all-results').show();
	    				var allresults = new URL($('.block-posts#' + blockid + ' .search-results .all-results a').attr('href'));
	    				allresults.searchParams.set('sc', searchterms.join(','));
	    				var matchids = [];
						$('.block-posts#' + blockid + ' .sidebar .search-bar .search-results ul li.match').each(function(index) {
							matchids.push($(this).data('id'));
						});
	    				allresults.searchParams.set('mi', matchids.join(','));
	    				$('.block-posts#' + blockid + ' .search-results .all-results a').attr('href', allresults.href);
	    			} else {
	    				$('.block-posts#' + blockid + ' .search-results .all-results').hide();
	    			}
	    			$('.block-posts#' + blockid + ' .search-results .no-results').hide();
	    		} else {
	    			$('.block-posts#' + blockid + ' .search-results .all-results').hide();
	    			$('.block-posts#' + blockid + ' .search-results .no-results').show();
	    		}
	    		// console.log('matches: ' + matches);
	    		$results = $('.block-posts#' + blockid + ' .search-results ul');

	    		$results.find('li').sort(function(a, b) {
				    return +b.dataset.relevance - +a.dataset.relevance;
				}).appendTo($results);
				$results.find('li:nth-child(n+4)').hide();
			}
        } else {
			$('.block-posts#' + blockid).find('.search-results').hide();
        }
    });
	
	$(window).on('resize scroll', function() {
		if ($('.block-posts .load-more').length > 0 && $('.block-posts .load-more').isInViewport() && !$('.block-posts .load-more').hasClass('loading')) {
			// console.log('loading more...');
			$('.block-posts .load-more').trigger('click');
		}
	});
	
	// $.fn.isInViewport = function() {
	// 	var elementTop = $(this).offset().top;
	// 	var elementBottom = elementTop + $(this).outerHeight();
	// 	var viewportTop = $(window).scrollTop();
	// 	var viewportBottom = viewportTop + $(window).height();
	// 	return elementTop < viewportBottom;
	// }; 

	$.fn.isInViewport = function() {
		if (this.is(":visible")) {
		    var win = $(window);
		    var viewport = {
		        top : win.scrollTop(),
		        left : win.scrollLeft()
		    };
		    viewport.right = viewport.left + win.width();
		    viewport.bottom = viewport.top + win.height();
		    var bounds = this.offset();
		    bounds.right = bounds.left + this.outerWidth();
		    bounds.bottom = bounds.top + this.outerHeight();
		    return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
		} else {
			return false;
		}
	};   
	
});