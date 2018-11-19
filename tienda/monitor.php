<!DOCTYPE <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="3">

    <style>
            .flex {
                display:flex;
                flex-direction:row;
                flex-wrap: wrap;
            }

            .flexitem {
                margin:5px;
                padding:5px;
                border-style: solid;
                border-color: grey;
            }

            #log {
                width:50%;
            }
    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <script>
        function requestTienda() {
            $.ajax({
                method: "POST",
                url: "initservice.php",
                data:{number:document.getElementById("number").value}
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

    <div class="fluid-container">
    <div id="menu">
        
        <input type="submit" value="Request Tienda" onclick="requestTienda()">
        <input type="number" value=3 name="number" id="number">
    
        
    </div>

    <div id="info" class="row">

        <div class="col-8">
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
        </div>

        <div id="log" class="col-4">
            
            <?php          
            $msgs = "SELECT * FROM `errores` ORDER BY time DESC";
            $error = $con->query($msgs);

            while($row = mysqli_fetch_array($error)):?>
                    
                <div><?php echo $row["tienda"]." ".$row["msg"] . " " . $row["time"];?></div>
                <p><p>
            <?php endwhile;?>

        </div>

    </div>
    
    </div>
    
</body>
</html>

