<?php 
	include "AgenteTienda.php";
	
	$ip_monitor = "192.168.1.224/init";
	$port_monitor = 8081;

	$ip_tienda = "192.168.1.224";
	$port_tienda = 80;

	$tienda = new AgenteTienda($ip_monitor,$port_monitor,$ip_tienda,$port_tienda);
	//Enviamos peticion al monitor para solicitar identificador
	$tienda->solicitarTiendas();

	
	
?>