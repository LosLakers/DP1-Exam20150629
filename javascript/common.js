$(document).ready(function () {
    $('button.error-message').click(function () {
        var div = $(this).parent();
        div.remove();
    });
});