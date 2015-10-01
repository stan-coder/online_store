$(document).ready(function(){

    $('form').on('submit', function(e){
        var password = $('#password').val().trim();
        var repeatPassword = $('#repeat_password').val().trim();

        var valid = {
            'Field "Password" must be greater then 4' : [password.length < 5, 1],
            'Field "Password" and "Repeat password" is not matched' : [password != repeatPassword, 1],
            'Email is not correct' : [!/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/.test($('#email').val().trim()), 0]
        };
        $('span.error').text('');
        for (var item in valid) {
            if (valid[item][0]) {
                $('span.error').eq(valid[item][1]).text(item);
                e.preventDefault();
            }
        }
        if (!e.isDefaultPrevented()) {
            var randomString = Math.random().toString() + (new Date().getTime()).toString() + $('input[type=hidden][name=CSRFHash]').val();
            var salt = sha512(randomString).toUpperCase().substr(0, 50);
            var preparedPassword = sha512(sha512(password) + salt)+'|'+salt;

            $('#password').removeAttr('name');
            var newPsw = document.createElement('input');
            newPsw.type = 'hidden';
            newPsw.setAttribute('name', 'password');
            newPsw.value = preparedPassword;
            $('form').append(newPsw);
        }
    });

});