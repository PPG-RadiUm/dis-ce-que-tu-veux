var webSocket = WS.connect("ws://127.0.0.1:8080");

webSocket.on("socket/connect", function(session){
    console.log("Successfully Connected to Saloon!");

    //the callback function in "subscribe" is called everytime an event is published in that channel.
    session.subscribe("dcqtv/saloon", function(uri, payload){
        console.log("Received message", payload.msg);

        var table = document.getElementById("saloonTable");
        var message = JSON.parse(payload.msg);
        if(message.creation){
            // Create an empty <tr> element and add it to the last position of the table:
            var row = table.insertRow(table.rows.length);

            // Insert new cells (<td> elements) at the 1st and 2nd position of the "new" <tr> element:
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);

            // Add some text to the new cells:
            cell1.innerHTML = "Partie de " + message.owner;
            cell2.innerHTML = message.actualPlayersNumber + "/" + message.maxPlayersNumber;
            cell3.innerHTML = message.spectators;
            cell4.innerHTML = message.gameState;
        }else{
            var rowIndex = null;
            // Get la bonne ligne : rowIndex avec un id ? un code de room ?
            var theRow = table.rows[rowIndex];
            theRow[1] = message.actualPlayersNumber + "/" + message.maxPlayersNumber;
            theRow[2] = message.spectators;
            theRow[3] = message.gameState;
        }

    });

    var toSendJson = {
        "owner": "moi",
        "creation": true,
        "actualPlayersNumber": 3,
        "maxPlayersNumber": 8,
        "spectators": 42,
        "gameState": "Pause"
    };

    session.publish("dcqtv/saloon", {msg: toSendJson});

    setTimeout(function(){
        session.publish("dcqtv/saloon", {msg: toSendJson});
    }, 5000);
});

webSocket.on("socket/disconnect", function(error){
    //error provides us with some insight into the disconnection: error.reason and error.code

    //alert("Good Bye");
    console.log("Disconnected for " + error.reason + " with code " + error.code);
});

