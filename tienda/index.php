<?php 
	header("Access-Control-Allow-Origin: *");
	include "AgenteTienda.php";

	//Evitar mostrar algunas advertencias
	error_reporting(E_ERROR | E_PARSE); 

	$ip_monitor = "10.0.69.39";
	$port_monitor = 3000;

	$ip_tienda = " 10.0.69.78";
	$port_tienda = 80;

	//Recepcion de los datos 
	$postData = file_get_contents('php://input');

	//Creacion de agente tienda
	$tienda = new AgenteTienda($ip_monitor,$port_monitor,$ip_tienda,$port_tienda);
	
	//Conexion a la bbdd
	$tienda->conexionBBDD("localhost", "root", "toor", "multiagentes");
	
	//Procesamiento del XML recibido
	$tienda->setXML($postData);

	$tienda->consoleLog("Acceso al sistema:".$postData);
	
	//Obtenemos el tipo de peticion buscando en el fichero xml
	$tipo_req = $tienda->getXML()->getElementsByTagName('tipo')->item(0)->nodeValue;

	if($tipo_req == "evento") {
		$tipo_req = $tienda->getXML()->getElementsByTagName('contenido')->item(0)->nodeValue;
	}

	//Ejecutamos una accion de la tienda
	switch($tipo_req) {

		//Iniciamos el stock de las tiendas recibidas del monitor
		case "inicializacion":
			$dat = $tienda->iniciarTiendaStock();
			$tienda->consoleLog("Enviado ACK inicializacion".$dat);
			echo $dat;
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
			echo $tienda->entrarTienda();
			break;
		
		case "salir":
			$tienda->salirTienda();
			break;
		
		default:
			$tienda->consoleLog("Opcion:".$tipo_req. " no disponible.");

	}
	
?>
	