var time = 20;
var player_id = 0;

$( window ).on( "load", function() {
    // Vérifie qu'il existe un élément dont l'id est game_timer, si c'est le cas, démarre le timer
    if ($('#game_timer').length > 0) {
        updateTimer();
    }
});

function validate(form_id) {
    // On affiche les participants qui ont fait les propositions
    document.getElementById("player1_pseudo").style.visibility = "visible";
    document.getElementById("player2_pseudo").style.visibility = "visible";


    //$("#animate").css("display", "block");
    $("#animate").css("visibility", "visible");
    var pos = 0;

    // On invoque la fonction frame() toutes les 2ms (500 FPS)
    var id = setInterval(frame, 2);
    function frame() {

        // Quand le score est positionné à 50%, on arrête et on change de page
        if (pos == 50) {
            if(form_id == 'proposition1_form') {
                $("#player1_pseudo").css("font-size", "20px");
            } else {
                $("#player2_pseudo").css("font-size", "20px");
            }

            clearInterval(id);
            document.getElementById(form_id).submit();
        } else {
            pos = pos + 0.5;

            // Si le joueur a cliqué sur la proposition de gauche, les points vont vers la gauche, sinon vers la droite
            if(form_id == 'proposition1_form') {
                $("#score").css("margin-right", pos + '%');
            } else {
                $("#score").css("margin-left", pos + '%');
            }
        }
    }
}

function updateTimer() {

    $('#game_timer').html(time+" s");
    time--;

    if(time >= 0) {

        // Si on est dans la vue jury de la phase de jeu
        if($('#div_table_participants_game_stage').length > 0){

            if(player_id < 7) {
                if (time <= 10 && time % 2 == 0) {

                    $("#player_" + player_id).append("<img src=\"{{ asset('img/participant_validate.svg') }}\" alt=\"ok\" height=\"26px\" width=\"26px\" />");
                    player_id++;

                } else if (time <= 10 && time % 3 == 0) {
                    $("#player_" + player_id).append("<img src=\"{{ asset('img/participant_validate.svg') }}\" alt=\"ok\" height=\"26px\" width=\"26px\" />");
                    player_id++;

                    $("#player_" + player_id).append("<img src=\"{{ asset('img/participant_validate.svg') }}\" alt=\"ok\" height=\"26px\" width=\"26px\" />");
                    player_id++;
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