<?php
include 'common_variables.php';

// used to check the validity of the user
// confront a cookie set in the client with a data in the $_SESSION
// used to avoid exchanging session cookie and guess another user
function check_user_validity() {
    if (isset($_COOKIE['user_cookie']) && isset($_SESSION['user_cookie'])) {
        if (strcmp($_COOKIE['user_cookie'], $_SESSION['user_cookie']) != 0) {
            session_destroy();
            die();
        }
    }
}

// function to check if cookies are enabled or not
function cookie_check() {
    if (!isset($_SESSION['cookieEn']) && !isset($_COOKIE['cookie_enabled'])) {
        $_SESSION['cookieEn'] = false;
        redirect_to("error_page.php");
    }
}

// to check if the session is expired or not
function session_expired()
{
    $session_duration = 2 * 60;
    if (isset($_SESSION['logged_time'])) {
        $current_time = time();
        if (($current_time - $_SESSION['logged_time']) > $session_duration) {
            session_destroy();
            session_start();
            return true;
        } else {
            // update session time
            $_SESSION['logged_time'] = time();
            return false;
        }
    } else {
        return false;
    }
}

// login management
function login($conn, $username, $password)
{
    if ($username != '' && $password != '') {
        // protection against SQL injection
        $username = sql_clean_up($username);
        $password = sql_clean_up($password);

        $where = "username='" . $username . "' AND password='" . $password . "'";

        $query = sql_query_select('*', 'user', $where, null);
        if ($query != null) {
            $res = mysqli_query($conn, $query);
            if ($res != false && mysqli_num_rows($res) == 1) {
                $_SESSION['username'] = $username;
                $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
                $_SESSION['name'] = $row['name'];
                $_SESSION['surname'] = $row['surname'];
                // save session time of login
                $_SESSION['logged_time'] = time();
                mysqli_free_result($res);
                return true;
            } else {
                mysqli_free_result($res);
                return false;
            }
        }
    }
}

// logout management
function logout()
{
    session_destroy();
    $redirect_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    redirect_to($redirect_url);
}

// insert jquery and css
function insert_head()
{
    echo "<script type='text/javascript' src='javascript/jquery-2.1.4.min.js'></script>";
    echo "<link href='css/css_file.css' rel='stylesheet' type='text/css'>";
}

function redirect_to($page) {
    header("Location: $page");
    die();
}

// error page redirect
function error_page_redirect($message) {
    $_SESSION['error_message'] = $message;
    $redirect = "error_page.php";
    header("Location: $redirect");
    die();
}

// establish a connection to the db
function dbconnection()
{
    global $HOST;
    global $USER;
    global $PASSWORD;
    global $DATABASE;

    $conn = new mysqli($HOST, $USER, $PASSWORD, $DATABASE);
    return $conn;
}

// prepare a query on a single table
// select must be a single string
// where must be a single string in the format 'KEY_1='VALUE_1' AND KEY_2='VALUE_2' ...'
// order must be a single string in the format 'KEY_1, KEY_2, ... ASC/DESC'
function sql_query_select($select, $from, $where, $order)
{
    if ($select != null && $from != null) {
        $query = "SELECT ";

        // insert select elements
        $query .= $select;

        // insert from
        $query .= " FROM " . $from;

        // insert where clause
        if ($where != null) {
            $query .= " WHERE ";
            $query .= $where;
        }

        if ($order != null) {
            $query .= " ORDER BY ";
            $query .= $order;
        }

        return $query;
    } else {
        return null;
    }
}

// function to prepare a query to insert a value into a table
function sql_query_insert($insert, $values)
{
    if ($insert != null && $values != null) {
        $query = "INSERT INTO ";
        $query .= $insert;
        $query .= " VALUES ";
        $query .= $values;
        return $query;
    } else {
        return null;
    }
}

// function to prepare a query to update a row into a table
function sql_query_update($from, $values, $where)
{
    if ($from != null && $values != null) {
        $query = "UPDATE ";
        $query .= $from;
        $query .= " SET ";
        $query .= $values;
        if ($where != null) {
            $query .= " WHERE ";
            $query .= $where;
        }
        return $query;
    } else {
        return null;
    }
}

// function to prepare a query to delete a row into a table
function sql_query_delete($from, $where)
{
    if ($from != null && $where != null) {
        $query = "DELETE FROM ";
        $query .= $from;
        $query .= " WHERE ";
        $query .= $where;
        return $query;
    } else {
        return false;
    }
}

// to call for avoiding sql injection
function sql_clean_up($variable)
{
    $variable = strip_tags($variable);
    $variable = htmlentities($variable);
    $variable = stripslashes($variable);
    $variable = mysql_real_escape_string($variable);

    return $variable;
}

?>