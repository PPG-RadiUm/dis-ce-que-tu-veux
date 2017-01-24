var webSocket = WS.connect("ws://127.0.0.1:8080");

webSocket.on("socket/connect", function(session){
    //session is an Autobahn JS WAMP session.
    console.log("Successfully Connected!");

    console.log(document.getElementById("joiningType").value);

    var joiningType = (document.getElementById("joiningType").value == "creation")
        ? "lobby_creation"
        : "lobby_join";

    var toLobby = {
        "lobby_player_role": document.getElementById("lobby_player_role").value,
        "player_pseudo": document.getElementById("lobby_player_pseudo").value,
    };
    toLobby[joiningType] = true;

    var toSaloonList = {
        "host": (document.getElementById("lobby_host").innerHTML.split(" "))[1],
        "lobbyId": parseInt(document.getElementById("lobby_id").value),
        "actualPlayersNumber": $('#lobby_participants_list').children('li').length,
        "maxPlayersNumber": parseInt(document.getElementById("lobby_max_player_number").value),
        "spectators": $('#lobby_audience_list').children('li').length,
        "gameState": "Création de la partie"
    };
    toSaloonList[joiningType] = true;

    session.publish("dcqtv/lobby/" + document.getElementById("lobby_id").value, toLobby);
    session.publish("dcqtv/saloon", toSaloonList);

    session.subscribe("dcqtv/lobby/" + document.getElementById("lobby_id").value, function(uri, payload){
        console.log("Message reçu : "+payload.msg);
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

