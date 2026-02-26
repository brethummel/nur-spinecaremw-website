<?php
require_once "scss.inc.php";

$directory = "stylesheets";

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;
use ScssPhp\Server\Server;

$server = new Server($directory);
$server->serve();
?>