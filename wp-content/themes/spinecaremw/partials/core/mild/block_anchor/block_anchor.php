<?php
/*
Partial Name: block_anchor
*/
?>

<!-- BEGIN ANCHOR -->
<?php $block = $args; 
    $anchor = $block['anchor_id'];
?>
<?php if (!isset($GLOBALS['anchor_exists'])) { ?>
	<?php $mypath = substr(dirname(__FILE__), strpos(dirname(__FILE__), '/wp-content')); ?>
	<script type="text/javascript" src="<?php bloginfo('url'); ?><?php echo($mypath); ?>/block_anchor.js"></script>
	<?php $GLOBALS['anchor_exists'] = true; ?>
<?php } ?>
<a id=<?php echo($anchor . "-hash"); ?>></a>
<!-- END ANCHOR --> 
