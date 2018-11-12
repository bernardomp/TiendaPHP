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

            .flexitem {
                margin:5px;
                padding:5px;
                border-style: solid;
                border-color: grey;
            }
    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        function requestTienda() {
            $.ajax({
                method: "POST",
                url: "initservice.php",
            })
            .done(function(a) {
                console.log("Correcto: Solicitar tienda");
            });
        }

    </script>
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
                
                <div class="flexitem">
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

    <input type="button" value="Request Tienda" onclick="requestTienda()">
    
</body>
</html>

