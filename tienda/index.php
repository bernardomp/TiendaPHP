<?php 
	include "AgenteTienda.php";

	$ip_monitor = "192.168.1.224";
	$port_monitor = 8081;

	$ip_tienda = "192.168.1.224";
	$port_tienda = 8081;

	//Recibir datos
	$postData = file_get_contents('php://input');
	
	//Procesamiento del XML
	$xml = new DOMDocument();
	
	if(!$xml->loadXML($postData)) {
		die("Recepción incorrecta");
	}
	
	//Obtenemos el tipo de peticion
	$tipo_req = $xml->getElementsByTagName('tipo')->item(0)->nodeValue;

	if($tipo_req == "evento") {
		$tipo_req = $xml->getElementsByTagName('contenido')->item(0)->nodeValue;
	}

	//Creacion de agente tienda
	$tienda = new AgenteTienda($ip_monitor,$port_monitor,$ip_tienda,$port_tienda);

	//Conexion a la bbdd
	$tienda->conexionBBDD("localhost", "root", "toor", "multiagentes");

	//Guardamos el fichero xml recibido
	$tienda->setXML($xml);

	//Ejecutamos una accion de la tienda
	switch($tipo_req) {

		//Obtenemos id tienda del monitor
		case "Inicio registrado":
			$tienda->obtenerTiendaID();
			break;

		//Iniciamos el stock de las tiendas recibidos del monitor
		case "inicializacion":
			$tienda->iniciarTiendaStock();
			break;

		//El monitor indica el inicio de la simulacion
		case "Inicio de simulacion":
			$tienda->iniciarSimulacion();
			break;

		case "compra":
			$tienda->comprarProducto();
			break;

		case "Fin de simulacion":
			$tienda->finSimulacion();
			break;
		
		case "conexion":
			$tienda->entrarTienda();
			break;
		
		case "salir":
			$tienda->salirTienda();
			break;

	}
	
?>
	