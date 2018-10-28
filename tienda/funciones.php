<?php

	
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
	
	
	function initTiendas($ip_dst,$port_dst) {
		
		$doc = new DOMDocument();
		$doc->load('xml/peticionconexion.xml');
		
		$res = new DOMDocument();
		
		
		$mysqli = new mysqli("localhost", "root", "toor", "multiagentes");
		
		if ($mysqli->connect_errno) {
			echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}
		
		$resultado = $mysqli->query("SELECT puerto FROM tienda") or die($mysqli->error);
		
		//$doc->getElementsByTagName('ip')->item(0)->nodeValue = $_SERVER['SERVER_ADDR'];
		$doc->getElementsByTagName('ip')->item(0)->nodeValue = "192.168.1.1";
		$doc->getElementsByTagName('ip')->item(1)->nodeValue = $ip_dst;
		$doc->getElementsByTagName('puerto')->item(1)->nodeValue = $port_dst;
		
		while ($fila = $resultado->fetch_assoc()) {
			
			$doc->getElementsByTagName('puerto')->item(0)->nodeValue = $fila['puerto'];
			
			$xml =  $doc->saveXML();
			
			if (!$doc->schemaValidate('schema/peticionconexion.xsd')) {
				echo 'DOMDocument::schemaValidate() Generated Errors!';
				libxml_display_errors();
			}
			
			else {
				
				$response =  sendData($ip_dst,$port_dst,$xml); 
				$res->loadXML($response);
				
				if (!$res->schemaValidate('schema/ackinicio.xsd')) {
					echo 'DOMDocument::schemaValidate() Generated Errors!';
					libxml_display_errors();
				}
			}
			
		}
		
	}

	function initStock($con) {
	}
	
?>