<?php
    include "codigo.php";

    class AgenteTienda {

        //Atributos de la clase
        private $ip_monitor;
        private $puerto_monitor;
        
        private $ip_tienda;
        private $puerto_tienda;

        private $con; //Conexion a la BBDD
        private $xml; //Fichero xml que hemos recibido


        //Constructor que inicializa los atributos de la clase
        function __construct($ip_monitor,$puerto_monitor,$ip_tienda,$puerto_tienda) {
            $this->ip_monitor = $ip_monitor;
            $this->puerto_monitor = $puerto_monitor;

            $this->ip_tienda = $ip_tienda;
            $this->puerto_tienda = $puerto_tienda;
            
        }

         //=====================================================================================

            // AUTOR: 
            // NOMBRE: setXML
            // DESCRIPCIÓN: Establece la propiedad xml con un fichero xml recibido

            // ARGUMENTOS:
            //	$xml: Fichero xml

            // SALIDA: --

        //==================================================================================== 

        public function setXML($xml) {
            $this->xml = $xml;
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

            //Establecemos la conexion con la ip, usuario, contraseña y basede datos
            $this->con = new mysqli($ip, $user, $password, $database);

            //Comprobamos que no existe ningun error al establecer la conexion
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
        public function solicitarTiendas(){

            $ntiendas = rand(5,10); //Generamos un numero aleatorio de tiendas
            
            //Preparamos el xml determinado
            $doc = new DOMDocument();
            $doc->load('xml/peticionconexion.xml');
        
            //Rellenamos fichero xml
            
            //Incluimos en el fichero xml la ip de origen y destino
            $doc->getElementsByTagName('ip')->item(0)->nodeValue = $this->ip_tienda;
            $doc->getElementsByTagName('ip')->item(1)->nodeValue = $this->ip_monitor;
        
             //Incluimos en el fichero xml el puerto de origen y destino
            $doc->getElementsByTagName('puerto')->item(0)->nodeValue = $this->puerto_tienda;
            $doc->getElementsByTagName('puerto')->item(1)->nodeValue = $this->puerto_monitor;
            
            //Iteramos por todas las tiendas creadas
            for ($i = 0; $i<$ntiendas; $i++){
        
                //Guardamos el fichero xml modificado anteriorment
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
        
                //Enviamos peticion al monitor para cada una de las tiendas pasandole la ip y puerto
                sendData($this->ip_monitor,$this->puerto_monitor,$xml); 
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

            //Obtenemos del fichero recibido el id para la nueva tienda creada
            $idTienda = $this->xml->getElementsByTagName('nuevoID')->item(0)->nodeValue;

            //Creamos en la tabla tienda un nuevo registro
            $init_tienda="INSERT INTO tienda (id) VALUES ('$idTienda')";
            
            //Comprobamos que la consulta se ha ejecutado correctamente
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

            //Obtenemos del fichero xml la lista de productos recibidos
            $productos = $this->xml->getElementsByTagName('producto');

            //Obtenemos del fichero xml el identificador de la tienda
            $tienda = $this->xml->getElementsByTagName('id')->item(0)->nodeValue;

            //Iteramos sobre la lista de productos
            foreach($productos as $producto) {
        
                //Para cada producto obtenemos el nombre y la cantidad
                $nom_prod = $producto->getElementsByTagName('nombre')->item(0)->nodeValue;
                $cant_prod = $producto->getElementsByTagName('cantidad')->item(0)->nodeValue;

                //Actualizamos la lista de productos y el stock asociado a cada tienda
                $producto_query = "INSERT INTO producto VALUES ('$nom_prod')";
                $stock_query = "INSERT INTO stock VALUES ('$tienda','$nom_prod', '$cant_prod')";

                //Comprobamos que la consulta se ha ejecudao correctamente
                if (!$this->con->query($producto_query)) {
                    printf("Error Producto: %s\n", $this->con->error);
                }

                //Comprobamos que la consulta se ha ejecudao correctamente
                if (!$this->con->query($stock_query)) {
                    printf("Error Stock: %s\n", $this->con->error);
                }

            }

            //Enviamos un mensaje de respuesta al monitor
            $this->agenteIniciado($tienda);
        }



        //=====================================================================================

            // AUTOR: 
            // NOMBRE: agenteIniciado
            // DESCRIPCIÓN: 
            // ARGUMENTOS:
            //  $tienda: 
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

            sendData($this->ip_monitor,$this->puerto_monitor,$xml);
        }



        //=====================================================================================

            // AUTOR: 
            // NOMBRE: iniciarSimulacion
            // DESCRIPCIÓN: Inicia la simulacion 
            // ARGUMENTOS: --
            // FUENTE: --
            // SALIDA: --

        //====================================================================================           
        public function iniciarSimulacion() {

            /*
            if (!$datos->schemaValidate('schema/go.xsd')) {
                echo 'DOMDocument::schemaValidate() Generated Errors!';
                libxml_display_errors();
            }*/
        
            $id_tienda = $this->xml->getElementsByTagName('id')->item(0)->nodeValue;
        
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
            sendData($ipCliente,$puertoCliente,$xml);
        }



        //=====================================================================================

            // AUTOR: 
            // NOMBRE: salirTienda
            // DESCRIPCIÓN: Borra al cliente de la tienda en la que se encontraba
            // ARGUMENTOS: --
            // FUENTE: --
            // SALIDA: --

        //====================================================================================  
        public function salirTienda(){
            /*
            if (!$datos->schemaValidate('schema/go.xsd')) { //Necesitamos schema
                echo 'DOMDocument::schemaValidate() Generated Errors!';
                libxml_display_errors();
            }
            */
            
            $id_cliente = $datos->getElementsByTagName('id')->item(0)->nodeValue;
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
            // SALIDA: --

        //====================================================================================          
        public function cerrarTienda(){

            $id_tienda = $this->xml->getElementsByTagName('id')->item(1)->nodeValue;
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
            // SALIDA: --

        //====================================================================================  
        public function finSimulacion(){

            $cerrar_simulacion="UPDATE tienda SET estado=0";
        
            if (!$this->con->query($cerrar_simulacion)) {
                printf("Error: %s\n", $this->con->error);
            }
        }
        


        //=====================================================================================

            // AUTOR: 
            // NOMBRE: extraerProductos
            // DESCRIPCIÓN: Devuelve el listado de productos de una tienda
            // ARGUMENTOS: --
            // FUENTE: --
            // SALIDA: --

        //====================================================================================  
        public function extraerProductos(){
           
            $productos='select nombre from producto';
            mysql_query($productos,$conexion);
        }
        
        


        //=====================================================================================

            // AUTOR: 
            // NOMBRE: comprarProducto
            // DESCRIPCIÓN: El cliente compra el producto si la tienda lo vende y hay unidades suficientes
            // ARGUMENTOS: --
            // FUENTE: --
            // SALIDA: --

        //====================================================================================     
        public function comprarProducto(){
        
            $idcliente = $this->xml->getElementsByTagName('id')->item(0)->nodeValue;
            $idtienda = $this->xml->getElementsByTagName('id')->item(1)->nodeValue;

            $ipCliente = $this->xml->getElementsByTagName('ip')->item(0)->nodeValue;
            $puertoCliente = $this->xml->getElementsByTagName('puerto')->item(0)->nodeValue;

            $producto = $this->xml->getElementsByTagName('nombre')->item(0)->nodeValue;
            $cant = $this->xml->getElementsByTagName('cantidad')->item(0)->nodeValue;

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
                $actualizar_stock="UPDATE Stock SET cantidad = cantidad-'$cant' WHERE idproducto='$producto' and idtienda = '$idtienda' ";

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
            sendData($ipCliente,$puertoCliente,$xml);
            echo $xml;
        
            
        }

        

        //=====================================================================================

            // AUTOR: 
            // NOMBRE: extraerTiendas
            // DESCRIPCIÓN: Devuelve el listado de tiendas de un cliente que esta en la misma tienda
            // ARGUMENTOS: --
            // FUENTE: --
            // SALIDA: --

        //====================================================================================          
        function extraerTiendas($conexion,$cliente){

            $tiendas = 'select tiendas from cliente_tiendas where idcliente='.$cliente;
            mysql_query($tiendas,$conexion);
        }

        /*
        function comprobarEstadoTienda() {
            $id_tienda = $this->xml->getElementsByTagName('id')->item(1)->nodeValue;
            $estado_tienda="SELECT estado FROM tienda WHERE id = '$id_tienda'";

            if(!$estado = $this->con->query($estado_tienda)) {
                printf("Error: %s\n", $this->con->error);
            }
           
        }*/


    }



?>