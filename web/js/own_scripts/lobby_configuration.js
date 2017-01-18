function createLobby(){

    // On informe le serveur de la création du nouveau salon
    $.ajax({
        method: "POST",
        url: "/lobby",
        data: {capParticipants: document.getElementById("capParticipants"), type: document.getElementById("type")}
    });
}
