<?php
/*
Partial Name: block_bio
*/
?>

<!-- BEGIN BIO -->
<?php $block = $args; 
    // echo('<pre>'); print_r($block); echo('</pre>');
    $settings = $block['bio_settings'];

    if ($settings['data_source'] == 'post' && isset($block['bio_datasource']) && strlen($block['bio_datasource'])) {
        $sourceid = url_to_postid($block['bio_datasource']);
        $contentblocks = get_field('content_blocks', $sourceid);
        // echo('<pre>'); print_r($contentblocks); echo('</pre>');
        foreach ($contentblocks as $blk) {
            if ($blk['acf_fc_layout'] == 'block_bio') {
                $block = $blk;
                $settings = $block['bio_settings'];
            }
        }
    }

    $classes = ''; 
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $i => $value) {
            $classes .= ' ' . $value;
            if ($i == 0) {
                $bkgndcolor = $value;
            }
        }
    }
    if ($block['bio_display']['custom_class'] !== null) { 
        $class = $block['bio_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
    $content = $block['bio_content'];
    if (strlen($content) == 0) {
        $classes .= ' no-bio';
    }
    if (isset($settings['read_more'])) { 
        $readmore = $settings['read_more'];
        if ($readmore && (strpos($content, '[read-more]') > 0)) {
            $overflow = substr($content, strpos($content, '<p>[read-more]') + 18, strlen($content));
            $content = substr($content, 0, strpos($content, '<p>[read-more]'));
        }
    } else {
        $readmore = false;
    }
    if (isset($block['bio_person']['title'])) { // backwards compatability
        $image = $block['bio_image'];
        $name = $block['bio_person']['name'];
        $title = $block['bio_person']['title'];
    } else {
        $image = $block['bio_info']['images']['image'];
        $name = $block['bio_info']['info']['full_name'];
        $title = $block['bio_info']['info']['title'];
        $social = $block['bio_social'];
    }
	$displayblock = true;
	if (isset($block['bio_display']['display_block']) && $block['bio_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<?php if ($readmore) { ?>
	<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
    <script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_bio.js"></script>
<?php } ?>
<div class="block-bio block padded<?php echo($classes); ?>">
    <div class="container">
		<?php $peopleposts = $GLOBALS['peopleposts']; ?>
		<?php if ($peopleposts['use'] == true && get_post_type() == $peopleposts['slug']) { ?>
			<div class="row">
				<div class="col-12 back-link">
					<a href="javascript:history.back()">&#171; back to previous page</a>
				</div>
			</div>
		<?php } ?>
        <div class="row">
            <div class="portrait-col col-12 col-md-4"><img class="img-fluid" src="<?php echo($image['url']); ?>" alt="<?php echo($name); ?>"/></div>
            <div class="bio-col col-12 col-md-8">
                <div class="person-container">
                    <h3><?php echo($name); ?></h3>
                    <p class="title"><?php echo($title); ?></p>
                </div>
                <?php if (isset($settings['social']['include']) && $settings['social']['include'] == 'true') { ?>
                    <div class="social-container">
                        <ul>
                            <?php $which = $settings['social']['which']; ?>
                            <?php if (in_array('linkedin', $which) && $social['linkedin']) { ?>
                                <li class="linkedin">
                                    <a href="<?php echo($social['linkedin']); ?>" target="_blank">
                                        <svg width="20" height="20" viewBox="0 0 22 22"><path d="M232.1,118.28h4.56V133H232.1Zm2.28-7.3a2.65,2.65,0,1,1-2.65,2.65,2.65,2.65,0,0,1,2.65-2.65" transform="translate(-231.73 -110.98)"/><path d="M239.52,118.28h4.37v2H244a4.81,4.81,0,0,1,4.32-2.37c4.61,0,5.46,3,5.46,7V133h-4.55v-7.15c0-1.7,0-3.89-2.37-3.89s-2.74,1.85-2.74,3.77V133h-4.55Z" transform="translate(-231.73 -110.98)"/></svg>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (in_array('twitter', $which) && $social['twitter']) { ?>
                                <li class="twitter">
                                    <a href="<?php echo($social['twitter']); ?>" target="_blank">
                                        <svg width="22" height="18" viewBox="0 0 22 18"><path d="M382.27,121a9,9,0,0,1-2.6.72,4.51,4.51,0,0,0,2-2.52,9.16,9.16,0,0,1-2.87,1.11,4.45,4.45,0,0,0-3.29-1.44,4.53,4.53,0,0,0-4.52,4.55,4.4,4.4,0,0,0,.12,1,12.77,12.77,0,0,1-9.3-4.75,4.58,4.58,0,0,0,1.39,6.07,4.42,4.42,0,0,1-2-.57v.06a4.55,4.55,0,0,0,3.62,4.46,4.68,4.68,0,0,1-1.19.15,4,4,0,0,1-.85-.08,4.53,4.53,0,0,0,4.22,3.16,9,9,0,0,1-5.61,1.94,9.46,9.46,0,0,1-1.07-.06A12.82,12.82,0,0,0,380,123.9c0-.19,0-.39,0-.58A9.39,9.39,0,0,0,382.27,121Z" transform="translate(-360.27 -118.83)"/></svg>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (in_array('instagram', $which) && $social['instagram']) { ?>
                                <li class="instagram">
                                    <a href="<?php echo($social['instagram']); ?>" target="_blank">
                                        <svg width="20px" height="20px" viewBox="0 0 20 20"><g><path d="M10,4.8c-2.8,0-5.2,2.3-5.2,5.2s2.3,5.2,5.2,5.2s5.2-2.3,5.2-5.2S12.8,4.8,10,4.8z M10,13.3c-1.8,0-3.3-1.5-3.3-3.3 S8.2,6.7,10,6.7s3.3,1.5,3.3,3.3S11.8,13.3,10,13.3z"/><circle cx="15.4" cy="4.7" r="1.2"/><path d="M18.4,1.7c-1-1.1-2.5-1.7-4.2-1.7H5.8C2.3,0,0,2.3,0,5.8v8.3c0,1.7,0.6,3.2,1.7,4.3c1.1,1,2.5,1.6,4.2,1.6h8.2 c1.7,0,3.2-0.6,4.2-1.6c1.1-1,1.7-2.5,1.7-4.3V5.8C20,4.2,19.4,2.7,18.4,1.7z M18.2,14.2c0,1.3-0.4,2.3-1.2,2.9 c-0.7,0.7-1.7,1-2.9,1H5.9c-1.2,0-2.2-0.4-2.9-1c-0.7-0.7-1.1-1.7-1.1-3V5.8c0-1.2,0.4-2.2,1.1-2.9c0.7-0.7,1.7-1,2.9-1h8.3 c1.2,0,2.2,0.4,2.9,1.1c0.7,0.7,1.1,1.7,1.1,2.9V14.2L18.2,14.2z"/></g></svg>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (in_array('facebook', $which) && $social['facebook']) { ?>
                                <li class="facebook">
                                    <a href="<?php echo($social['facebook']); ?>" target="_blank">
                                        <svg width="20" height="20" viewBox="0 0 22 22"><path d="M19.5,0H2.5C1.1,0,0,1.1,0,2.5v16.9C0,20.9,1.1,22,2.5,22h9.3v-8.5H8.9v-3.3h2.9V7.7c0-2.9,1.7-4.4,4.3-4.4 c0.9,0,1.7,0,2.6,0.1v3h-1.8c-1.4,0-1.7,0.7-1.7,1.6v2.1h3.3L18,13.5h-2.9V22h4.3c1.4,0,2.5-1.1,2.5-2.5V2.5C22,1.1,20.9,0,19.5,0z"/></svg>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (in_array('email', $which) && $social['email']) { ?>
                                <li class="email">
                                    <a href="mailto:<?php echo($social['email']); ?>" target="_blank">
                                        <svg width="22" height="18" viewBox="0 0 22 20"><g><path d="M11,9.5L21.7,2c-0.3-0.5-0.9-0.8-1.5-0.8H1.8C1.2,1.2,0.6,1.5,0.3,2L11,9.5z"/><polygon points="14.6,8.5 22,14.4 22,3.3    "/><g><polygon points="7.4,8.5 0.1,3.3 0.1,14.4"/><path d="M13.5,9.3l-2.2,1.5c-0.1,0.1-0.2,0.1-0.4,0.1s-0.3,0-0.4-0.1L8.5,9.3l-8.3,6.6C0.4,16.5,1.1,17,1.8,17h18.4 c0.7,0,1.4-0.4,1.6-1.1L13.5,9.3z"/></g></g></svg>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (in_array('mobile', $which) && $social['mobile']) { ?>
                                <li class="mobile">
                                    <a href="tel:<?php echo($social['mobile']); ?>" target="_blank">
                                        <svg width="22" height="18" viewBox="0 0 22 20"><g><path d="M11,9.5L21.7,2c-0.3-0.5-0.9-0.8-1.5-0.8H1.8C1.2,1.2,0.6,1.5,0.3,2L11,9.5z"/><polygon points="14.6,8.5 22,14.4 22,3.3    "/><g><polygon points="7.4,8.5 0.1,3.3 0.1,14.4"/><path d="M13.5,9.3l-2.2,1.5c-0.1,0.1-0.2,0.1-0.4,0.1s-0.3,0-0.4-0.1L8.5,9.3l-8.3,6.6C0.4,16.5,1.1,17,1.8,17h18.4 c0.7,0,1.4-0.4,1.6-1.1L13.5,9.3z"/></g></g></svg>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
                <?php if ($readmore) { ?>
                    <?php if (isset($overflow)) { ?>
                        <div class="bio-container <?php echo($bkgndcolor); ?>">
                            <?php echo($content); ?>
                            <div class="read-more"><?php echo($overflow); ?></div>
                            <p class="read-more"><a href="#">read more</a></p>
                        </div>
                    <?php } else { ?>
                        <div class="bio-container read-more <?php echo($bkgndcolor); ?>">
                            <?php echo($content); ?>
                        </div>
                        <p class="read-more"><a href="#">read more</a></p>
                    <?php } ?>
                <?php } else { ?>
                    <div class="bio-container">
                        <?php echo($content); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<!-- END BIO --> 
