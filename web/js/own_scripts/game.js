var time = 13;
var playersArray = ["player_1", "player_2", "player_3", "player_4", "player_5", "player_6", "player_7"];
var playersArraySpect = ["player_0", "player_1", "player_2", "player_3", "player_4", "player_5", "player_6", "player_7"];

var playersScore = null;
var tempPlayer = null;

$( window ).on( "load", function() {
    shuffle(playersArray);
    shuffle(playersArraySpect);
    console.log(playersArray);
    console.log(playersArraySpect);

    if($("#scores_1").val() != undefined)
        playersScore = JSON.parse($("#scores_1").val());
    
    // Vérifie qu'il existe un élément dont l'id est game_timer, si c'est le cas, démarre le timer
    if ($('#game_timer').length > 0) {
        updateTimer();
    }
    if($("#scores").length > 0) {
        var scoresss = JSON.parse($("#scores").val());
        var scoresObj = [];

        for(var i = 0; i < scoresss.length; i++){
            var temp = scoresss[i].split("_");
            scoresObj[i] = {};
            scoresObj[i].pseudo = temp[0];
            scoresObj[i].score = temp[1];
        }

        var boolCond = true;
        var cpt = 1;

        while(boolCond){
            var max = 0;
            for(i = 0; i < scoresObj.length; i++){
                if(scoresObj[i].score >= scoresObj[max].score){
                    //console.log(scoresObj[i]);
                    max = i;
                }
            }
            document.getElementById("Cpseudo_" + cpt).innerHTML = scoresObj[max].pseudo;
            document.getElementById("Cscore_" + cpt).innerHTML = scoresObj[max].score;
            scoresObj[max].score = 0;
            if(cpt == 8){
                boolCond = false;
            }
            cpt ++;
        }
    }
});

function validate(form_id) {
    // On affiche les participants qui ont fait les propositions
    document.getElementById("player1_pseudo").style.visibility = "visible";
    document.getElementById("player2_pseudo").style.visibility = "visible";

    if(form_id == 'proposition1_form') {
        $("#player_0").addClass("vote_proposition1");
    } else {
        $("#player_0").addClass("vote_proposition2");
    }

    playersArray = ["player_1", "player_2", "player_3", "player_4", "player_5", "player_6", "player_7"];
    shuffle(playersArray);

    var i = 0;
    var scoreP1 = 0;
    var scoreP2 = 0;

    while(i <= 7){
        if(Math.random() >= 0.5){
            $("#"+playersArray[i]).addClass("vote_proposition1");
            scoreP1 += 1;
        }else{
            $("#"+playersArray[i]).addClass("vote_proposition2");
            scoreP2 += 1;
        }
        i++;
    }

    var votePublicFor1 = Math.round(15 * Math.random());
    scoreP1 = Math.round(((scoreP1 + votePublicFor1)/23)*1000);
    playersScore[playersScore.indexOf(document.getElementById("player1_pseudo").innerHTML)] += "_" + scoreP1;
    //var votePublicFor2 = 15 - votePublicFor1;
    scoreP2 = 1000 - scoreP1;
    playersScore[playersScore.indexOf(document.getElementById("player2_pseudo").innerHTML)] += "_" + scoreP2;

    $("#td_audience").css("background", "linear-gradient(to right, #3498db "+ ((votePublicFor1/15) * 100) +"%, #e67e22 0%)");
    $("#td_audience").css("color", "black");

    document.getElementById("scores_1").value = JSON.stringify(playersScore);
    //$("#scores_1").val(JSON.stringify(playersScore));
    document.getElementById("scores_2").value = JSON.stringify(playersScore);
    //document.getElementsById("scores_2").value = JSON.stringify(playersScore);
    //$("#scores_2").val(JSON.stringify(playersScore));


    //$("#animate").css("display", "block");
    $("#animate").css("visibility", "visible");
    var pos = 0;

    // On invoque la fonction frame() toutes les 2ms (500 FPS)
    var id = setInterval(frame, 2);
    function frame() {

        // Quand le score est positionné à 50%, on arrête et on change de page
        if (pos == 46) {

            /* Il faut faire grossir le pseudo de celui qui obtient le meilleur score
            if(form_id == 'proposition1_form') {
                $("#player1_pseudo").css("font-size", "20px");
            } else {
                $("#player2_pseudo").css("font-size", "20px");
            }*/

            clearInterval(id);
            document.getElementById(form_id).submit();
        } else {
            pos = pos + 0.5;

            // On déplace les scores de chaque côté
            $("#score_right").css("margin-left", pos + '%');
        }
    }
}

function updateTimer() {

    $('#game_timer').html(time+" s");
    time--;

    if(time >= 0) {

        // Si on est dans la vue jury de la phase de jeu
        if($('#div_table_participants_game_stage').length > 0){

            if(playersArray.length > 0) {
                if (time <= 10 && time % 2 == 0) {

                    tempPlayer = playersArraySpect[playersArraySpect.length-1];
                    $("#"+tempPlayer).addClass("participant_ready");
                    playersArraySpect = playersArraySpect.slice(0,-1);

                } else if (time <= 10 && time % 3 == 0) {
                    tempPlayer = playersArraySpect[playersArraySpect.length-1];
                    $("#"+tempPlayer).addClass("participant_ready");
                    playersArraySpect = playersArraySpect.slice(0,-1);

                    tempPlayer = playersArraySpect[playersArraySpect.length-1];
                    $("#"+tempPlayer).addClass("participant_ready");
                    playersArraySpect = playersArraySpect.slice(0,-1);
                }
            }
        }

        // Si on est dans la phase de vote
        if($('#div_table_participants_vote_stage').length > 0){

            if(playersArraySpect.length > 0) {
                if (time <= 10 && time % 2 == 0) {
                    tempPlayer = playersArraySpect[playersArraySpect.length-1];
                    $("#"+tempPlayer).addClass("participant_ready");
                    playersArraySpect = playersArraySpect.slice(0,-1);

                } else if (time <= 10 && time % 3 == 0) {
                    tempPlayer = playersArraySpect[playersArraySpect.length-1];
                    $("#"+tempPlayer).addClass("participant_ready");
                    playersArraySpect = playersArraySpect.slice(0,-1);

                    tempPlayer = playersArraySpect[playersArraySpect.length-1];
                    $("#"+tempPlayer).addClass("participant_ready");
                    playersArraySpect = playersArraySpect.slice(0,-1);
                }
            }
        }

        setTimeout(updateTimer, 1000);
    } else {

        if($('#proposition_form').length > 0){
            // Cas de la phase de jeu (il faut faire une proposition)
            document.getElementById('proposition_form').submit();
        } else {
            // Cas de la phase de vote
            validate('proposition1_form');
        }

    }
}

function shuffle(a) {
    var j, x, i;
    for (i = a.length; i; i--) {
        j = Math.floor(Math.random() * i);
        x = a[i - 1];
        a[i - 1] = a[j];
        a[j] = x;
    }
}

// Obsolète pour le moment
function proposalSubmitted() {

    // Envoyer $data['vote_stage']
    $.ajax({
        type: "POST",
        url: "{{path('dcqtv_game')}}",
        cache: "false",
        dataType: "html",
        data: { vote_stage: "vote_stage" },
        success: function (result) {
            $("section#game").innerHTML = result;
        }
    })
}