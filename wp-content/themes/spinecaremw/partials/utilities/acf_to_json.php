<?php 

require_once("../../../../../wp-load.php");

$themeurl = get_template_directory_uri();
$themedir = get_template_directory();

$acf_file = '/custom/block_textimage/block_textimage.acf';

// echo($themedir . '/partials' . $acf_file);
$file = file_get_contents($themedir . '/partials' . $acf_file);

echo("<pre>");
// echo($block . '<br/>');

// echo(substr($file, strpos($file, 'acf_add_local_field_group(') + 26, strpos($file, '));' . PHP_EOL . PHP_EOL . 'endif;') - strpos($file, 'acf_add_local_field_group(') - 26 + 1) . '<br/>');
// echo(strpos($file, '));' . PHP_EOL . PHP_EOL . 'endif;'));
$arrstr = substr($file, strpos($file, 'acf_add_local_field_group(') + 26, strpos($file, '));' . PHP_EOL . PHP_EOL . 'endif;') - strpos($file, 'acf_add_local_field_group(') - 26 + 1) . ';';
// echo($arrstr);
$block = eval('return ' . $arrstr);
// print_r($block);
$json = json_encode($block, JSON_PRETTY_PRINT);
echo('[' . $json . ']');
echo("</pre>");




//echo "<pre>";
//echo $json;
//echo "</pre>";

?>