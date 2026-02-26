jQuery(document).ready(function($) { 
    
    console.log("successfully loaded block_audioplayer.js");
	
	var mypath = $('.block-audioplayer').data('path');
	var base = $('.block-audioplayer .base-color').css('background-color');
	var progress = hexc($('.block-audioplayer .progress-color').css('background-color'));
	var cursor = hexc($('.block-audioplayer .cursor-color').css('background-color'));
	// console.log(mypath);
	// console.log(base);
	// console.log(progress);
	
	function hexc(colorval) {
		var parts = colorval.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		delete(parts[0]);
		for (var i = 1; i <= 3; ++i) {
			parts[i] = parseInt(parts[i]).toString(16);
			if (parts[i].length == 1) parts[i] = '0' + parts[i];
		}
		hex = '#' + parts.join('');
		return hex;
	}
	
	$.getScript('https://unpkg.com/wavesurfer.js@6.6.3/dist/wavesurfer.min.js', function(){ 
		console.log("wavesurfer.js loaded.");
		$.getScript(mypath + '/waveform-player/js/new_cb.js', function(){ 
			console.log("new_cb.js loaded."); 
		});
		$.getScript(mypath + '/waveform-player/js/new.js?43', function(){ 
			console.log("new.js loaded."); 
			var settings = {
				instanceName: "default2",
				sourcePath: mypath + '/waveform-player/',
				playlistList: "#awp-playlist-list",
				activePlaylist: "#playlist-audio",
				activeItem: 0,
				volume: .75,
				autoPlay: false,
				drawWaveWithoutLoad: false,
				randomPlay: false,
				loopingOn: false,
				autoAdvanceToNextMedia: false,
				mediaEndAction: "stop",
				sck: "",
				gdk: "",
				useKeyboardNavigationForPlayback: true,
				usePlaylistScroll: true,
				playlistScrollOrientation: "vertical",
				playlistScrollTheme: "light-thin",
				useDownload: true,
				useShare: false,
				useTooltips: true,
				fbk: "",
				useNumbersInPlaylist: true,
				numberTitleSeparator: ".  ",
				artistTitleSeparator: " - ",
				playlistItemContent: "title",
				wavesurfer:{
					waveColor: base,
					progressColor: progress,
					barWidth: 3,
					cursorColor: cursor,
					cursorWidth: 1,
					height: 120,
				},
				iconLink:'<i class="fa fa-cart-plus"></i>'
			};
			awp_player = $("#awp-wrapper").awp(settings);
		});
	});
	$.getScript(mypath + '/waveform-player/js/jsmediatags.min.js', function(){ 
		console.log("jsmediatags.min.js loaded."); 
	});
	$.getScript(mypath + '/waveform-player/js/jquery.mCustomScrollbar.concat.min.js', function(){ 
		console.log("jquery.mCustomScrollbar.concat.min.js loaded."); 
	});
		
});