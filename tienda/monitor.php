<!DOCTYPE <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
            .flex {
                display:flex;
                flex-direction:row;
            }
    </style>
</head>
<body>
    <?php
        $query = "SELECT * FROM `tienda`";
        $con = new mysqli("localhost", "root", "toor", "multiagentes");

        if (!$tiendas = $con->query($query)) {
            printf("Error: %s\n", $con->error);
        }

    ?>

    <div class="flex">

        <?php while($row = mysqli_fetch_array($tiendas)): ?>
                
                <div>
                    <div>Tienda: <?php echo $row["id"]?></div>
                    <div>
                        <?php
                            if ($row["estado"] == 0) {
                                echo "Estado: Cerrado"; 
                            }
            
                            else {
                                echo "Estado: Abierto"; 
                            }
                        ?>
                    </div>

                    <div>
                        <?php
                        $idtienda = $row["id"];
                        $stocks = "SELECT * FROM `stock` WHERE idtienda='$idtienda'";
                        $stock = $con->query($stocks);

    
                        while($row = mysqli_fetch_array($stock)):?>
                            
                            <div><?php echo $row["idproducto"].": ".$row["cantidad"];?></div>
                        
                        <?php endwhile;?>
    
                            
                    </div>
                </div>

    

        <?php endwhile;?>


    </div>
    
</body>
</html>

