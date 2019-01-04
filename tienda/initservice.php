<?php 
	header("Access-Control-Allow-Origin: *");
	include "AgenteTienda.php";
	
	/**
	 * Este fichero permite a PHP enviar de forma activa informacion al monitor.
	 * Principalmente, esto nos sirve para solicitar las tiendas al monitor
	 **/


	// Direccion IP y puerto del monitor
	$ip_monitor = "10.0.69.39/init";
	$port_monitor = 3000;

	// Direccion IP y puerto de la tienda
	$ip_tienda = "10.0.69.78";
	$port_tienda = 80;

	// Inicialización del agente tienda
	$tienda = new AgenteTienda($ip_monitor,$port_monitor,$ip_tienda,$port_tienda);

	//Establecemos conexion con la bbdd
	$tienda->conexionBBDD("localhost", "root", "toor", "multiagentes");

	//Enviamos peticion al monitor para solicitar identificador
	$tienda->consoleLog("Solicitando ".$_POST["number"]." tienda/s ...");

	//Iniciamos la solicitud de las tiendas
	$tienda->solicitarTiendas($_POST["number"]);

	
	
?>