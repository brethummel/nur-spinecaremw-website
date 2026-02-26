// JavaScript Document

jQuery(document).ready(function($) { 	
	
	// console.log('header.js loaded successfully!');
	
	if ($(document).scrollTop() > 20) {
		$('header').addClass('scrolled');
	}
	
    $(window).scroll(function() {
        if ($(document).scrollTop() > 20) {
            $('header').addClass('scrolled');
        }
        else {
            $('header').removeClass('scrolled');
        }
    });
	
	let resetmenu = null;
	// submenu rollover (desktop)
	$('header .menu > ul > li > a').on('mouseenter', function(event) {
		event.preventDefault();
        if (resetmenu != null) {
        	clearTimeout(resetmenu);
        	resetmenu = null;
        }
        // console.log($(this).css('display'));
		if (!$(this).closest('.menu').hasClass('locked') && $(this).closest('li').css('display') != 'block' && $(this).closest('li').find('.drop').length !== 0) {
			$(this).closest('.menu').addClass('locked');
			$('header .menu').find('.drop').slideUp(350);
			$('header .menu > ul > li').removeClass('open');
			if ($(this).closest('li').find('.drop').length !== 0 && $(this).closest('li').css('display') != 'block') {
				$(this).closest('li').addClass('open');
				$(this).closest('li').children('.drop').slideDown(350, function(event) {
					$(this).closest('.menu').removeClass('locked');
				});
			}
		}
	});

	$('header .menu > ul > li > a').on('click', function(event) {
		if (!$(this).hasClass('open')) {
			$(this).trigger('mouseenter');
		} else {
			$(this).trigger('mouseleave');
		}
	});
	
	$('header .menu > ul > li').on('mouseleave', function(event) {
		if (!$(this).closest('.menu').hasClass('locked')) {
			if ($(this).find('.drop').length !== 0 && $(this).css('display') != 'block') {
				$(this).removeClass('open');
				$(this).find('.drop').slideUp(350, function(event) {
					$(this).closest('.menu').removeClass('locked');
					// console.log('removed lock on .main-menu');
				});
			}
		}
	});

	$(document).mousemove(function(){
        if ($("header:hover").length == 0 && $('header .header-bottom').css('display') != 'block') {
        	// console.log($('.menu >ul >li').css('display'));
            if (resetmenu == null) {
				resetmenu = setTimeout(function() {
					$('.menu ul li').removeClass('open').find('.drop').slideUp(350).closest('.menu').removeClass('locked');
					resetNav();
				}, 1000);
            }
        }
    });
	
	window.closeMenu = function() {
		$('header .header-bottom').addClass('locked');
		$('header .header-bottom').slideUp(400, function() {
			// $('header .hamburger img').attr("src", imageSrc);
			$('header .header-bottom').removeClass('locked open');
			// $('.main-menu li.open .drop').slideUp(300);
			// $('.main-menu li.open').removeClass('open');
			// $('body').removeClass('no-scroll');
			$('header').removeClass('menu-open scrolled');
			if ($(document).scrollTop() < 20) {
				$('header').removeClass('scrolled');
			}
			//resetNav();
		});
	}
	
    // $(document).on("click", function(event){
    //     var $trigger = $('.menu > ul > li');
    //     if ($trigger !== event.target && !$trigger.has(event.target).length) {
    //     	if ($('header .header-bottom').css('display') != 'block') { // desktop
	// 			$('header .main-menu .drop').slideUp(200, function(event) {
	// 				$('header .main-menu').removeClass('locked');
	// 				$(this).parent().removeClass('open');
	// 			});
    //     	} else { // mobile
    //     		// console.log('evaluating what to do for mobile!');
    //     		// if (event.target !== $('header .hamburger .hamburger-container') && !$trigger.has(event.target).length) {
    //     		// 	// console.log('the click was not on the hamburger!');
    //     		// 	$('header .hamburger .hamburger-container').trigger('click');
    //     		// }
    //     	}
    //     }            
    // });
	
    $('header .hamburger .hamburger-container').on('click', function(event) {
        if (!$('header .header-bottom').hasClass('locked')) {
            if ($('header .header-bottom').hasClass('open')) {
				$('header .header-bottom').removeClass('transition-ready');
                closeMenu();
            } else {
                $('header .header-bottom').addClass('locked');
                // $('header .hamburger img').attr("src", imageSrc);
				$('header').addClass('scrolled menu-open');
                $('header .header-bottom').slideDown(400, function() {
                    $('header .header-bottom').removeClass('locked');
                    $('header .header-bottom').addClass('open');
                    // $('body').addClass('no-scroll');
	                setTimeout(function() {
						$('header .header-bottom').addClass('transition-ready');
					}, 500);
                });
            }
        }
	});
	
	// submenu click (mobile)
	$('header .menu li.has-submenu').click(function(event) {
		if ($(this).css('display') == 'block') {
			if (!$(this).hasClass('open')) {
				console.log('menu item is not open!');
				event.preventDefault();
				$('header .header-bottom .menu-reset').slideDown(350);
				$('.menu li.open').removeClass('open');
				$(this).addClass('open');
				$('header .header-bottom .menu').addClass('slide-collapse');
				if ($(this).children('.mega').length) {
					$('header .header-bottom').addClass('mega-open');
				}
			}
		}
	});

	$('header .header-bottom .menu-reset').on('click', function(event) {
		resetNav();
	});
	
	window.closeMenu = function() {
		$('header .header-bottom').addClass('locked');
		$('header .header-bottom').slideUp(400, function() {
			// $('header .hamburger img').attr("src", imageSrc);
			$('header .header-bottom').removeClass('locked open');
			// $('.main-menu li.open .drop').slideUp(300);
			// $('.main-menu li.open').removeClass('open');
			// $('body').removeClass('no-scroll');
			$('header').removeClass('menu-open scrolled');
			if ($(document).scrollTop() < 20) {
				$('header').removeClass('scrolled');
			}
			$('header .header-bottom').removeClass('mega-open');
			resetNav();
		});
	}
	
	window.resetNav = function() {
		$('header .header-bottom .menu').removeClass('slide-collapse');
		$('.menu li.open').removeClass('open');
		$('header .header-bottom .menu-reset').slideUp(350);
		$('header .header-bottom').removeClass('mega-open');
	}
    
/*    $('header .hamburger img').on('click', function(event) {
        if (!$('header .main-menu').hasClass('locked')) {
            imageSrc = $('header .hamburger img').attr("src");
            if ($('header .main-menu').hasClass('open')) {
                $('header .main-menu').addClass('locked');
                $('header .main-menu').slideUp(250, function() {
                    imageSrc = imageSrc.replace("menu_close.png", "menu.png");
                    $('header .hamburger img').attr("src", imageSrc);
                    $('header .main-menu').removeClass('locked');
                    $('header .main-menu').removeClass('open');
                    $('body').removeClass('no-scroll');
                });
            } else {
                $('header .main-menu').addClass('locked');
                imageSrc = imageSrc.replace("menu.png", "menu_close.png");
                $('header .hamburger img').attr("src", imageSrc);
                $('header .main-menu').slideDown(250, function() {
                    $('header .main-menu').removeClass('locked');
                    $('header .main-menu').addClass('open');
                    $('body').addClass('no-scroll');
                });
            }
        }
	});*/
	
});