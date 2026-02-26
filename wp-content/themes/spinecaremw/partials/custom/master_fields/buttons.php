<?php
/*
Partial Name: buttons
*/
?>

<!-- BEGIN BUTTONS -->
<?php $buttons = $args; ?>
<?php if (is_string(array_key_first($buttons)) && (array_key_first($buttons[array_key_first($buttons)]) == 'settings') || array_key_first($buttons) == 'button') { // legacy
    $buttons = array($buttons); // this corrects for single buttons not in a repeater
} ?>
<?php foreach ($buttons as $button) { ?>
    <?php 
        $type = $button['button']['settings']['type'];
        $color = $button['button']['settings']['color'];
        $text = $button['button']['destination']['text'];
        if ($type == 'page') {
            $url = $button['button']['destination']['page'];
        } else if ($type == 'url') {
            $url = $button['button']['destination']['url'];
        } else if ($type == 'anchor') {
            $url = $button['button']['destination']['anchor'];
        } else if ($type == 'email') {
            $url = 'mailto:' . $button['button']['destination']['email'];
        }
        if ($button['button']['destination']['new_tab']) {
            $target = ' target="_blank"';
        }
    ?>
    <a class="button medium <?php echo($color); ?>" href="<?php echo($url); ?>"<?php if (isset($target) && ($type == 'page' || $type == 'url')) { echo($target); } ?>><span class="button-text"><?php echo($text); ?></span></a>
<?php } ?>
<!-- END BUTTONS --> 
