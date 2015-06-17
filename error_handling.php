<?php

$error_messages = array(
    // error messages
    'ERROR_USERNAME_SELECT' => 'Username already in use',
    'ERROR_USER_INSERT' => 'Error in inserting the user, try again later',

    // success messages
    'SUCCESS_USER_INSERT' => 'User successfully inserted'
);

// TODO -> create css for errors
$type_messages = array(
    'ERROR' => 'alert-danger',
    'SUCCESS' => 'alert-success'
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

// class to manage error messages -> TODO
class ErrorClass
{
    // const array available in PHP 5.6
}

?>