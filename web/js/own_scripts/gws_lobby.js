var webSocket = WS.connect("ws://127.0.0.1:8080");

webSocket.on("socket/connect", function(session){
    //session is an Autobahn JS WAMP session.
    console.log("Successfully Connected!");

    console.log("Your joining type is : "+document.getElementById("joiningType").value);

    // Quand on créé la salon, cette variable = lobby_creation, lobby_join sinon
    var joiningType = (document.getElementById("joiningType").value == "creation")
        ? "lobby_creation"
        : "lobby_join";

    // Variable qui sera envoyée au channel dcqtv/lobby/{id} pour mettre à jour la vue de ceux qui sont dans le salon
    var toLobby = {
        "lobby_player_role": document.getElementById("lobby_player_role").value,
        "player_pseudo": document.getElementById("lobby_player_pseudo").value,
    };
    toLobby[joiningType] = true;

    // Variable qui sera envoyée au channel dcqtv/saloon pour mettre à jour la liste des salons
    var toSaloonList = {
        "host": (document.getElementById("lobby_host").innerHTML.split(" "))[1],
        "lobbyId": parseInt(document.getElementById("lobby_id").value),
        "actualPlayersNumber": $('#lobby_participants_list').children('li').length,
        "maxPlayersNumber": parseInt(document.getElementById("lobby_max_player_number").value),
        "spectators": $('#lobby_audience_list').children('li').length,
        "gameState": "Création de la partie"
    };
    toSaloonList[joiningType] = true;

    console.log("You publish to lobby and saloon")
    session.publish("dcqtv/lobby/" + document.getElementById("lobby_id").value, toLobby);
    session.publish("dcqtv/saloon", toSaloonList);

    console.log("You have been subscribed to dcqtv/lobby/"+document.getElementById("lobby_id").value);
    session.subscribe("dcqtv/lobby/" + document.getElementById("lobby_id").value, function(uri, payload){
        console.log("Received a publish to dcqtv/lobby/"+document.getElementById("lobby_id").value+" : "+payload.msg);
        //var data = payload.msg.split("_");
        var data = payload.msg;

        console.log(data);

        if(data.lobby_join){
            if(data.lobby_player_role == "participant"){

                console.log("Nouveau participant : ", data.player_pseudo);
                var li = document.createElement("li");
                li.innerHTML = data.player_pseudo;
                document.getElementById("lobby_participants_list").appendChild(li);

            } else if(data.lobby_player_role == "audience"){

                console.log("Nouveau membre de l'audience : "+ data.player_pseudo);
                var li = document.createElement("li");
                li.innerHTML = data.player_pseudo;
                document.getElementById("lobby_audience_list").appendChild(li);
            }
        }
    });
});

webSocket.on("socket/disconnect", function(error){
    //error provides us with some insight into the disconnection: error.reason and error.code

    //alert("Good Bye");
    console.log("Disconnected for " + error.reason + " with code " + error.code);
});

