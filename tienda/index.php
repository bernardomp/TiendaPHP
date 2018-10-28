<?php 
	include "codigo.php";

	$ip_monitor = "192.168.1.224";
	$port_monitor = 8081;

	$postData = file_get_contents('php://input');
	
	//Procesamiento del XML
	$xml = new DOMDocument();
	
	if(!$xml->loadXML($postData)) {
		die("Recepción incorrecta");
	}
	
	$tipo_req = $xml->getElementsByTagName('tipo')->item(0)->nodeValue;

	if($tipo_req == "evento") {
		$tipo_req = $xml->getElementsByTagName('contenido')->item(0)->nodeValue;
	}

	$con = new mysqli("localhost", "root", "toor", "multiagentes");

	switch($tipo_req) {

		case "inicializacion":
			iniciarTiendaStock($con,$xml);
			break;

		case "Inicio de simulacion":
			iniciarSimulacion($con,$xml);
			break;

		case "Fin de simulacion":
			cerrarSimulacion($con);
			break;
		
		case "conexion":
			entrarTienda($con,$xml);
			break;
		
		case "salir":
			salirTienda($con,$xml);
			break;

	}
	
?>
	