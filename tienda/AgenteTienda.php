<?php
    include "codigo.php";

    class AgenteTienda {

        private $ip_monitor;
        private $puerto_monitor;
        
        private $ip_tienda;
        private $puerto_tienda;

        private $con; //Conexion a la BBDD
        private $xml;


        function __construct($ip_monitor,$puerto_monitor,$ip_tienda,$puerto_tienda) {
            $this->ip_monitor = $ip_monitor;
            $this->puerto_monitor = $puerto_monitor;

            $this->ip_tienda = $ip_tienda;
            $this->puerto_tienda = $puerto_tienda;
            
        }

        public function setXML($xml) {
            $this->xml = $xml;
        }

        public function conexionBBDD($ip,$user,$password,$database) {
            $this->con = new mysqli($ip, $user, $password, $database);

            if ($this->con->connect_errno) {
                printf("Connect failed: %s\n", $mysqli->connect_error);
                exit();
            }
        }

        public function solicitarTiendas(){
            //mandamos peticion al monitor con nuestra ip y puerto para que cree la tienda
        
            $ntiendas = rand(5,10); //Generamos un numero aleatorio de tiendas
            
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
        
                sendData($this->ip_monitor,$this->puerto_monitor,$xml); 
            }
        }


        //recibe los id del monitor
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

        //Inicializa el stock para cada tienda
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

                $producto_query = "INSERT INTO producto VALUES ('$nom_prod')";
                $stock_query = "INSERT INTO stock VALUES ('$tienda','$nom_prod', '$cant_prod')";

                if (!$this->con->query($producto_query)) {
                    printf("Error Producto: %s\n", $this->con->error);
                }

                if (!$this->con->query($stock_query)) {
                    printf("Error Stock: %s\n", $this->con->error);
                }

            }

            $this->agenteIniciado($tienda);
        }

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

        //añade al cliente en la tienda en la que se encuentra
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

        //borra al cliente de la tienda en la que se encontraba
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

        public function cerrarTienda(){
            //si nos quedamos sin productos cerramos el acceso a la tienda
            $id_tienda = $this->xml->getElementsByTagName('id')->item(1)->nodeValue;
            $cerrar_tienda="UPDATE tienda SET estado=0 WHERE id = '$id_tienda'";
        
            if (!$this->con->query($cerrar_tienda)) {
                printf("Error: %s\n", $this->con->error);
            }
        }

        //el monitor manda una peticion para finalizar el proceso
        public function finSimulacion(){

            $cerrar_simulacion="UPDATE tienda SET estado=0";
        
            if (!$this->con->query($cerrar_simulacion)) {
                printf("Error: %s\n", $this->con->error);
            }
        }
        
        //Devuelve el listado de productos de una tienda
        public function extraerProductos(){
           
            $productos='select nombre from producto';
            mysql_query($productos,$conexion);
        }
        
        
        //si la tienda tiene el producto y hay unidades suficientes para vender al cliente
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
            sendData($ipCliente,$puertoCliente,$xml);
            echo $xml;
        
            
        }
        
        function extraerTiendas($conexion,$cliente){
            //devuelve el listado de tiendas de un cliente que esta en la misma tienda
            $tiendas = 'select tiendas from cliente_tiendas where idcliente='.$cliente;
            mysql_query($tiendas,$conexion);
        }


    }



?>