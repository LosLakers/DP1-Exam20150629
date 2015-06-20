<?php

$error_messages = array(
    // error messages
    'ERROR_USERNAME_SELECT' => 'Username already in use',
    'ERROR_USER_INSERT' => 'Error in inserting the user, try again later',
    'ERROR LOGIN' => 'Username and/or password are wrong, try again',
    'ERROR CREATE RESERVATION' => 'Error in performing a reservation',
    'ERROR DELETE RESERVATION' => 'An error occurred in deleting a reservation',
    'ERROR RESERVATION PRESENT' => 'You already reserved places for this activity',
    'ERROR RESERVATION TOO SPACES' => 'You are trying to reserve more places than the available ones',
    'ERROR JAVASCRIPT DISABLED' => 'This page needs Javascript to work properly',

    // success messages
    'SUCCESS_USER_INSERT' => 'User successfully inserted',
    'SUCCESS CREATE RESERVATION' => 'A reservation has been successfully created',
    'SUCCESS_DELETE_RESERVATION' => 'One or more reservations have been successfully deleted'
);

$type_messages = array(
    'ERROR' => 'has-error',
    'SUCCESS' => 'has-success'
);

function get_message_type($error) {
    global $type_messages;
    if (strpos($error, 'SUCCESS') !== false) {
        return $type_messages['SUCCESS'];
    } else if (strpos($error, 'ERROR') !== false) {
        return $type_messages['ERROR'];
    } else {
        return null;
    }
}

function get_message($error) {
    global $error_messages;
    if (isset($error_messages[$error])) {
        return $error_messages[$error];
    } else {
        return null;
    }
}