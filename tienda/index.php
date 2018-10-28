<?php 
	include "funciones.php";

	
	$postData = file_get_contents('php://input');
	
	$xml = new DOMDocument();
	
	if(!$xml->loadXML($postData)) {
		die("Recepción incorrecta");
	}
	
	
	
	echo $xml->saveXML();
	
?>
	