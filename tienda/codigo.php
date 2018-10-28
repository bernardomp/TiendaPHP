<?php

function crearTienda(){
	//mandamos peticion al monitor con nuestra ip y puerto para que cree la tienda
	$ntiendas = rand(5,10); //Generamos un numero aleatorio de tiendas
	
	for($i=0;$i<$ntiendas;$i++){
		//mandamos peticion al monitor para cada una de las tiendas pasandole la ip y puerto
	}
}

function iniciarSimulacion(mysql_connect $conexion){
	//cuando el monitor nos mande la peticion ponemos en marcha las tiendas
	if(!$conexion){
		die('Error al conectarse a la base de datos') . mysql_error();
	}
	//creamos la base de datos
	$sql='CREATE DATABASE bd_compras';

	if (mysql_query($sql, $conexion)) {
		echo "La base de datos se creó correctamente\n";
	} 
	else {
		echo 'Error al crear la base de datos: ' . mysql_error() . "\n";
	}
	
	$creacion="CREATE TABLE TIENDAS.." //Falta añadir los campos
	
}

function convertirXML(){
	//convertir y validad un fichero xml
}

function entrarTienda($conexion){
	//añade al cliente en la tienda en la que se encuentra
	$actualizar='update clientes..';
	mysql_query($actualizar,$conexion);
}

function extraerProductos(){
	//Devuelve el listado de productos de una tienda
	$productos='select nombre from productos';
	mysql_query($productos,$conexion);
}

function comprarProducto($conexion,int producto){
	//si la tienda tiene el producto y hay unidades suficientes para vender al cliente
	//vendemos el producto y modificamos el stock de la tienda
	$actualizar='update tiendas..';
	mysql_query($actualizar,$conexion); //actualizamos el stock de ese producto en la tienda 
}

function extraerTiendas($conexion,int cliente){
	//devuelve el listado de tiendas de un cliente que esta en la misma tienda
	$tiendas = 'select tiendas from cliente_tiendas where idcliente='$cliente;
	mysql_query($tiendas,$conexion);
}

function salirTienda(){
	//borra al cliente de la tienda en la que se encontraba
	$actualizar='updte clientes...';
	mysql_query($actualizar,$conexion);
}

function cerrarTienda($conexion){
	//si nos quedamos sin productos cerramos el acceso a la tienda
	
}

function cerrarSimulacion(){
	//el monitor manda una peticion para finalizar el proceso
}


?>