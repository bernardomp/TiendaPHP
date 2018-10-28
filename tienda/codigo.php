<?php

function crearTiendas($ip_src,$ip_dst,$port_dst){
    //mandamos peticion al monitor con nuestra ip y puerto para que cree la tienda
    $puertos = [8082,8083,8084,8085,8086,8087];

	$ntiendas = rand(5,10); //Generamos un numero aleatorio de tiendas
	
	foreach ($puertos as $port_src){
        //mandamos peticion al monitor para cada una de las tiendas pasandole la ip y puerto
        
        initTienda($ip_src,$port_src, $ip_dst,$port_dst);
	}
}

function iniciarTiendaStock($con,$datos){
    //cuando el monitor nos mande la peticion ponemos en marcha las tiendas
    
    //Validamos xml
    if (!$datos->schemaValidate('schema/inicializaciontienda.xsd')) {
        echo 'DOMDocument::schemaValidate() Generated Errors!';
        libxml_display_errors();
    }

	if ($con->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }

    $productos = $datos->getElementsByTagName('producto');
    $tienda = $datos->getElementsByTagName('puerto')->item(1)->nodeValue;

    foreach($productos as $producto) {
        initStock($producto,$tienda,$con);
    }
}


function iniciarSimulacion($con,$datos) {

    if (!$datos->schemaValidate('schema/go.xsd')) {

        echo 'DOMDocument::schemaValidate() Generated Errors!';
        libxml_display_errors();
    }

    $ip_dst = $xml->getElementsByTagName('ip')->item(1)->nodeValue;
    $puerto_dst = $xml->getElementsByTagName('puerto')->item(1)->nodeValue;

    $ip_src = $xml->getElementsByTagName('ip')->item(0)->nodeValue;
    $puerto_src = $xml->getElementsByTagName('puerto')->item(0)->nodeValue;

    $abrir_tienda="UPDATE tienda SET estado=1 WHERE puerto='$puerto_dst'";

    if (!$con->query($abrir_tienda)) {
        printf("Error: %s\n", $con->error);
    }

    ackAgenteIniciado($ip_dst,$port_dst, $ip_src,$port_src);
    
}

function convertirXML(){
	//convertir y validad un fichero xml
}

function entrarTienda($conexion,$datos){
	//añade al cliente en la tienda en la que se encuentra
	
	if (!$datos->schemaValidate('schema/go.xsd')) { //Necesitamos schema

        echo 'DOMDocument::schemaValidate() Generated Errors!';
        libxml_display_errors();
    }
	
	$ip = $datos->getElementsByTagName('ip')->item(0)->nodeValue;
    $puerto = $datos->getElementsByTagName('puerto')->item(0)->nodeValue;
	$tienda = $datos->getElementsByTagName('puerto')->item(1)->nodeValue;
	
	$entrar_tienda="INSERT INTO cliente (ip,puerto,tiendaActual) VALUES ('$ip','$puerto','$tienda')";
	
	if (!$con->query($entrar_tienda)) {
        printf("Error: %s\n", $con->error);
    }
}

function extraerProductos(){
	//Devuelve el listado de productos de una tienda
	$productos='select nombre from productos';
	mysql_query($productos,$conexion);
}

function comprarProducto($conexion,$producto){
	//si la tienda tiene el producto y hay unidades suficientes para vender al cliente
	//vendemos el producto y modificamos el stock de la tienda
	$actualizar='update tiendas..';
	mysql_query($actualizar,$conexion); //actualizamos el stock de ese producto en la tienda 
}

function extraerTiendas($conexion,$cliente){
	//devuelve el listado de tiendas de un cliente que esta en la misma tienda
	$tiendas = 'select tiendas from cliente_tiendas where idcliente='.$cliente;
	mysql_query($tiendas,$conexion);
}

function salirTienda(){
	//borra al cliente de la tienda en la que se encontraba
	
	if (!$datos->schemaValidate('schema/go.xsd')) { //Necesitamos schema

        echo 'DOMDocument::schemaValidate() Generated Errors!';
        libxml_display_errors();
    }
	
	$ip = $datos->getElementsByTagName('ip')->item(0)->nodeValue;
    $puerto = $datos->getElementsByTagName('puerto')->item(0)->nodeValue;
	
	$salir_tienda="DELETE FROM cliente WHERE ip = '$ip' and puerto = '$puerto'";
	
	if (!$con->query($entrar_tienda)) {
        printf("Error: %s\n", $con->error);
    }
}

function cerrarTienda($con,$datos){
    //si nos quedamos sin productos cerramos el acceso a la tienda
    $puerto_dst = $datos->getElementsByTagName('puerto')->item(1)->nodeValue;

    $cerrar_tienda="UPDATE tienda SET estado=0 WHERE puerto = '$puerto_dst'";

    if (!$con->query($cerrar_tienda)) {
        printf("Error: %s\n", $con->error);
    }
	
}

function cerrarSimulacion($con){
    //el monitor manda una peticion para finalizar el proceso
    
    $cerrar_simulacion="UPDATE tienda SET estado=0";

    if (!$con->query($cerrar_simulacion)) {
        printf("Error: %s\n", $con->error);
    }
}


	//Enviar datos a una ip y puerto
function sendData($ip,$port,$input_data) {
		
		$headers = array(
			"Content-type: application/xml",
			"Content-length: " . strlen($input_data)
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ip);
		curl_setopt($ch, CURLOPT_PORT, $port);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$input_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
		$data = curl_exec($ch);
		curl_close($ch);
		
		return $data;
}
    
    
function ackAgenteIniciado($ip_src,$port_src, $ip_dst,$port_dst) {

        $doc = new DOMDocument();
		$doc->load('xml/ackagenteiniciado.xml');

		//Rellenamos fichero xml
		$doc->getElementsByTagName('ip')->item(0)->nodeValue = $ip_src;
		$doc->getElementsByTagName('ip')->item(1)->nodeValue = $ip_dst;

		$doc->getElementsByTagName('puerto')->item(0)->nodeValue = $port_src;
		$doc->getElementsByTagName('puerto')->item(1)->nodeValue = $port_dst;
		
		$xml =  $doc->saveXML();
			
		if (!$doc->schemaValidate('schema/ackagenteiniciado.xsd')) {
			echo 'DOMDocument::schemaValidate() Generated Errors!';
			libxml_display_errors();
        }
        
        sendData($ip_dst,$port_dst,$xml);
}
	
function initTienda($ip_src,$port_src, $ip_dst,$port_dst) {
		
    $doc = new DOMDocument();
    $doc->load('xml/peticionconexion.xml');

    //Rellenamos fichero xml
    $doc->getElementsByTagName('ip')->item(0)->nodeValue = $ip_src;
    $doc->getElementsByTagName('ip')->item(1)->nodeValue = $ip_dst;

    $doc->getElementsByTagName('puerto')->item(0)->nodeValue = $port_src;
    $doc->getElementsByTagName('puerto')->item(1)->nodeValue = $port_dst;
    
    $xml =  $doc->saveXML();
			
    if (!$doc->schemaValidate('schema/peticionconexion.xsd')) {
        echo 'DOMDocument::schemaValidate() Generated Errors!';
        libxml_display_errors();
    }
			
    else {
			
        //Enviamos fichero y esperamos respuesta para validarla
        $res = new DOMDocument();
        $response =  sendData($ip_dst,$port_dst,$xml); 
        $res->loadXML($response);
				
        if (!$res->schemaValidate('schema/ackinicio.xsd')) {
            echo 'DOMDocument::schemaValidate() Generated Errors!';
            libxml_display_errors();
        }
            
        echo "<br>Tienda: ".$port_src. " Ok\n";
		}	
}

function initStock($producto,$tienda,$con) {

    $tienda_query = "INSERT INTO tienda (puerto,estado) VALUES ('$tienda',TRUE)";

    if (!$con->query($tienda_query)) {
        printf("Error Tienda: %s\n", $con->error);
    }

    $nom_prod = $producto->getElementsByTagName('nombre')->item(0)->nodeValue;
    $cant_prod = $producto->getElementsByTagName('cantidad')->item(0)->nodeValue;

    $producto_query = "INSERT INTO producto VALUES ('$nom_prod')";

    if (!$con->query($producto_query)) {
        printf("Error Producto: %s\n", $con->error);
    }

    $stock_query = "INSERT INTO stock VALUES ('$tienda','$nom_prod', '$cant_prod')";

    if (!$con->query($stock_query)) {
        printf("Error Stock: %s\n", $con->error);
    }
}


?>