$ (document).ready(function (){

    $(".nick-input").blur(function (){

        var nick = this.value;
        var boton = $(".register-input");

        $.ajax({
            url: URL + '/nick-test',
            data: {nick: nick},
            type: 'POST',
            success: function (response) {
                if (response == "used" || !nick){

                    $(".nick-input").css("border", "1px solid red");
                    $(".register-input").attr('disabled', 'disabled');
                } else {

                    $(".nick-input").css("border", "1px solid green");
                    $(".register-input").removeAttr('disabled', 'disabled');

                }
            }

        });
    });

});