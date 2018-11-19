<?php

    if(!isset($_POST["method"])) {
        die();
    }

    $con = new mysqli("localhost", "root", "toor", "multiagentes");

    if ($con->connect_errno) {
        die("Fallo al conectar a MySQL: " . $con->connect_error);
    }

    switch ($_POST["method"]) {
        case 'updateTiendas':
            updateTiendas($con);
            break;

        case 'updateLog':
            updateLog($con);
            break;
        
        default:
            break;
    }
    
    function updateLog($con) {     

        $msgs = "SELECT * FROM `errores` ORDER BY time DESC";
        $error = $con->query($msgs);

        while($row = mysqli_fetch_array($error)) {
                
            echo "<div>" . $row["tienda"]." ".$row["msg"] . " " . $row["time"]."</div>";
            echo "<p><p>";
        }

    }

    function updateTiendas($con) {

        $query = "SELECT * FROM `tienda`";

        if (!$tiendas = $con->query($query)) {
            printf("Error: %s\n", $con->error);
        }

        while($row = mysqli_fetch_array($tiendas)) {
        
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


                        while($row = mysqli_fetch_array($stock)) {
                
                            echo "<div>" . $row["idproducto"].": ".$row["cantidad"]. "</div>";
                            
                        }
                        
                    echo "</div>";

            echo "</div>";
                
        }
    }




?>