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
        "player_pseudo": document.getElementById("lobby_player_pseudo").value
    };
    toLobby[joiningType] = true;

    // Variable qui sera envoyée au channel dcqtv/saloon pour mettre à jour la liste des salons
    var toSaloonList = {
        "host": (document.getElementById("lobby_host").innerHTML.split(" "))[1],
        "lobbyId": parseInt(document.getElementById("lobby_id").value),
        "actualPlayersNumber": $('#lobby_participants_list').find('td').length,
        "maxPlayersNumber": parseInt(document.getElementById("lobby_max_player_number").value),
        "spectators": $('#lobby_audience_list').find('td').length,
        "gameState": "Création de la partie"
    };
    toSaloonList[joiningType] = true;

    console.log("You publish to lobby and saloon");
    session.publish("dcqtv/lobby/" + document.getElementById("lobby_id").value, toLobby);
    session.publish("dcqtv/saloon", toSaloonList);

    console.log("You have been subscribed to dcqtv/lobby/"+document.getElementById("lobby_id").value);
    session.subscribe("dcqtv/lobby/" + document.getElementById("lobby_id").value, function(uri, payload){
        console.log("Received a publish to dcqtv/lobby/"+document.getElementById("lobby_id").value+" : "+payload.msg);
        var data = payload.msg;

        console.log(data);

        if(data.lobby_join){
            var boolean = false;

            if(data.lobby_player_role == "participant"){
                $('#lobby_participants_list').find('td').each(function () {
                    boolean = boolean && ($(this).val() == document.getElementById("lobby_player_id").value);
                })

                if(!boolean){
                    var table = document.getElementById("lobby_participants_list");

                    // Mise à jour du nombre de participants
                    $('#participants_th').html("Participants (" +
                        table.rows.length + "/" + document.getElementById("lobby_max_player_number").value + ")");
                    console.log("Nouveau participant : ", data.player_pseudo);

                    /**
                     * Affichage du bouton de lancement de la partie pour le propriétaire de la partie et quand
                     * le nombre de participants max est atteint.
                     */
                    if(document.getElementById("lobby_host_id").value
                        == document.getElementById("lobby_player_id").value
                        && table.rows.length == document.getElementById("lobby_max_player_number").value){
                        $("#createButton").show();
                        // TODO Amélioration, passer en disabled mais visible seulement que pour le créateur.
                        //$("#createButton").removeAttribute("disable");
                        $("#createButton").attr("href", "/game");
                    }
                }
            } else if(data.lobby_player_role == "audience"){
                $('#lobby_audience_list').find('td').each(function () {
                    boolean = boolean && ($(this).val() == document.getElementById("lobby_player_id").value);
                })

                if(!boolean){
                    var table = document.getElementById("lobby_audience_list");
                    console.log("Nouveau membre de l'audience : "+ data.player_pseudo);
                }
            }

            if(!boolean){
                var row = table.insertRow(table.rows.length);
                var cell1 = row.insertCell(0);
                cell1.innerHTML = data.player_pseudo;
            }
        }
    });
});

webSocket.on("socket/disconnect", function(error){
    //error provides us with some insight into the disconnection: error.reason and error.code
    console.log("Disconnected for " + error.reason + " with code " + error.code);

    /*var toLobby = {
        "lobby_player_role": document.getElementById("lobby_player_role").value,
        "player_pseudo": document.getElementById("lobby_player_pseudo").value
    };
    toLobby[joiningType] = true;

    // Variable qui sera envoyée au channel dcqtv/saloon pour mettre à jour la liste des salons
    var toSaloonList = {
        "host": (document.getElementById("lobby_host").innerHTML.split(" "))[1],
        "lobbyId": parseInt(document.getElementById("lobby_id").value),
        "actualPlayersNumber": $('#lobby_participants_list').find('td').length,
        "maxPlayersNumber": parseInt(document.getElementById("lobby_max_player_number").value),
        "spectators": $('#lobby_audience_list').find('td').length,
        "gameState": "Création de la partie"
    };
    toSaloonList[joiningType] = true;

    console.log("You publish to lobby and saloon");
    session.publish("dcqtv/lobby/" + document.getElementById("lobby_id").value, toLobby);
    session.publish("dcqtv/saloon", toSaloonList);*/
});

