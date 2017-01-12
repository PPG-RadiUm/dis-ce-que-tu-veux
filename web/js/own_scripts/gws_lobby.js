var webSocket = WS.connect("ws://127.0.0.1:8080");

webSocket.on("socket/connect", function(session){
    //session is an Autobahn JS WAMP session.

    //alert("Hello");
    console.log("Successfully Connected!");

    session.publish("dcqtv/lobby/"+document.getElementById("lobby_id"), document.getElementById("lobby_player_type")+"_"+document.getElementById("lobby_player_pseudo"));

    session.subscribe("dcqtv/lobby/"+document.getElementById("lobby_id"), function(uri, payload){
        var data = payload.msg.split("_");

        if(data[0] == "participant"){

            console.log("Nouveau participant : ", data[1].pseudo);
            var li = document.createElement("li");
            li.innerHTML = data[1].pseudo;
            document.getElementById("lobby_participant_list").appendChild(li);

        } else if(data[0] == "audience"){

            console.log("Nouveau membre de l'audience : "+ data[1].pseudo);
            var li = document.createElement("li");
            li.innerHTML = data[1].pseudo;
            document.getElementById("lobby_audience_list").appendChild(li);
        }
    });
});

webSocket.on("socket/disconnect", function(error){
    //error provides us with some insight into the disconnection: error.reason and error.code

    //alert("Good Bye");
    console.log("Disconnected for " + error.reason + " with code " + error.code);
});

