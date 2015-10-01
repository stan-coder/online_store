$(document).ready(function(){

    $('form').on('submit', function(e){
        var password = $('#password').val().trim();
        var span = $('span.error');


        span.text('');
        if (password.length < 5) {
            span.eq(1).text('Password must be greater then 4');
            e.preventDefault();
        }
        if (!/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/.test($('#email').val().trim())) {
            span.eq(0).text('Email is not correct');
            e.preventDefault();
        }
        if (!e.isDefaultPrevented()) {
            $('#password').removeAttr('name');
            var newPsw = document.createElement('input');
            newPsw.type = 'hidden';
            newPsw.setAttribute('name', 'password');
            newPsw.value = sha512(password);
            $('form').append(newPsw);
        }
    });
});
