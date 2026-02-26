// JavaScript Document


jQuery(window).load(function($) {
	
	// ****************** FOR STATS COUNT-UP ****************** //
	
	if (jQuery('.count-up').length > 0) {
		var myval;
		jQuery('span.count-up').each(function(event) {
			myval = jQuery(this).text();
			mystart = '0';
			mydist = jQuery(this).offset().top;
			mythresh = mydist - (jQuery(window).height() * .80);
			jQuery(this).data('value', myval);
			jQuery(this).data('dist', mydist);
			jQuery(this).data('thresh', mythresh);
			jQuery(this).text(mystart);
		});
	}
	
	// ****************** END STATS COUNT-UP ****************** //
			
});

jQuery(document).ready(function($) {
	
	// console.log('global.js loaded successfully!');

	// ****************** FOR STATS COUNT-UP ****************** //
	
	if ($('.count-up').length > 0) {
		$(window).scroll(function(event) {
			// console.log('scrollTop: ' + $(window).scrollTop());
			$('span.count-up').each(function(event) {
				mythresh = parseInt($(this).data('thresh'));
				// console.log($(this).parent().attr('class') + ': ' + mythresh);
				if ($(window).scrollTop() >= mythresh && !$(this).hasClass('counted')) {
					$(this).addClass('counted');
					countUp($(this));
				}
			})
		});
	}
	
	// To count up a stat, you must surround the number with a <span></span> tag,
	// making sure to include class="count-up" and then a data attribute for the
	// increment desired (either "1" or "0.1") as such: data-inc="1". Example:
	// <span class="count-up" data-inc="1">62</span>. If there is a "units" (like,
	// "62k"), also surround the unit with a <span class="label">k</span>
		
	function countUp(element) {
		mystart = parseFloat(element.text());
		myend = parseFloat(element.data('value'));
		myinc = parseFloat(element.data('inc'));
		if (myinc == 1) {
			$({ countNum: element.text()}).animate({
				countNum: myend
			},
			{
				duration: 1500,
				easing: 'swing',
				step: function() {
					element.text(Math.floor(this.countNum));
				},
				complete: function() {
					element.text(this.countNum);
				}
			});
		}
		if (myinc == 0.1) {
			$({ countNum: element.text()}).animate({
				countNum: myend
			},
			{
				duration: 1500,
				easing: 'swing',
				step: function() {
					element.text(parseFloat(this.countNum).toFixed(1));
				},
				complete: function() {
					element.text(this.countNum);
				}
			});
		}
	}
	
	// ****************** END STATS COUNT-UP ****************** //




	// **************** TABBED REFERRAL FORMS ***************** //

	$('.for-physicians.refer-a-patient .referral-forms .block-contactform.procedure-only').hide();

	$('.for-physicians.refer-a-patient .referral-forms .block-text .column-one .content-container ul li').on('click', function(event) {
		if (!$(this).hasClass('open')) {
			$(this).siblings().removeClass('open');
			$(this).addClass('open');
			if ($(this).hasClass('new-eval')) {
				$('.for-physicians.refer-a-patient .referral-forms .block-contactform.new-evaluation').show();
				$('.for-physicians.refer-a-patient .referral-forms .block-contactform.procedure-only').hide();
			} else if ($(this).hasClass('procedure-only')) {
				$('.for-physicians.refer-a-patient .referral-forms .block-contactform.procedure-only').show();
				$('.for-physicians.refer-a-patient .referral-forms .block-contactform.new-evaluation').hide();
			}
		}
	});


	// ************** END TABBED REFERRAL FORMS *************** //

	
});
