var webSocket = WS.connect("ws://127.0.0.1:8080");

webSocket.on("socket/connect", function(session){
    console.log("Successfully Connected to Saloon!");

    //the callback function in "subscribe" is called everytime an event is published in that channel.
    session.subscribe("dcqtv/saloon", function(uri, payload){
        console.log("Received message", payload.msg);
        console.log(payload);

        var table = document.getElementById("saloonTable");
        //var message = JSON.parse(payload);
        var message = payload.msg;
        if(message){
            if(message.lobby_creation){
                // Create an empty <tr> element and add it to the last position of the table:
                var row = table.insertRow(table.rows.length);

                // Insert new cells (<td> elements) at the 1st and 2nd position of the "new" <tr> element:
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);

                // Add some text to the new cells:
                cell1.innerHTML = "Partie de " + message.host;
                cell2.innerHTML = message.actualPlayersNumber + "/" + message.maxPlayersNumber;
                cell3.innerHTML = message.spectators;
                cell4.innerHTML = message.gameState;
                cell5.innerHTML = "<form action='/lobby' method='post'>" +
                        "<input type='hidden' name='lobby_id' value='" + message.lobbyId + "'/>" +
                        "<input type='hidden' name='lobby_join' value='true'/>" +
                        "<input type='hidden' name='lobby_player_role' value='participant'/>" +
                        "<input type='hidden' name='player_pseudo' value='" + document.getElementById('player_pseudo').value + "'/>" +
                        "<input type='submit' name='lobbyJoin'/>"
                    "</form>";
                cell6.innerHTML = "<form action='/lobby' method='post'>" +
                    "<input type='hidden' name='lobby_id' value='" + message.lobbyId + "'/>" +
                    "<input type='hidden' name='lobby_join' value='true'/>" +
                    "<input type='hidden' name='lobby_player_role' value='jury'/>" +
                    "<input type='hidden' name='player_pseudo' value='" + document.getElementById('player_pseudo').value + "'/>" +
                    "<input type='submit' name='lobby_join'/>"
                "</form>";
            }else if(message.lobby_join){
                var rowIndex = null;
                // Get la bonne ligne : rowIndex avec un id ? un code de room ?
                var theRow = table.rows[rowIndex];
                theRow[1] = message.actualPlayersNumber + "/" + message.maxPlayersNumber;
                theRow[2] = message.spectators;
                theRow[3] = message.gameState;
            }
        }

    });
});

webSocket.on("socket/disconnect", function(error){
    //error provides us with some insight into the disconnection: error.reason and error.code

    //alert("Good Bye");
    console.log("Disconnected for " + error.reason + " with code " + error.code);
});