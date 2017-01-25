var webSocket = WS.connect("ws://127.0.0.1:8080");

webSocket.on("socket/connect", function(session){
    console.log("Successfully Connected !");
});

webSocket.on("socket/disconnect", function(error){
    console.log("Disconnected for " + error.reason + " with code " + error.code);
});