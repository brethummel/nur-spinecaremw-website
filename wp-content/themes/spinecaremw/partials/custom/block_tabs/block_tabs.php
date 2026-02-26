<?php
/*
Partial Name: block_tabs
*/
?>

<!-- BEGIN tabs -->
<?php $block = $args; 
//	echo('<pre style="padding-left: 100px;">');
//	print_r($block);
//	echo('</pre>');
    $settings = $block['tabs_settings'];
    $classes = ''; 
    if ($block['tabs_display']['custom_class'] !== null) { 
        $class = $block['tabs_display']['custom_class'];
		if (strlen($class) > 0) {
			$classes .= ' ' . $class;
		}
	}
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
    $tabs = $block['tabs_tabs'];
    if (count($tabs) > 3) {
        $breakpoint = 'lg';
    } else {
        $breakpoint = 'md';
    }
    $tabclasses = ' col-12 col-' . $breakpoint . '-' . (12 / count($tabs));
	$displayblock = true;
	if (isset($block['tabs_display']['display_block']) && $block['tabs_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_tabs.js"></script>
<div class="block-tabs block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row tabs-container">
            <?php foreach ($tabs as $t => $tab) { ?>
                <div class="tab<?php echo($tabclasses); ?><?php if ($t == 0) { ?> open<?php } ?>" data-tab="<?php echo($t); ?>">
                    <div class="tab-text">
                        <h4><?php echo($tab['tab']); ?></h4>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="row details-container">
            <?php foreach ($tabs as $t => $tab) { ?>
                <div class="tab-detail col-12<?php if ($t == 0) { ?> open<?php } ?>" data-tab="<?php echo($t); ?>">
                    <div class="blocks-wrapper">
                        <?php $detail = $tab['content']; ?>
                        <?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), 'partials')); ?>
                        <?php get_template_part($mypath . '/block_tabs_blocks', null, $detail); ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>
<!-- END tabs --> 
