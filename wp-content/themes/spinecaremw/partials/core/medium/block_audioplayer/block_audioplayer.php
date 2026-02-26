<?php
/*
Partial Name: block_audioplayer
*/
?>

<!-- BEGIN AUDIO PLAYER -->
<?php $block = $args; 
    $settings = $block['audioplayer_settings'];
    $classes = '';
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
		if ($background[1] == 'dark') {
			$dark = true;
		} else {
			$dark = false;
		}
    }
	if ($dark) {
		$base = 'rgba(255, 255, 255, .2)';
		$progress = '#ffffff';
	} else {
		$base = 'rgba(0, 0, 0, .2)';
		$progress = '#ffffff';
	}
    if (get_sub_field('custom_class') !== null) { 
        $class = get_sub_field('custom_class');
		if (strlen($class) > 0) {
        	$classes .= ' ' . $class;
		}
    }
	$audio = $block['audioplayer_audio'];
	if ($audio['source'] == 'media') {
		$audiofile = $audio['file']['url'];
	} elseif ($audio['source'] == 'url') {
		$audiofile = $audio['url'];
	}
	$info = $block['audioplayer_info'];
?>
<?php 
	$themeurl = get_template_directory_uri();
	wp_enqueue_style('fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');
    wp_enqueue_style('typicons', $themeurl . '/partials/core/medium/block_audioplayer/waveform-player/css/jquery.mCustomScrollbar.css');
    // wp_enqueue_style('default2', $themeurl . '/partials/core/medium/block_audioplayer/waveform-player/css/awp_default2.css');
?>
<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_audioplayer.js"></script>
<div class="block-audioplayer block padded<?php if (!$dark) { echo(' ' . $settings['progress']); } ?><?php echo($classes); ?>" data-path="<?php bloginfo('url'); ?><?php echo($mypath); ?>">
    <div class="container">
		<div class="base-color"></div>
		<div class="progress-color"></div>
		<div class="cursor-color"></div>
		<div id="awp-wrapper">
			<div class="awp-player-wrap">
				<!--<div class="awp-player-thumb-wrapper">
					<div class="awp-player-thumb"></div>
				</div>-->
				<div class="awp-player-holder">
					<div class="row align-items-end">
						<div class="col-12 d-md-none mobile-info">
							<div class="awp-info">
								<div class="awp-player-artist"></div>
								<div class="awp-player-title"></div>
							</div>
						</div>
						<div class="col-6 col-md-9 controls">
							<div class="awp-playback-toggle">
								<div class="awp-btn awp-btn-play">
									<i class="fa fa-play"></i>
								</div>
								<div class="awp-btn awp-btn-pause">
									<i class="fa fa-pause"></i>
								</div>
							</div>
							<div class="awp-info d-none d-md-inline-block">
								<div class="awp-player-artist"></div>
								<div class="awp-player-title"></div>
							</div>
						</div>
						<div class="col-6 col-md-3 volume">
							<div class="awp-volume-wrapper">
								<div class="awp-player-volume awp-contr-btn awp-volume-toggable">
									<div class="awp-btn awp-btn-volume-up">
										<i class="fa fa-volume-up"></i>
									</div>
									<div class="awp-btn awp-btn-volume-down">
										<i class="fa fa-volume-down"></i>
									</div>
									<div class="awp-btn awp-btn-volume-off">
										<i class="fa fa-volume-off"></i>
									</div>
								</div>
								<div class="awp-volume-seekbar">
									 <div class="awp-volume-bg"><div class="awp-volume-level"></div></div>
								</div>
							</div>
						</div>
						<div class="col-12 waveform">
							<div class="awp-waveform-wrap">
								<div class="awp-waveform awp-hidden"></div>  
								<div class="awp-waveform-img awp-hidden"><!-- image waveform backup -->
									<div class="awp-waveform-img-load"></div>
									<div class="awp-waveform-img-progress-wrap"><div class="awp-waveform-img-progress"></div></div>
								</div>
								<span class="awp-waveform-preloader"></span>
							</div>  
							<div class="awp-media-time-total awp-hidden">0:00</div>
							<div class="awp-media-time-current awp-hidden">0:00</div>
						</div>
					</div>
				</div>
			</div> 
		</div>

		<!-- PLAYLIST LIST -->
		<div id="awp-playlist-list">
			 <div id="playlist-audio">
				<div class="awp-playlist-item" data-type="audio" data-mp3="<?php echo($audiofile); ?>" data-artist="<?php echo($info['artist']); ?>" data-title="<?php echo($info['title']); ?>" data-thumb="<?php echo($themeurl); ?>/partials/core/medium/block_audioplayer/waveform-player/media/audio/3/06.jpg" data-description=""></div>
			</div>
		</div>
    </div>
</div>
<!-- END AUDIO PLAYER --> 
