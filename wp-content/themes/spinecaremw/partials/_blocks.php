<?php

if (isset($args)) {
	$masterblocks = $args;
}
$GLOBALS['sectioncount'] = 0;

if (isset($masterblocks)) {
	display_blocks($masterblocks['before']['ID'], $masterblocks['before']['blocks']);
}
display_blocks();
if (isset($masterblocks)) {
	display_blocks($masterblocks['after']['ID'], $masterblocks['after']['blocks']);
}

function display_blocks($id='', $blocks=false) {
	if( have_rows('content_blocks', $id) ): // check if flexible content field has data
		while ( have_rows('content_blocks', $id) ) : the_row(); // loop through the rows of data

			$themedir = get_template_directory();
			$block = get_row(true);
			$layout = get_row_layout();
	
			if ($blocks) {
				$displayblock = false;
				if (in_array(get_row_index(), $blocks)) {
					$displayblock = true;
				}
			} else {
				$displayblock = true;
			}
	
			if ($displayblock) {
				
				// ======================================= //
				//            MILD (CORE) BLOCKS           //
				// ======================================= // 

				if( $layout == 'block_accordion' ):
					if (file_exists($themedir . '/partials/custom/block_accordion/block_accordion.php')) {
						get_template_part('partials/custom/block_accordion/block_accordion', null, $block);
					} else {
						get_template_part('partials/core/mild/block_accordion/block_accordion', null, $block);
					}
				elseif( $layout == 'block_anchor' ):
					if (file_exists($themedir . '/partials/custom/block_anchor/block_anchor.php')) {
						get_template_part('partials/custom/block_anchor/block_anchor', null, $block);
					} else {
						get_template_part('partials/core/mild/block_anchor/block_anchor', null, $block);
					}
				elseif( $layout == 'block_bio' ):
					if (file_exists($themedir . '/partials/custom/block_bio/block_bio.php')) {
						get_template_part('partials/custom/block_bio/block_bio', null, $block);
					} else {
						get_template_part('partials/core/mild/block_bio/block_bio', null, $block);
					}
				elseif( $layout == 'block_buttons' ):
					if (file_exists($themedir . '/partials/custom/block_buttons/block_buttons.php')) {
						get_template_part('partials/custom/block_buttons/block_buttons', null, $block);
					} else {
						get_template_part('partials/core/mild/block_buttons/block_buttons', null, $block);
					}
				elseif( $layout == 'block_contactform' ):
					if (file_exists($themedir . '/partials/custom/block_contactform/block_contactform.php')) {
						get_template_part('partials/custom/block_contactform/block_contactform', null, $block);
					} else {
						get_template_part('partials/core/mild/block_contactform/block_contactform', null, $block);
					}
				elseif( $layout == 'block_fullimage' ):
					if (file_exists($themedir . '/partials/custom/block_fullimage/block_fullimage.php')) {
						get_template_part('partials/custom/block_fullimage/block_fullimage', null, $block);
					} else {
						get_template_part('partials/core/mild/block_fullimage/block_fullimage', null, $block);
					}
				elseif( $layout == 'block_hero' ):
					if (file_exists($themedir . '/partials/custom/block_hero/block_hero.php')) {
						get_template_part('partials/custom/block_hero/block_hero', null, $block);
					} else {
						get_template_part('partials/core/mild/block_hero/block_hero', null, $block);
					}
					if (get_post_type() == 'post') { // adds published date below block_hero on posts
	//					$layout = substr($blocks[get_row_index()]['acf_fc_layout'], 6);
	//					$curpost['published_settings']['background_color'] = 'bkgnd-white|light';
	//					if ($blocks[get_row_index()][$layout . '_settings']['background']) {
	//						$curpost['published_settings']['background_color'] = $blocks[get_row_index()][$layout . '_settings']['background'];
	//					} elseif ($blocks[get_row_index()][$layout . '_settings']['background_color']) {
	//						$curpost['published_settings']['background_color'] = $blocks[get_row_index()][$layout . '_settings']['background_color'];
	//					}
	//					if (file_exists($themedir . '/partials/custom/block_published/block_published.php')) {
	//						get_template_part('partials/custom/block_published/block_published', null, $curpost);
	//					} else {
	//						get_template_part('partials/core/mild/block_published/block_published', null, $curpost);
	//					}
					} else {
						// nothing
					}
				elseif( $layout == 'block_legal' ):
					if (file_exists($themedir . '/partials/custom/block_legal/block_legal.php')) {
						get_template_part('partials/custom/block_legal/block_legal', null, $block);
					} else {
						get_template_part('partials/core/mild/block_legal/block_legal', null, $block);
					}
				elseif( $layout == 'block_peoplegrid' ):
					if (file_exists($themedir . '/partials/custom/block_peoplegrid/block_peoplegrid.php')) {
						get_template_part('partials/custom/block_peoplegrid/block_peoplegrid', null, $block);
					} else {
						get_template_part('partials/core/mild/block_peoplegrid/block_peoplegrid', null, $block);
					}
				elseif( $layout == 'block_posts' ):
					$useexcerpts = false;
					// echo('<pre>'); echo('on block_posts!'); echo('</pre>');
					if (array_key_exists('page', $GLOBALS['posttypes'])) {
						// echo('<pre>'); echo('new posttypes method!'); echo('</pre>');
						foreach ($GLOBALS['posttypes'] as $t => $type) {
							if (in_array('excerpts', $type)) { // backwards compatability
								$useexcerpts = true;
							} elseif (in_array('post_title', $type)) {
								$useexcerpts = true;
							} else {
								foreach ($type as $term) {
									if (gettype($term) == 'array') {
										if (in_array('image', $term) || in_array('title', $term) || in_array('excerpt', $term)) {
											$useexcerpts = true;
										}
									}
								}
							}
						}
					} else { // backwards compatability
						if (in_array('post', $GLOBALS['posttypes']) || in_array('article', $GLOBALS['posttypes'])) {
							$useexcerpts = true;
						}
					}
					if ($useexcerpts) {
						if (file_exists($themedir . '/partials/custom/block_posts/block_posts.php')) {
							get_template_part('partials/custom/block_posts/block_posts', null, $block);
						} else {
							get_template_part('partials/core/mild/block_posts/block_posts', null, $block);
						}
					} else {
						// nothing
					}
				elseif( $layout == 'block_pullquote' ):
					if (file_exists($themedir . '/partials/custom/block_pullquote/block_pullquote.php')) {
						get_template_part('partials/custom/block_pullquote/block_pullquote', null, $block);
					} else {
						get_template_part('partials/core/mild/block_pullquote/block_pullquote', null, $block);
					}
				elseif( $layout == 'block_rule' ):
					if (file_exists($themedir . '/partials/custom/block_rule/block_rule.php')) {
						get_template_part('partials/custom/block_rule/block_rule', null, $block);
					} else {
						get_template_part('partials/core/mild/block_rule/block_rule', null, $block);
					}
				elseif( $layout == 'block_share' ):
					if (file_exists($themedir . '/partials/custom/block_share/block_share.php')) {
						get_template_part('partials/custom/block_share/block_share', null, $block);
					} else {
						get_template_part('partials/core/mild/block_share/block_share', null, $block);
					}
				elseif( $layout == 'block_strip' ):
					if (file_exists($themedir . '/partials/custom/block_strip/block_strip.php')) {
						get_template_part('partials/custom/block_strip/block_strip', null, $block);
					} else {
						get_template_part('partials/core/mild/block_strip/block_strip', null, $block);
					}
				elseif( $layout == 'block_testimonials' ):
					if (file_exists($themedir . '/partials/custom/block_testimonials/block_testimonials.php')) {
						get_template_part('partials/custom/block_testimonials/block_testimonials', null, $block);
					} else {
						get_template_part('partials/core/mild/block_testimonials/block_testimonials', null, $block);
					}
				elseif( $layout == 'block_text' ):
					if (file_exists($themedir . '/partials/custom/block_text/block_text.php')) {
						get_template_part('partials/custom/block_text/block_text', null, $block);
					} else {
						get_template_part('partials/core/mild/block_text/block_text', null, $block);
					}
				elseif( $layout == 'block_textimage' ):
					if (file_exists($themedir . '/partials/custom/block_textimage/block_textimage.php')) {
						get_template_part('partials/custom/block_textimage/block_textimage', null, $block);
					} else {
						get_template_part('partials/core/mild/block_textimage/block_textimage', null, $block);
					}
				elseif( $layout == 'block_tiles' ):
					if (file_exists($themedir . '/partials/custom/block_tiles/block_tiles.php')) {
						get_template_part('partials/custom/block_tiles/block_tiles', null, $block);
					} else {
						get_template_part('partials/core/mild/block_tiles/block_tiles', null, $block);
					}
				endif;


				// ======================================= //
				//              MEDIUM BLOCKS              //
				// ======================================= //

				if( $layout == 'block_audioplayer' ):
					if (file_exists($themedir . '/partials/custom/block_audioplayer/block_audioplayer.php')) {
						get_template_part('partials/custom/block_audioplayer/block_audioplayer', null, $block);
					} else {
						get_template_part('partials/core/medium/block_audioplayer/block_audioplayer', null, $block);
					}
				elseif( $layout == 'block_gallery' ):
					if (file_exists($themedir . '/partials/custom/block_gallery/block_gallery.php')) {
						get_template_part('partials/custom/block_gallery/block_gallery', null, $block);
					} else {
						get_template_part('partials/core/medium/block_gallery/block_gallery', null, $block);
					}
				elseif( $layout == 'block_layerslider' ):
					if (file_exists($themedir . '/partials/custom/block_layerslider/block_layerslider.php')) {
						get_template_part('partials/custom/block_layerslider/block_layerslider', null, $block);
					} else {
						get_template_part('partials/core/medium/block_layerslider/block_layerslider', null, $block);
					}
				elseif( $layout == 'block_logogrid' ):
					if (file_exists($themedir . '/partials/custom/block_logogrid/block_logogrid.php')) {
						get_template_part('partials/custom/block_logogrid/block_logogrid', null, $block);
					} else {
						get_template_part('partials/core/medium/block_logogrid/block_logogrid', null, $block);
					}
				elseif( $layout == 'block_photostrip' ):
					if (file_exists($themedir . '/partials/custom/block_photostrip/block_photostrip.php')) {
						get_template_part('partials/custom/block_photostrip/block_photostrip', null, $block);
					} else {
						get_template_part('partials/core/medium/block_photostrip/block_photostrip', null, $block);
					}
				elseif( $layout == 'block_resources' ):
					if (file_exists($themedir . '/partials/custom/block_resources/block_resources.php')) {
						get_template_part('partials/custom/block_resources/block_resources', null, $block);
					} else {
						get_template_part('partials/core/medium/block_resources/block_resources', null, $block);
					}
				elseif( $layout == 'block_section' ):
					if (file_exists($themedir . '/partials/custom/block_section/block_section.php')) {
						get_template_part('partials/custom/block_section/block_section', null, $block);
					} else {
						get_template_part('partials/core/medium/block_section/block_section', null, $block);
					}
				elseif( $layout == 'block_ticker' ):
					if (file_exists($themedir . '/partials/custom/block_ticker/block_ticker.php')) {
						get_template_part('partials/custom/block_ticker/block_ticker', null, $block);
					} else {
						get_template_part('partials/core/medium/block_ticker/block_ticker', null, $block);
					}
				endif;


				// ======================================= //
				//               SPICY BLOCKS              //
				// ======================================= // 

				if( $layout == 'block_related' ):
					if (file_exists($themedir . '/partials/custom/block_related/block_related.php')) {
						get_template_part('partials/custom/block_related/block_related', null, $block);
					} else {
						get_template_part('partials/core/spicy/block_related/block_related', null, $block);
					}
				elseif( $layout == 'block_map' ):
					if (file_exists($themedir . '/partials/custom/block_map/block_map.php')) {
						get_template_part('partials/custom/block_map/block_map', null, $block);
					} else {
						get_template_part('partials/core/spicy/block_map/block_map', null, $block);
					}
				endif;


				// ======================================= //
				//              CUSTOM BLOCKS              //
				// ======================================= // 

				require('custom/_custom_blocks.php');
				
			}


		endwhile;
	endif;
}

?>