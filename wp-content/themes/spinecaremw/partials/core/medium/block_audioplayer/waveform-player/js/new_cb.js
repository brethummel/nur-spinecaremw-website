"use strict"

//############################################//
/* callbacks */
//############################################//

function awpSetupDone(instance, instanceName){
	/* called when component is ready to use public API. Returns player instance, sound id. */
	//console.log('awpSetupDone: ', instanceName);
}
function awpPlaylistEnd(instance, instanceName){
	/* called when current playlists reaches end. Returns player instance, sound id. */
	//console.log('awpPlaylistEnd: ');
} 
function awpMediaStart(instance, instanceName, counter){
	/* called when media starts. Returns player instance, instanceName, media counter. */
	//console.log('awpMediaStart: ', counter);
}
function awpMediaPlay(instance, instanceName, item, counter){
	/* called when media is played. Returns player instance, instanceName, media counter. */
	//console.log('awpMediaPlay: ', instanceName);

	if(item && instance.hasClass('awp-play-overlay')){
		item.find('.awp-playlist-thumb-style').addClass('awp-playlist-thumb-style-pause');
	}
}
function awpMediaPause(instance, instanceName, item, counter){
	/* called when media is paused. Returns player instance, instanceName, media counter. */
	//console.log('awpMediaPause: ', instanceName);

	if(item && instance.hasClass('awp-play-overlay')){
		item.find('.awp-playlist-thumb-style').removeClass('awp-playlist-thumb-style-pause');
	}
}
function awpMediaEnd(instance, instanceName, counter){
	/* called when media ends. Returns player instance, instanceName, media counter. */
	//console.log('awpMediaEnd: ', counter);
}
function awpPlaylistStartLoad(instance, instanceName){
	/* called when playlist load starts. Returns player instance, instanceName. */
	//console.log('awpPlaylistStartLoad: ', instanceName);
}
function awpPlaylistEndLoad(instance, instanceName, playlistContent){
	/* called when playlist load ends. Returns player instance, instanceName. */
	//console.log('awpPlaylistEndLoad: ', instanceName);

	if(playlistContent.length && instance.hasClass('awp-masonry')){
    	
    	if(playlistContent.hasClass('awp-masonry'))playlistContent.masonry('destroy');

		var grid = playlistContent.masonry({
		    itemSelector: '.awp-playlist-item',
		});

		instance.masonry = grid;
		playlistContent.addClass('awp-masonry')

		grid.imagesLoaded(function(){
				grid.masonry('layout');
			}).progress(function() {
		    //grid.masonry('layout');
		});

		playlistContent.find('.awp-playlist-item').each(function(){
			var item = jQuery(this);
			if(item.find('.awp-playlist-thumb-style').length == 0){
				item.find('.awp-playlist-thumb').after(jQuery('<div class="awp-playlist-thumb-style"/>'));
			}

		});	

	}
	else if(instance.hasClass('awp-play-overlay')){
		playlistContent.find('.awp-playlist-item').each(function(){
			var item = jQuery(this);
			if(item.find('.awp-playlist-thumb-style').length == 0){
				item.find('.awp-playlist-thumb').after(jQuery('<div class="awp-playlist-thumb-style"></div>'));
			}
		});
	}
	
}

function awpItemTriggered(instance, instanceName, counter){
	/* called when new sound is triggered. Returns player instance, instanceName, media counter. */
	//console.log('awpItemTriggered: ', instanceName, counter);
}
function awpPlaylistItemEnabled(instance, instanceName, item, id){
	/* called on playlist item enable. Returns player instance, instanceName, playlist item, playlist item id (playlist items have data-id attributes starting from 0). */
	//console.log('awpPlaylistItemEnabled: ');
}
function awpPlaylistItemDisabled(instance, instanceName, item, id){
	/* called on playlist item disable. Returns player instance, instanceName, playlist item, playlist item id (playlist items have data-id attributes starting from 0). */
	//console.log('awpPlaylistItemDisabled: ');

}
function awpPlaylistItemClick(instance, instanceName, target, counter){
	/* called on playlist item click. Returns player instance, instanceName, playlist item (target), media counter. */
	//console.log('awpPlaylistItemClick: ', counter);
}
function awpPlaylistItemRollover(instance, instanceName, target, id){
	/* called on playlist item mouseenter. Returns player instance, instanceName, playlist item (target), playlist item id (playlist items have data-id attributes starting from 0). */
	//console.log('awpPlaylistItemRollover: ', id);
}
function awpPlaylistItemRollout(instance, instanceName, target, id, active){
	/* called on playlist item mouseleave. Returns player instance, instanceName, playlist item (target), playlist item id (playlist items have data-id attributes starting from 0), active (is active playlist item, boolean). */
	//console.log('awpPlaylistItemRollout: ', id);
}
function awpMediaEmpty(instance, instanceName){
	/* called when active media is removed from the playlist. Returns player instance, instanceName. */
	//console.log('awpMediaEmpty: ', instanceName);
}
function awpPlaylistEmpty(instance, instanceName, playlistContent){
	/* called when playlist becomes empty (no items in playlist after new playlist has been created or last playlist item removed from playlist, NOT after destroyPlaylist!). Returns player instance, instanceName. */
	//console.log('awpPlaylistEmpty: ', instanceName);
}
function awpCleanMedia(instance, instanceName){
	/* called when active media is destroyed. Returns player instance, instanceName. */
	//console.log('awpCleanMedia: ', instanceName);
}
function awpDestroyPlaylist(instance, instanceName, playlistContent){
	/* called when active playlist is destroyed. Returns player instance, instanceName. */
	//console.log('awpDestroyPlaylist: ', instanceName);
	if(playlistContent.length && instance.hasClass('awp-masonry')){
		if(playlistContent.hasClass('awp-masonry'))playlistContent.masonry('destroy');
	}
}
function awpVolumeChange(instance, instanceName, volume){
	/* called on volume change. Returns player instance, instanceName, volume. */
}
function awpFilterChange(instance, instanceName, playlistContent){
	/* called after filter playlist items. Returns player instance, instanceName. */
	//console.log('awpFilterChange: ', instanceName);
	if(playlistContent.length && instance.hasClass('awp-masonry')){
		if(playlistContent.hasClass('awp-masonry'))playlistContent.masonry('layout');
	}
}


/* END PLAYER CALLBACKS */

	

