jQuery(document).ready(function($) { 
    
    // console.log("successfully loaded lead_attribution.js");
    
    var debug = false;
	
	// ======================================= //
	//            UTLITY FUNCTIONS             //
	// ======================================= // 
	
	var logger = function() {
		var oldConsoleLog = null;
		var pub = {};
		pub.enableLogger =  function enableLogger() {
			if(oldConsoleLog == null)
				return;
			window['console']['log'] = oldConsoleLog;
		};
		pub.disableLogger = function disableLogger() {
			oldConsoleLog = console.log;
			window['console']['log'] = function() {};
		};
		return pub;
	}();
	
	function getCookie(name) {
		let matches = document.cookie.match(new RegExp(
			"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
		));
		return matches ? decodeURIComponent(matches[1]) : undefined;
	}
	
	function setCookie(name, value, options = {}) {
		options = {
			path: '/',
			// add other defaults here if necessary
			...options
		};
		if (options.expires instanceof Date) {
			// console.log('inside option.expires fixer!');
			options.expires = options.expires.toUTCString();
		}
		let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);
		for (let optionKey in options) {
			updatedCookie += "; " + optionKey;
			let optionValue = options[optionKey];
			if (optionValue !== true) {
				updatedCookie += "=" + optionValue;
			}
		}
		document.cookie = updatedCookie;
	}
	
	function leadingZeros(value) { 
		return (value < 10 ? '0' : '') + value;
	}
	
	var dt = new Date();
	var daypart;
	if (dt.getHours() > 12) {
		daypart = 'pm';
	} else {
		daypart = 'am';
	}
	var timestamp = (dt.getMonth()+1) + '/' + dt.getDate() + '/' + dt.getFullYear() + ' ' + ((dt.getHours() + 11) % 12 + 1) + ":" + leadingZeros(dt.getMinutes()) + daypart;
	
	if (debug != true) {
		logger.disableLogger();
	}
	
	var cookies_enabled = navigator.cookieEnabled;
	// console.log("Cookies enabled: " + cookies_enabled);
	
	
	// ======================================= //
	//        CAPTURE / RETRIEVE VALUES        //
	// ======================================= // 
	
	var urlParams = new URLSearchParams(window.location.search);
	
	if (urlParams.has('utm_source')) {
		// console.log('Has utm_source! Setting vars...');
		var campaign = urlParams.get('utm_campaign');
		var medium = urlParams.get('utm_medium');
		var source = urlParams.get('utm_source');
		var attrsrc = 'utm_source-params';
	} else if (urlParams.has('source')) {
		// console.log('Has source! Setting vars...');
		var campaign = urlParams.get('campaign');
		var medium = urlParams.get('medium');
		var source = urlParams.get('source');
		var attrsrc = 'source-params';
	} else {
		// console.log('No query string! Setting vars from fallbacks...');
		var campaign = $('body .attribution .data').data('campaign');
		var source = $('body .attribution .data').data('source');
		var medium = $('body .attribution .data').data('medium');
		var attrsrc = $('body .attribution .data').data('attrsrc');
	}
	// var environment = $('body main section').find('form div.spr-environment').find('.ginput_container textarea').val();
	// if (environment == null) {
	// 	environment = '';
	// }
	// if (medium == null) {
	// 	medium = '';
	// }
	// environment += '\r\nCookies enabled: ' + cookies_enabled;
	// console.log('campaign: ' + campaign);
	// console.log('source: ' + source);
	// console.log('medium: ' + medium);
	// console.log('attrsrc: ' + attrsrc);
	// console.log('timestamp: ' + timestamp);
	// console.log('environment: ' + environment);
	
	
	// ======================================= //
	//     UPDATE / SET TOUCHPOINTS COOKIE     //
	// ======================================= // 
	
	// if spr_touchpoints cookie exists, grab info, otherwise initialize empty var
	var touchpoints;
	if (cookies_enabled) {
		if (getCookie('spr_touchpoints') != null) {
			touchpoints = getCookie('spr_touchpoints');
			// console.log('Existing spr_touchpoints values:');
			// console.log(touchpoints);
		} else {
			touchpoints = '';
		}
	
		var domain = $('body .attribution .data').data('domain');
		// console.log(domain);
		var lifespan = $('body .attribution .data').data('lifespan');

		// add activity to cookie if current URL has campaign/source parameters
		if (urlParams.has('attrsrc')) { // only exists on thankyou pages so must record the conversion as a touchpoint
			// console.log('attrsrc parameter found!');
			touchpoints += urlParams.get('source') + ',' + urlParams.get('campaign') + ',' + urlParams.get('medium') + ',' + urlParams.get('attrsrc') + ',' + timestamp + '|';
			setCookie('spr_touchpoints', touchpoints, {domain: domain, 'max-age': lifespan});
		} else if (attrsrc == 'utm_source-params' || attrsrc == 'source-params') {
			// console.log('no attrsrc parameter found!');
			touchpoints += source + ',' + campaign + ',' + medium + ',' + attrsrc + ',' + timestamp + '|';
			// console.log(domain);
			// console.log(lifespan);
			setCookie('spr_touchpoints', touchpoints, {domain: domain, 'max-age': lifespan});
		}
		
	} else {
		// if cookies are not enabled, report in visits field
		touchpoints = '';
		visits = 'Cookies are not enabled!';
		// console.log('cookies are not enabled!');
	}
	
	// environment += '\r\nTouchpoints: ' + touchpoints + '\r\n';
	// console.log('environment:');
	// console.log(environment);
	
	// console.log('New spr_touchpoints values:');
	// console.log(getCookie('spr_touchpoints'));
	
	
	// ======================================= //
	//      UPDATE / SET PAGEVISITS COOKIE     //
	// ======================================= // 
	
	// capture page visit
	if (getCookie('spr_pagevisits') != null) {
		var pagevisits = getCookie('spr_pagevisits');
		// console.log('Existing spr_pagevisits values:');
		// console.log(pagevisits);
	} else {
		var pagevisits = '';
	}
	
	var mf;
	if (urlParams.has('fw') && urlParams.get('fw') == 1) {
		mf = urlParams.get('mf');
		pagevisits += timestamp + ' : ' + decodeURIComponent(mf) + $('body .attribution .data').data('delimiter');
	} else {
		pagevisits += timestamp + ' : ' + $('body .attribution .data').data('slug') + $('body .attribution .data').data('delimiter');
	}
	$('body .attribution .data .visits').text(pagevisits);
	setCookie('spr_pagevisits', pagevisits, {'domain': domain, 'max-age': lifespan});
	
	// console.log('New spr_pagevisits values:');
	// console.log(getCookie('spr_pagevisits'));
	
	
	// ======================================= //
	//        REDIRECT FOR ATTACHMENTS         //
	// ======================================= // 
	
	// if (urlParams.has('fw') && urlParams.get('fw') == 1) {
	// 	var destination = $('body .attribution .data').data('siteurl') + '/wp-content/uploads/' + decodeURIComponent(mf);
	// 	location.replace(destination);
	// }
	
	
	// ======================================= //
	//      DETECT WHEN CODE FORM LOADS        //
	// ======================================= // 


	var formobserver = new MutationObserver(mutationhandler);

	function mutationhandler(mutations) {
		mutations.forEach(function(mutation) {
			// console.log('mutation target:');
	        // console.log(mutation.target);
	        targetblock = $(mutation.target).closest('.block-contactform');
			// console.log('targetblock:');
	        // console.log(targetblock);
	        if (mutation.type == 'childList') {
				populatefields(targetblock, campaignfield, sourcefield, mediumfield, attrsrcfield, visitsfield);
	        }
	    });
	    formobserver.disconnect();
	}

	var observerconfig = {
		attributes: true,
		childList: true,
		characterData: true
	};

	// var target = document.querySelector('.block-contactform .form-col');
	// formobserver.observe(target, observerconfig);

	let campaignfield;
	let sourcefield;
	let mediumfield;
	let attrsrcfield;
	let visitsfield;
	
	// ======================================= //
	//       DETERMINE ATTRIBUTION VALUES      //
	// ======================================= // 
	// console.log($('body main section').find('form div.spr-campaign').length);
	// console.log($('body main section').find('form div.spr-campaign').find('.ginput_container input').length);
	
    if ($('body .content-container').find('.block-contactform.form').length > 0) {
		
		// determine attribution
		var model = $('body .attribution .data').data('model');

		touchpoints = touchpoints.split('|'); // always results in empty last element
		// console.log(touchpoints);
		touchpoints.pop(); // delete last element
		// console.log('touchpoints:');
		// console.log(touchpoints);
		var attribution = [];

		if (model == 'first') {
			// console.log('model = first');
			// console.log(touchpoints[0]);
			if (touchpoints[0] != undefined) {
				attribution = touchpoints[0].split(',');
			}
		} else if (model == 'last') {
			// console.log('model = last');
			attribution = [source, campaign, medium, attrsrc, timestamp];
		} else if (model == 'last-non-direct') {
			// console.log('model = last-non-direct');
			touchpoints.reverse();
			$.each(touchpoints, function(index, value) {
				var touchpoint = value.split(',');
				var position;
				if (touchpoint.length == 3) {
					position = 2;
					timestamp = 'not captured';
				} else if (touchpoint.length == 4) {
					position = 3;
					timestamp = 'not captured';
				} else {
					position = 3;
				}
				if (touchpoint[position] == 'utm_source-params' || touchpoint[position] == 'source-params') {
					// console.log(touchpoint);
					attribution = touchpoint;
					// console.log('last non-direct touchpoint:');
					// console.log(attribution);
					return false;
				}
			});
		}
		if (attribution == null || attribution.length == 0) {
			// console.log(attribution);
			// console.log('attribution is not set, assigning fallbacks!');
			attribution = [source, campaign, medium, attrsrc, timestamp];
		}
		// console.log('attribution touchpoint:');
		// console.log(attribution);

		if (attribution.length == 3) {
			// console.log('found only three parameters!');
			source = attribution[0];
			campaign = attribution[1];
			medium = '';
			attrsrc = attribution[2];
		} else if (attribution.length == 4) {
			// console.log('found four parameters!');
			source = attribution[0];
			campaign = attribution[1];
			medium = attribution[2];
			attrsrc = attribution[3];
		} else {
			source = attribution[0];
			campaign = attribution[1];
			medium = attribution[2];
			attrsrc = attribution[3];
			timestamp = attribution[4];
		}
		if (medium == null) {
			medium = '';
		}
		var visits = $('body .attribution .data .visits').text();

		// get fieldnames from forms
		$('body .content-container').find('.block-contactform.form').each(function(index) {

			campaignfield = $(this).data('campaign-field');
			sourcefield = $(this).data('source-field');
			mediumfield = $(this).data('medium-field');
			attrsrcfield = $(this).data('attrsrc-field');
			visitsfield = $(this).data('visits-field');

			//populate fields

			if ($(this).hasClass('code')) {
				var target = $(this).find('.form-col');
				target.each(function() {
					formobserver.observe(this, observerconfig);
				});
			} else {
				populatefields($(this), campaignfield, sourcefield, mediumfield, attrsrcfield, visitsfield);
			}
		});
		
	}

	// ======================================= //
	//       POPULATE ATTRIBUTION FIELDS       //
	// ======================================= // 

	function populatefields(targetblock, campaignfield, sourcefield, mediumfield, attrsrcfield, visitsfield) {

		// console.log('populatefields called for target ' + targetblock);
		// console.log(targetblock);
		visits = visits.replace(/ \| /g, '\r\n');

		if (targetblock.hasClass('cognito')) {
			var json = '{';
			json += JSON.stringify(campaignfield) + ': ' + JSON.stringify(campaign) + ', ';
			json += JSON.stringify(sourcefield) + ': ' + JSON.stringify(source) + ', ';
			json += JSON.stringify(mediumfield) + ': ' + JSON.stringify(medium) + ', ';
			json += JSON.stringify(attrsrcfield) + ': ' + JSON.stringify(attrsrc) + ', ';
			json += JSON.stringify(visitsfield) + ': ' + JSON.stringify(visits);
			json += '}';
			// console.log(json);
			Cognito.prefill(json);
		} else {

			//determine if user-specified fields as class name or id
			if ($('#' + campaignfield).length > 0) {
				campaignfield = '#' + campaignfield;
				sourcefield = '#' + sourcefield;
				mediumfield = '#' + mediumfield;
				attrsrcfield = '#' + attrsrcfield;
				visitsfield = '#' + visitsfield;
			} else if ($('.' + campaignfield).length > 0) {
				campaignfield = '.' + campaignfield;
				sourcefield = '.' + sourcefield;
				mediumfield = '.' + mediumfield;
				attrsrcfield = '.' + attrsrcfield;
				visitsfield = '.' + visitsfield;
			}

			//determine if user-specified field is an input or parent of input
			// console.log(targetblock.find(campaignfield).prop('tagName').toLowerCase());
			let elementtype = targetblock.find(campaignfield).prop('tagName').toLowerCase();
			if (elementtype != 'input' && elementtype != 'textarea') {
				targetblock.find('form ' + campaignfield).find('input').val(campaign);
				targetblock.find('form ' + sourcefield).find('input').val(source);
				targetblock.find('form ' + mediumfield).find('input').val(medium);
				targetblock.find('form ' + attrsrcfield).find('input').val(attrsrc);
				targetblock.find('form ' + visitsfield).find('textarea').val(visits);
			} else {
				targetblock.find('form ' + campaignfield).val(campaign);
				targetblock.find('form ' + sourcefield).val(source);
				targetblock.find('form ' + mediumfield).val(medium);
				targetblock.find('form ' + attrsrcfield).val(attrsrc);
				targetblock.find('form ' + visitsfield).val(visits);
			}

		}
	}
	
	if (debug != true) {
		logger.enableLogger();
	}
	
});