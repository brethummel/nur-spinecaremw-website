<?php
/*
Partial Name: block_accordion
*/
?>

<!-- BEGIN ACCORDION -->
<?php $block = $args; 
//	echo('<pre style="padding-left: 100px;">');
//	print_r($block);
//	echo('</pre>');
    $settings = $block['accordion_settings'];
    $classes = ''; 
    if (isset($settings['style'])) {
        $style = $settings['style'];
        $classes .= ' ' . $style;
    }
    if ($block['accordion_display']['custom_class'] !== null) { 
        $class = $block['accordion_display']['custom_class'];
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
	$behavior = $settings['behavior'];
	if (in_array('open', $behavior)) {
		$initopen = true;
    	$classes .= ' init-open';
	}
	if (in_array('multiple', $behavior)) {
		$multiple = true;
    	$classes .= ' multiple';
	}
    $usesubs = $settings['subcategories'];
    if ($usesubs) {
        $classes .= ' subcategories';
    }
    $categories = $block['accordion_categories'];
	$displayblock = true;
	if (isset($block['accordion_display']['display_block']) && $block['accordion_display']['display_block'] != true) {
		$displayblock = false;
	}
?>
<?php if ($displayblock) { ?>
<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_accordion.js"></script>
<div class="block-accordion block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row accordion-container">
            <?php if ($style == 'cols-1') {
                $categoryclasses = "col-12";
            } else if ($style == 'cols-2') {
                $categoryclasses = "col-12 col-lg-6";
            } ?>
            <?php foreach ($categories as $c => $category) { ?>
                <div class="category <?php echo($categoryclasses); ?><?php if ($c == 0 && isset($initopen) && $initopen) { ?> init open<?php } ?>" data-cat="<?php echo($c); ?>">
                    <div class="tab">
                        <h4><?php echo($category['category']); ?></h4>
                    </div>
                </div>
                <?php if ($style == 'cols-2') {  
                    if ($c % 2 == 0 && $c != count($categories) - 1) { // first col category on desktop
                        $detailclasses = " hidden-desktop";
                        $storeddetail = $category['content'];
                        $storedsubcategories = $category['subcategories'];
                        $storedid = $c;
                    } else {
                        $detailclasses = "";
                    }
                } else {
                    $detailclasses = ""; 
                } ?>
                <?php if ((isset($storeddetail) || isset($storedsubcategories)) && $c % 2 != 0) { ?>
                    <div class="col-12 category-detail hidden-mobile" data-cat="<?php echo($storedid); ?>">
                        <?php if ($usesubs) { ?>
                            <div class="subcategories-container">
                                <?php foreach ($storedsubcategories as $s => $subcategory) { ?>
                                    <div class="subcategory" data-cat="<?php echo($c . '-' . $s); ?>">
                                        <div class="tab">
                                            <h4 class="subcat"><?php echo($subcategory['subcategory']); ?></h4>
                                        </div>
                                        <div class="subcategory-detail">
                                            <div class="blocks-wrapper">
												<?php $detail = $subcategory['content']; ?>
												<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), 'partials')); ?>
                                                <?php get_template_part($mypath . '/block_accordion_blocks', null, $detail); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if (!$usesubs) { ?>
                            <div class="blocks-wrapper">
								<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), 'partials')); ?>
								<?php get_template_part($mypath . '/block_accordion_blocks', null, $storeddetail); ?>
                            </div>
                        <?php } else { ?>

                        <?php } ?>
                    </div>
                <?php unset($storeddetail); unset($storedsubcategories); unset($storedid); } ?>
                <div class="category-detail col-12<?php echo($detailclasses); ?><?php if ($c == 0 && isset($initopen) && $initopen) { ?> init open<?php } ?>" data-cat="<?php echo($c); ?>">
                    <?php if ($usesubs) { ?>
                        <div class="subcategories-container">
                            <?php $subcategories = $category['subcategories']; ?>
                            <?php foreach ($subcategories as $s => $subcategory) { ?>
                                <div class="subcategory" data-cat="<?php echo($c . '-' . $s); ?>">
                                    <div class="tab">
                                        <h4 class="subcat"><?php echo($subcategory['subcategory']); ?></h4>
                                    </div>
                                    <div class="subcategory-detail">
                                        <div class="blocks-wrapper">
											<?php $detail = $subcategory['content']; ?>
											<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), 'partials')); ?>
											<?php get_template_part($mypath . '/block_accordion_blocks', null, $detail); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if (!$usesubs) { ?>
                        <div class="blocks-wrapper">
							<?php $detail = $category['content']; ?>
							<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), 'partials')); ?>
							<?php get_template_part($mypath . '/block_accordion_blocks', null, $detail); ?>
                        </div>
                    <?php } else { ?>

                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>
<!-- END ACCORDION --> 
