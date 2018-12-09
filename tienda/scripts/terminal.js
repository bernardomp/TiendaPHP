var logIndex = 0;

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
        data:{method:"updateLog",index:logIndex}
    })
    .done(function(data) {
        
        var tabledata = JSON.parse(data);

        if(tabledata.length > 0) {
        
            createTable(tabledata);
            logIndex = tabledata[tabledata.length-1][0];
        }
    });
}

function createTable(data) {
    var table = document.getElementById("logtable");

    for(var i = 0; i< data.length;i++) {

        
        var row = table.insertRow(0);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1)

        cell1.innerHTML = data[i][1];
        cell2.innerHTML = data[i][2];
        
        //cell2.setAttribute("nowrap","nowrap");
    }
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
