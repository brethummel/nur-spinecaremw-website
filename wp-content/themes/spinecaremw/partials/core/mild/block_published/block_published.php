<?php
/*
Partial Name: block_published
*/
?>

<!-- BEGIN RULE -->
<?php $block = $args; 
//	echo('<pre>');
//	print_r($block);
//	echo('</pre>');
    $settings = $block['published_settings'];
    $classes = '';
    if (isset($settings['background'])) { 
        $background = explode("|", $settings['background']);
        foreach ($background as $value) {
            $classes .= ' ' . $value;
        }
    }
?>
<div class="block-published block padded<?php echo($classes); ?>">
    <div class="container">
        <div class="row">
            <div class="col-12 content-container">
                <p><?php echo(get_the_date()); ?></p>
            </div>
        </div>
    </div>
</div>
<!-- END RULE --> 
