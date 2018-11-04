<?php

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

?>


            
        