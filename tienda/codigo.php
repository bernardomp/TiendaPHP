<?php

//=====================================================================================
	// AUTOR: BERNARDO MARTINEZ PARRAS
	// NOMBRE:sendData
	// DESCRIPCIÓN: Envia datos a la direccion y puerto especificado mediante la funcion
	//				predefinida curl de php
	// ARGUMENTOS:
	//	$ip: Ip de destino de los datos que queremos enviar
	//	$port: Puerto de destino de los datos que queremos enviar
	//	$input_data: Fichero xml que vamos a enviar 

	// FUENTE: 
	//	https://stackoverflow.com/questions/7916184/how-to-properly-send-and-receive-xml-using-curl
	//	http://php.net/manual/es/book.curl.php

	// SALIDA : Un string representando el resultado de la tranferencia que nos devuelve
	//			el sistema ubicado en la ip y puerto de destino
//====================================================================================
function sendData($ip,$port,$input_data) {
		
	//Establecemos la cabecera del mensaje indicando el formato y la longitud del mismo
	$headers = array(
		"Content-type: application/xml",
		"Content-length: " . strlen($input_data)
	);

	$ch = curl_init(); //Inicia sesión cURL

	//Configura las opciones para una transferencia cURL

	curl_setopt($ch, CURLOPT_URL, $ip); //Establecemos la ip de destino de conexion
	curl_setopt($ch, CURLOPT_PORT, $port); //Establecemos el puerto de destino de conexion
	curl_setopt($ch, CURLOPT_POST, true); //Indicamos que queremos hacer una peticion post
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); //Establecemos la cabecera descrita anteriormente
	curl_setopt($ch, CURLOPT_POSTFIELDS,$input_data); //Establecemos todos los datos a enviar mediante la peticion post
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //TRUE para devolver el resultado de la transferencia como string
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300); //Numero de segundos a esperar cuando se está intentado conecta

	$data = curl_exec($ch); //Establece una sesión cURL
	curl_close($ch); //Cierra una sesión cURL
	
	//Devolvemos el resultado de la transferencia de datos
	return $data;
}    

?>


            
        