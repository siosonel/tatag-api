<?php 
$parserDir = "../../addtd";
include_once "$parserDir/ParsedownSlate.php";
$content = file_get_contents('docs.md');

$Parsedown = new ParsedownSlate();
$content = $Parsedown->text($content);
$languages = array('javascript');

include_once "../../addtd/docs.php";
