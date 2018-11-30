<?php
    header("Access-Control-Allow-Origin: *");
//=====================================================================================

            // AUTOR: 
            // CLASE: AgenteTienda
            // DESCRIPCIÓN:

            // ATRIBUTOS:
            //	$ip_monitor: direccion ip del host
            //	$puerto_monitor: nombre del usuario
            //	$ip_tienda: password del usuario
            //	$puerto_tienda: nombre de la base de datos
            //  $con:Conexion a la BBDD
            //  $xml: 


        //==================================================================================== 
    class AgenteTienda {

        private $ip_monitor;
        private $puerto_monitor;
        
        private $ip_tienda;
        private $puerto_tienda;

        private $con; 
        private $xml;


        function __construct($ip_monitor,$puerto_monitor,$ip_tienda,$puerto_tienda) {
            $this->ip_monitor = $ip_monitor;
            $this->puerto_monitor = $puerto_monitor;

            $this->ip_tienda = $ip_tienda;
            $this->puerto_tienda = $puerto_tienda;
            
        }

        public function getXML() {
            return $this->xml;
        }

        public function setXML($data) {
            $xml = new DOMDocument();

            if (!$xml->loadXML($data)) {
                echo 'Error al convertir el documento xml';
                $this->showErrors(NULL,"Error parsing ".$data);
                exit;
            }
            else {
                $this->xml = $xml;
            }
        }


        //=====================================================================================

            // AUTOR: 
            // NOMBRE: conexionBBDD
            // DESCRIPCIÓN: Se conecta con la base de datos

            // ARGUMENTOS:
            //	$ip: direccion ip del host
            //	$user: nombre del usuario
            //	$password: password del usuario
            //	$database: nombre de la base de datos

            // FUENTE: 
            //	http://php.net/manual/es/book.curl.php

            // SALIDA: --

        //====================================================================================        
        public function conexionBBDD($ip,$user,$password,$database) {
            $this->con = new mysqli($ip, $user, $password, $database);

            if ($this->con->connect_errno) {
                printf("Connect failed: %s\n", $mysqli->connect_error);
                exit();
            }
        }


        //=====================================================================================

            // AUTOR: 
            // NOMBRE: solicitarTiendas
            // DESCRIPCIÓN: Manda peticion al monitor con nuestra ip y puerto para que cree las tiendas
            // ARGUMENTOS: --
            // FUENTE: --
            // SALIDA: --

        //====================================================================================    
        public function solicitarTiendas($ntiendas){

            //$ntiendas = rand(5,10); //Generamos un numero aleatorio de tiendas
            
            //Preparamos el xml
            $doc = new DOMDocument();
            $doc->load('xml/peticionconexion.xml');
        
            //Rellenamos fichero xml
            $doc->getElementsByTagName('ip')->item(0)->nodeValue = $this->ip_tienda;
            $doc->getElementsByTagName('ip')->item(1)->nodeValue = $this->ip_monitor;
        
            $doc->getElementsByTagName('puerto')->item(0)->nodeValue = $this->puerto_tienda;
            $doc->getElementsByTagName('puerto')->item(1)->nodeValue = $this->puerto_monitor;
            
            for ($i = 0; $i<$ntiendas; $i++){
                //mandamos peticion al monitor para cada una de las tiendas pasandole la ip y puerto
        
                $xml =  $doc->saveXML();
                    
                /*
                if (!$doc->schemaValidate('schema/peticionconexion.xsd')) {
                    echo 'DOMDocument::schemaValidate() Generated Errors!';
                    libxml_display_errors();
                }
                    
                else {
                    //Enviamos fichero
                    $res = new DOMDocument();
                    sendData($ip_dst,$port_dst,$xml); 
                }	
                */
        
                $response = $this->sendData($this->ip_monitor,$this->puerto_monitor,$xml);
                $this->setXML($response);
                $this->obtenerTiendaID();
            }

        }



        //=====================================================================================

            // AUTOR: 
            // NOMBRE: obtenerTiendaID
            // DESCRIPCIÓN: Recibe los id de las tiendas desde el monitor
            // ARGUMENTOS: --
            // FUENTE: --
            // SALIDA: --

        //====================================================================================   
        public function obtenerTiendaID() {

            /*
            if (!xml->schemaValidate('schema/ackinicio.xsd')) {
                echo 'DOMDocument::schemaValidate() Generated Errors!';
                libxml_display_errors();
            }
            */

            $idTienda = $this->xml->getElementsByTagName('nuevoID')->item(0)->nodeValue;
           
            $init_tienda="INSERT INTO tienda (id) VALUES ('$idTienda')";
            
            if (!$this->con->query($init_tienda)) {
                printf("Error: %s\n", $this->con->error);
            }
        
        }



        //=====================================================================================

            // AUTOR: 
            // NOMBRE: iniciarTiendaStock
            // DESCRIPCIÓN: Inicializa el stock para cada tienda
            // ARGUMENTOS: --
            // FUENTE: --
            // SALIDA: --

        //====================================================================================         
        public function iniciarTiendaStock(){

            //Validamos xml
            /*
            if (!$datos->schemaValidate('schema/inicializaciontienda.xsd')) {
                echo 'DOMDocument::schemaValidate() Generated Errors!';
                libxml_display_errors();
            }
            */

            $productos = $this->xml->getElementsByTagName('producto');
            $tienda = $this->xml->getElementsByTagName('id')->item(0)->nodeValue;

            foreach($productos as $producto) {
        
                $nom_prod = $producto->getElementsByTagName('nombre')->item(0)->nodeValue;
                $cant_prod = $producto->getElementsByTagName('cantidad')->item(0)->nodeValue;

                $nom_prod = trim($nom_prod);
                $cant_prod = intval($cant_prod);

                $producto_query = "INSERT INTO producto VALUES ('$nom_prod')";
                $stock_query = "INSERT INTO stock VALUES ('$tienda','$nom_prod', '$cant_prod')";

                if (!$this->con->query($producto_query)) {
                    $this->showErrors($tienda,"Error Producto: ".$nom_prod." Cantidad: ".$cant_prod. " Tienda: ".$tienda);
                }

                if (!$this->con->query($stock_query)) {
                    $this->showErrors($tienda,"Error Stock".$nom_prod." Cantidad:".$cant_prod. "Tienda: ".$tienda);
                }

            }
            $this->showErrors($tienda,"Hola".$this->con->error);
            return $this->agenteIniciado($tienda);
        }



        //=====================================================================================

            // AUTOR: 
            // NOMBRE: agenteIniciado
            // DESCRIPCIÓN: iniciamos todos los agentes tienda
            // ARGUMENTOS:
            //  $tienda: le pasamos como parametro la tienda que queremos inicializar
            // FUENTE: --
            // SALIDA: --

        //====================================================================================           
        public function agenteIniciado($tienda) {

            $doc = new DOMDocument();
            $doc->load('xml/ackagenteiniciado.xml');
        
            //Rellenamos fichero xml
            $doc->getElementsByTagName('ip')->item(0)->nodeValue = $this->ip_tienda;
            $doc->getElementsByTagName('ip')->item(1)->nodeValue = $this->ip_monitor;
        
            $doc->getElementsByTagName('puerto')->item(0)->nodeValue = $this->puerto_tienda;
            $doc->getElementsByTagName('puerto')->item(1)->nodeValue = $this->puerto_monitor;
                
            $doc->getElementsByTagName('id')->item(0)->nodeValue = $tienda;
                
            $xml =  $doc->saveXML();
                  
            /*
            if (!$doc->schemaValidate('schema/ackagenteiniciado.xsd')) {
                echo 'DOMDocument::schemaValidate() Generated Errors!';
                libxml_display_errors();
            }
            */

            //$this->sendData($this->ip_monitor,$this->puerto_monitor,$xml);
            //echo $xml; //Con echo debe ser suficiente
            return $xml;
        }



        //=====================================================================================

            // AUTOR: 
            // NOMBRE: iniciarSimulacion
            // DESCRIPCIÓN: Inicia la simulacion 
            // ARGUMENTOS: mandamos una petición al monitor para que inicie la simulación de
            //              la parte servidor
            // FUENTE: --
            // SALIDA: en caso de fallo muestra un error

        //====================================================================================           
        public function iniciarSimulacion() {

            /*
            if (!$datos->schemaValidate('schema/go.xsd')) {
                echo 'DOMDocument::schemaValidate() Generated Errors!';
                libxml_display_errors();
            }*/
        
            $id_tienda = $this->xml->getElementsByTagName('id')->item(0)->nodeValue;

            
            $id_tienda = intval($id_tienda);
        
            $abrir_tienda="UPDATE tienda SET estado=1 WHERE id='$id_tienda'";
        
            if (!$this->con->query($abrir_tienda)) {
                printf("Error: %s\n", $this->con->error);
            }
            
        }




        //=====================================================================================

            // AUTOR: 
            // NOMBRE: entrarTienda
            // DESCRIPCIÓN: Añade al cliente en la tienda en la que se encuentra
            // ARGUMENTOS: --
            // FUENTE: --
            // SALIDA: --

        //====================================================================================  
        public function entrarTienda(){
            /*
            if (!$datos->schemaValidate('schema/go.xsd')) { //Necesitamos schema
                echo 'DOMDocument::schemaValidate() Generated Errors!';
                libxml_display_errors();
            }*/
            
            $idCliente = $this->xml->getElementsByTagName('id')->item(0)->nodeValue;
            $ipCliente = $this->xml->getElementsByTagName('ip')->item(0)->nodeValue;
            $puertoCliente = $this->xml->getElementsByTagName('puerto')->item(0)->nodeValue;
            $idTienda = $this->xml->getElementsByTagName('id')->item(1)->nodeValue;

            
            $puertoCliente = intval($puertoCliente);
            
            $entrar_tienda="INSERT INTO cliente (ip,puerto,idCliente,tiendaActual) VALUES ('$ipCliente','$puertoCliente','$idCliente','$idtienda')";
            
            if (!$this->con->query($entrar_tienda)) {
                printf("Error: %s\n", $this->con->error);
            }

            //Prepara respuesta
            $doc = new DOMDocument();
            $doc->load('xml/ackentrartienda.xml');


            //Insertamos los ids
            $doc->getElementsByTagName('id')->item(0)->nodeValue = $id_tienda;
            $doc->getElementsByTagName('id')->item(1)->nodeValue = $idCliente;

            //Insertamos ip
            $doc->getElementsByTagName('ip')->item(0)->nodeValue = $this->ip_tienda;
            $doc->getElementsByTagName('ip')->item(1)->nodeValue = $ipCliente;

            //Insertamos puerto
            $doc->getElementsByTagName('puerto')->item(0)->nodeValue = $this->puerto_tienda;
            $doc->getElementsByTagName('puerto')->item(1)->nodeValue = $puertoCliente;

            //Insertamos mensaje
            $doc->getElementsByTagName('msg')->item(0)->nodeValue = "OK";

            $xml =  $doc->saveXML();
            //$this->sendData($ipCliente,$puertoCliente,$xml);
            //Con echo debe ser suficiente
            echo $xml;
        }



        //=====================================================================================

            // AUTOR: 
            // NOMBRE: salirTienda
            // DESCRIPCIÓN: Borra al cliente de la tienda en la que se encontraba
            // ARGUMENTOS: --
            // FUENTE: --
            // SALIDA: en caso de fallo muestra un error

        //====================================================================================  
        public function salirTienda(){
            /*
            if (!$datos->schemaValidate('schema/go.xsd')) { //Necesitamos schema
                echo 'DOMDocument::schemaValidate() Generated Errors!';
                libxml_display_errors();
            }
            */
            
            $id_cliente = $datos->getElementsByTagName('id')->item(0)->nodeValue;

          
            $id_cliente = intval($id_cliente);
            $salir_tienda="DELETE FROM cliente WHERE idcliente = '$id_cliente'";
            
            if (!$this->con->query($entrar_tienda)) {
                printf("Error: %s\n", $this->con->error);
            }
        }



        //=====================================================================================

            // AUTOR: 
            // NOMBRE: cerrarTienda
            // DESCRIPCIÓN: Cierra el acceso a la tienda si se queda sin productos
            // ARGUMENTOS: --
            // FUENTE: --
            // SALIDA: en caso de fallo muestra un error

        //====================================================================================          
        public function cerrarTienda(){

            $id_tienda = $this->xml->getElementsByTagName('id')->item(1)->nodeValue;

            $id_tienda = trim($id_tienda);
            $cerrar_tienda="UPDATE tienda SET estado=0 WHERE id = '$id_tienda'";
        
            if (!$this->con->query($cerrar_tienda)) {
                printf("Error: %s\n", $this->con->error);
            }
        }



        //=====================================================================================

            // AUTOR: 
            // NOMBRE: finSimulacion
            // DESCRIPCIÓN: El monitor manda una peticion para finalizar el proceso
            // ARGUMENTOS: --
            // FUENTE: --
            // SALIDA: en caso de fallo muestra un error.

        //====================================================================================  
        public function finSimulacion(){

            /*
            $cerrar_simulacion="UPDATE tienda SET estado=0";
        
            if (!$this->con->query($cerrar_simulacion)) {
                printf("Error: %s\n", $this->con->error);
            }*/

            $this->resetAgente();
        }
        


        //=====================================================================================

            // AUTOR: 
            // NOMBRE: extraerProductos
            // DESCRIPCIÓN: devuelve el listado de productos de una tienda cuando el cliente
            //              ha solicitado una peticion para conocer los productos
            // ARGUMENTOS: ($tienda)
            // FUENTE: --
            // SALIDA: lista de productos de una tienda

        //====================================================================================  
        public function extraerProductos(){
           
            $productos='select nombre from producto';
            mysql_query($productos,$conexion);
        }
        
        


        //=====================================================================================

            // AUTOR: 
            // NOMBRE: comprarProducto
            // DESCRIPCIÓN: el cliente manda una petición de comprar el producto. Mediante esta
            //              funcion comprobamos en la base de datos que existe el producto y 
            //              que se encuentra en la cantidad seleccionada
            // ARGUMENTOS: --
            // FUENTE: --
            // SALIDA: devuelve un error si la cantidad es incorrecta 

        //====================================================================================     
        public function comprarProducto(){
        
            $idcliente = $this->xml->getElementsByTagName('id')->item(0)->nodeValue;
            $idtienda = $this->xml->getElementsByTagName('id')->item(1)->nodeValue;

            $ipCliente = $this->xml->getElementsByTagName('ip')->item(0)->nodeValue;
            $puertoCliente = $this->xml->getElementsByTagName('puerto')->item(0)->nodeValue;

            $producto = $this->xml->getElementsByTagName('nombre')->item(0)->nodeValue;

            $cant = $this->xml->getElementsByTagName('cantidad')->item(0)->nodeValue;
            $cant = intval($cant);

            $doc = new DOMDocument();
            $doc->load('xml/respuestacomprar.xml');

            //Insertamos los ids
            $doc->getElementsByTagName('id')->item(0)->nodeValue = $idtienda;
            $doc->getElementsByTagName('id')->item(1)->nodeValue = $idcliente;

            //Insertamos ip
            $doc->getElementsByTagName('ip')->item(0)->nodeValue = $this->ip_tienda;
            $doc->getElementsByTagName('ip')->item(1)->nodeValue = $ipCliente;

            //Insertamos puerto
            $doc->getElementsByTagName('puerto')->item(0)->nodeValue = $this->puerto_tienda;
            $doc->getElementsByTagName('puerto')->item(1)->nodeValue = $puertoCliente;


            if($cant>=1) {
                //vendemos el producto y modificamos el stock de la tienda
                $producto= trim($producto);
                
                $actualizar_stock="UPDATE Stock SET cantidad = cantidad-'$cant' WHERE idproducto='$producto'";

                //actualizamos el stock de ese producto en la tienda 
                if (!$this->con->query($actualizar_stock)) {
                    printf("Error: %s\n", $this->con->error);
                }

                //Insertamos mensaje
                $doc->getElementsByTagName('contenido')->item(0)->nodeValue = "OK";
            }

            else {
                //Insertamos mensaje
                $doc->getElementsByTagName('contenido')->item(0)->nodeValue = "No devoluciones";
            }

            $xml =  $doc->saveXML();
            //$this->sendData($ipCliente,$puertoCliente,$xml);
            echo $xml;
        
            
        }

        

        //=====================================================================================

            // AUTOR: 
            // NOMBRE: extraerTiendas
            // DESCRIPCIÓN: nos muestra un listado de tiendas de un cliente que se encuentra
            //              en la misma tienda.
            // ARGUMENTOS:
            //  $conexion: conexion a la base de datos
            //  $cliente: id del cliente del que queremos saber el listado de tiendas
            // FUENTE: --
            // SALIDA: Devuelve el listado de tiendas de un cliente que esta en la misma tienda

        //====================================================================================          
        function extraerTiendas($conexion,$cliente){

            $tiendas = 'select tiendas from cliente_tiendas where idcliente='.$cliente;
            mysql_query($tiendas,$conexion);
        }


        function showErrors($tienda,$msg) {
            
            if($tienda === NULL) {
                $error="INSERT INTO Errores (msg) VALUES ('$msg')";
            }
            
            else {
                $error="INSERT INTO Errores (tienda,msg) VALUES ('$tienda','$msg')";
            }

          
            if (!$this->con->query($error)) {
                printf("Error: %s\n", $this->con->error);
            }
        }
        
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
            $this->showErrors(NULL,$data);
            curl_close($ch); //Cierra una sesión cURL
            
            //Devolvemos el resultado de la transferencia de datos
            return $data;
        }

        function resetAgente() {
            $query1 = "DELETE FROM cliente";
            $query2 = "DELETE FROM clientetienda";
            $query3 = "DELETE  FROM errores";
            $query4 = "DELETE FROM producto";
            $query5 = "DELETE  FROM stock";
            $query6 = "DELETE  FROM tienda";
    
            if (!$this->con->query($query3)) {
                printf("Error: %s\n", $this->con->error);
            }

            if (!$this->con->query($query5)) {
                printf("Error: %s\n", $this->con->error);
            }

            if (!$this->con->query($query4)) {
                printf("Error: %s\n", $this->con->error);
            }

            if (!$this->con->query($query2)) {
                printf("Error: %s\n", $this->con->error);
            }

            if (!$this->con->query($query6)) {
                printf("Error: %s\n", $this->con->error);
            }

            if (!$this->con->query($query1)) {
                printf("Error: %s\n", $this->con->error);
            }
    
        }

    }



?>
