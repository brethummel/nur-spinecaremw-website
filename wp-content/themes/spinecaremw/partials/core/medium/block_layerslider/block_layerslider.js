jQuery(document).ready(function($) { 
    
    console.log("successfully loaded block_layerslider.js");
	
	var folder = $('.block-layerslider').data('folderpath') + '/partials/custom/_templates/_layerslider/' + $('.block-layerslider').data('layerslider') + '/';
	var script = folder + $('.block-layerslider').data('layerslider') + ".js";
	$.getScript(script);
    
});