$(document).ready(function () {
    // when a checkbox is selected, is added to the form for deleting a reservation
    // when is deselected, is removed from the form
    $('input[type=checkbox]').click(function () {
        var checkboxval = $(this).val();

        if ($(this).is(':checked')) {
            // action if the checkbox is selected
            var input = '<input type=hidden id=' + checkboxval + ' name=reservation[] value=' + checkboxval + '>';
            $('#deleteReservations').append(input);

        } else {
            // action if the checkbox is deselected
            var input = '#' + checkboxval;
            $(input).remove();
        }
    });
});