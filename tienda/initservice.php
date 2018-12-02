<?php 
	header("Access-Control-Allow-Origin: *");
	include "AgenteTienda.php";
	
	$ip_monitor = "10.0.69.39/init";
	$port_monitor = 3000;

	$ip_tienda = "10.0.69.78";
	$port_tienda = 80;


	$tienda = new AgenteTienda($ip_monitor,$port_monitor,$ip_tienda,$port_tienda);
	
	$tienda->conexionBBDD("localhost", "root", "toor", "multiagentes");
	//Enviamos peticion al monitor para solicitar identificador

	$tienda->consoleLog("Solicitando ".$_POST["number"]." tienda/s ...");
	$tienda->solicitarTiendas($_POST["number"]);

	
	
?>