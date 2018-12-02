<!DOCTYPE html>
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
                flex-wrap: wrap;
                align-items: flex-start;
                align-content: flex-start;
            }

            .formElement {
                text-align:center;
                font-size:1rem;
                color: #495057;
                background-color: #fff;
                background-clip: padding-box;
                border: 1px solid #ced4da;
                border-radius: .25rem;
                transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
            }

            .flexitem {
                margin:5px;
                padding:5px;
                border-style: solid;
                border-color: grey;
            }

            #log {
                overflow:auto;
            }

            #menu {
                margin:2%;
            }



            .gridcontainer {
                display: grid;
                grid-template-columns: 66% 34%;
                justify-items:center;
            }
    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <script>

        setInterval(function(){
            updateTiendas();
            updateLog();
            
        }, 3000);

        function requestTiendas() {
            $.ajax({
                method: "POST",
                url: "initservice.php",
                data:{number:document.getElementById("number").value}
            })
            .done(function(a) {
                console.log("Correcto: Solicitar tienda");
            });
        }

        function updateTiendas() {
            $.ajax({
                method: "POST",
                url: "funciones.php",
                data:{method:"updateTiendas"}
            })
            .done(function(data) {
                $('#tiendas').html(data);
            });
        }

        function updateLog() {
            $.ajax({
                method: "POST",
                url: "funciones.php",
                data:{method:"updateLog"}
            })
            .done(function(data) {
                $('#log').html(data);
            });
        }

        function borrarBBDD() {
            $.ajax({
                method: "POST",
                url: "funciones.php",
                data:{method:"borrarBBDD"}
            })
            .done(function(data) {
                $('#log').html(data);
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

        
    <div id="info" class="gridcontainer">

            <div id="menu">

                <div style="text-align:center">
                    <input type="button" value="Request Tienda" onclick="requestTiendas()" class="btn btn-secondary">
                    <input type="number" value=3 name="number" id="number" class="formElement">
                    <input type="button" value="Borrar Tiendas" onclick="borrarBBDD()" class="btn btn-danger">

                </div>
        
               
                <div class="flex" id="tiendas"></div>

            </div>

            <div id="log"></div>

    </div>

    
</body>
</html>
