jQuery(document).ready(function($) { 
    
    console.log("successfully loaded block_map.js");
	
	var mypath = $('.block-map #mapplic').data('path');
	var config = $('.block-map #mapplic').data('config');
	
	
	$('.block-map #mapplic').mapplic({
		source: mypath + config.source,
		sidebar: config.sidebar,
		zoommargin: 0,
		fullscreen: false,
		maxscale: 3,
		deeplinking: false,
		mousewheel: false,
	});
	
	var address = encodeURI("〒850-0963 長崎県長崎市ダイヤランド3丁目 Nagasaki 850-0963 Japan");
	geolocate(address);
	
	function geolocate(address) {
		
		var ajaxURL = mypath + "/block_map_api.php";

		var request = $.ajax({
			url: ajaxURL,
			data: 'action=geolocate&address=' + address,
			xhrFields: {
				withCredentials: true
			},
			type: "POST"
		});

		request.done(function(response) {
			response = $.parseJSON(response);
			// console.log(response['results'][0]['address_components']);
			var address_components = response['results'][0]['address_components'];
			$.each(address_components, function(index, value) {
				if ($.inArray("country", value['types']) > -1) {
					var country = value['long_name'];
					var country_code = value['short_name'];
					// console.log(value['short_name']);
				}
			});
			// console.log(response['results'][0]['geometry']['location']);
		});

		request.fail(function(jqXHR, textStatus) {
			response = [];
			response['response'] = 3;
			response['response_code'] = '500';
			response['responsetext'] = textStatus;
			console.log(response);
		}); 
		
	}
    
    
});