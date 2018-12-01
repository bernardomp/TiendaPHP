<?php
    //=====================================================================================

            // AUTOR: 
            // NOMBRE: test.php
            // DESCRIPCIÓN: Este fichero permite tanto al monitor como al cliente verificar si es posible establecer
            //              la comunicación con nuestra tienda. Este archivo es fudamental para verifIcar que los clientes
            //              que utilicen el sistema CORS 
            // ARGUMENTOS: --
            // FUENTE: --
            // SALIDA: Devuelve el string "RECIBIDO, CON CARIÑO"

     //====================================================================================         


    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

    echo "RECIBIDO, CON CARIÑO";
?>