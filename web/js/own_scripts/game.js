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