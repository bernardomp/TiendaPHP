<?php


$query = "SELECT * FROM `tienda`";
$query2 = "SELECT * FROM `stock`";

$con = new mysqli("localhost", "root", "toor", "multiagentes");
if (!$tiendas = $con->query($query)) {
    printf("Error: %s\n", $con->error);
}

while($row = mysqli_fetch_array($tiendas)) {
    
    echo "<br>Tienda: ". $row['id']; 

    if ($row["estado"] == 0) {
        echo "<br>Estado: Cerrado <br>"; 
    }

    else {
        echo "<br>Estado: Abierto <br>"; 
    }
    
    $idtienda = $row["id"];

    $query2 = "SELECT * FROM `stock` WHERE idtienda='$idtienda'";
    
    $stock = $con->query($query2);

    
    while($row = mysqli_fetch_array($stock)) {
       echo $row["idproducto"].": ";
       echo $row["cantidad"]."<br>";
    }
    
}

?>