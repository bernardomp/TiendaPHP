<?php
	
//$post = file_get_contents('php://input');

$dom = new DOMDocument();
$dom->load("../tienda/xml/ackinicio.xml");
echo $dom->saveXML();
?>