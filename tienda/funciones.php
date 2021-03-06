<?php
    header("Access-Control-Allow-Origin: *");
    if(!isset($_POST["method"])) {
        die();
    }
    
    /*
        Este fichero nos permite actualizar nuestra interfaz grafica con la información de
        la base de datos
    */

    //Establecemos ka conexion con la bbdd
    $con = new mysqli("localhost", "root", "toor", "multiagentes");

    if ($con->connect_errno) {
        die("Fallo al conectar a MySQL: " . $con->connect_error);
    }

    //Verificamos que funcion hemos recibido desde nuestra interfaz
    switch ($_POST["method"]) {
        case 'updateTiendas':
            updateTiendas($con);
            break;

        case 'updateLog':
            updateLog($con);
            break;

        case 'borrarBBDD':
            borrarBBDD($con);
            break;
        
        default:
            break;
    }

    //=====================================================================================
	    // AUTOR:
        // NOMBRE: borrarBBDD
	    // DESCRIPCIÓN: Elimina todas los registros de la base de datos
	    // ARGUMENTOS: --
	    // FUENTE: --
        // SALIDA: --
    //====================================================================================
    function borrarBBDD($con) {
        $query1 = "DELETE FROM cliente";
        $query2 = "DELETE FROM clientetienda";
        $query3 = "DELETE  FROM errores";
        $query4 = "DELETE FROM producto";
        $query5 = "DELETE  FROM stock";
        $query6 = "DELETE  FROM tienda";


        # Si alguna de las operaciones de borrado falla imprime el error correspondiente

        if (!$con->query($query3)) {
            printf("Error: %s\n", $this->con->error);
        }

        if (!$con->query($query5)) {
            printf("Error: %s\n", $this->con->error);
        }

        if (!$con->query($query4)) {
            printf("Error: %s\n", $this->con->error);
        }

        if (!$con->query($query2)) {
            printf("Error: %s\n", $this->con->error);
        }

        if (!$con->query($query6)) {
            printf("Error: %s\n", $this->con->error);
        }

        if (!$con->query($query1)) {
            printf("Error: %s\n", $this->con->error);
        }

    }
    

    //=====================================================================================
	    // AUTOR:
        // NOMBRE: updateLog
	    // DESCRIPCIÓN: Muestra los errores encontrados que hay guardados en la tabla de la base de datos
	    // ARGUMENTOS: --
	    // FUENTE: --
        // SALIDA: --
    //====================================================================================
    function updateLog($con) {    

        if(!isset($_POST["index"])) {
            die();
        }

        $index = $_POST["index"];

        # Se seleccionan todos los errores de la tabla según el id y por tiempo ascendente
        $msgs = "SELECT * FROM `errores` WHERE id > '$index' ORDER BY time ASC";
        $error = $con->query($msgs);

        $brr = array();

        //Recorremos la bbdd y extraemos los campos seleccionados
        while($row = mysqli_fetch_array($error)) {
            $arr = array();
            array_push($arr,$row["id"]);
            array_push($arr,$row["msg"]);
            array_push($arr,$row["time"]);

            array_push($brr,$arr); //Almcenamos los campos en un array           
        }

        //Devolvemos el array a nuestra interfaz en formato json
        echo json_encode($brr);

    }


    //=====================================================================================
	    // AUTOR:
        // NOMBRE: updateTiendas
	    // DESCRIPCIÓN: Actualiza el estado de las tiendas
	    // ARGUMENTOS: --
	    // FUENTE: --
        // SALIDA: --
    //====================================================================================
    function updateTiendas($con) {

        $query = "SELECT * FROM `tienda`";

        if (!$tiendas = $con->query($query)) {
            printf("Error: %s\n", $con->error);
        }

        //Recorremos la bbdd
        while($row = mysqli_fetch_array($tiendas)) {
        
            //Generamos el codigo html
            echo "<div class='flexitem'>";

                echo "<div>Tienda: " .$row["id"] . "</div>";

                    echo "<div>";
                        
                    if ($row["estado"] == 0) {
                        echo "Estado: Cerrado"; 
                    }  
    
                    else {
                        echo "Estado: Abierto"; 
                    }
        
                    echo "</div>";

                    echo "<div>";
                        
                        $idtienda = $row["id"];
                        $stocks = "SELECT * FROM `stock` WHERE idtienda='$idtienda'";
                        $stock = $con->query($stocks);

                        //Devolvemos la productos de cada tienda
                        while($row = mysqli_fetch_array($stock)) {
                
                            echo "<div>" . $row["idproducto"].": ".$row["cantidad"]. "</div>";
                            
                        }
                        
                    echo "</div>";

            echo "</div>";
                
        }
    }




?>