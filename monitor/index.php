<?php
	
$post = file_get_contents('php://input');

$dom = new DOMDocument();
$dom->loadXML($post);
echo $dom->saveXML();
?>