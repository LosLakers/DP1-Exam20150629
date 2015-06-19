/*
* Javascript for the validation of the registration form, client side
* */

$(document).ready(function() {
    // event performed when the form is submitted
    $('form[name="registrationForm"]').submit(function(event) {
        var pass = $('#password').val();
        var conf_pass = $('#conf_password').val();

        if (pass !== conf_pass) {
            event.preventDefault();
            // control and notification of password error
        }
    });

    // event performed when the password is inserted
    $('#password').change(function() {
        passwordCheck();
    });

    $('#conf_password').change(function() {
        passwordCheck();
    });
});

function passwordCheck() {
    var pass = $('#password');
    var conf_pass = $('#conf_password');
    if (pass.val() !== conf_pass.val()) {
        var div = conf_pass.parent();
        div.addClass('has-error');
        div.removeClass('has-success');
        var text = "<div id='error_message'><b>Password is wrong</b></div>"
        div.append(text);
    } else {
        var div = conf_pass.parent();
        div.addClass('has-success');
        div.removeClass('has-error');
        if ($('#error_message') != null) {
            $('#error_message').remove();
        }
    }
}