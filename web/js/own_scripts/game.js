function proposalSubmitted() {

    $.ajax({

        type: "POST",
        url: "{{path('yourpath-means header name in routing.yml')}}",
        cache: "false",
        dataType: "html",
        success: function (result) {
            $("div#box").append(result);
        }
    })
}