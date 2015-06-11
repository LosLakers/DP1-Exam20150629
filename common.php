<?php
// check if a user is logged in
function is_loggedin() {
    if (!isset($_SESSION['login'])) {
        return false;
    } else {
        // TODO -> improve validity of logged in
        return true;
    }
}

function session_expired() {
    $session_duration = 2 * 60; // 2 minutes
    $current_time = time();
    if (isset($_SESSION['loggedtime']) && ($current_time - $_SESSION['loggedtime']) > $session_duration) {
        session_destroy();
        session_start();
    }
}

// login management
function login($conn) {
    // TODO -> check if the connection is at a db
    if ($conn != null) {
        $user = isset($_POST['username']) ? $_POST['username'] : '';
        $pass = isset($_POST['password']) ? $_POST['password'] : '';

        if ($user != '' && $pass != '') {
            // protection against SQL injection -> TODO as to be improved??
            $user = stripslashes($user);
            $pass = stripslashes($pass);
            $user = mysql_real_escape_string($user);
            $pass = mysql_real_escape_string($pass);

            // to change with an array
            $where = "username='" . $user . "' AND password='" . $pass . "'";

            $query = query('*', 'user', $where);
            if ($query != null) {
                $res = mysqli_query($conn, $query);
                if ($res != false) {
                    $count = mysqli_num_rows($res);
                    if ($count == 1) {
                        $_SESSION['username'] = $user;
                        $_SESSION['password'] = $pass;
                        // session is valid only for 2 minutes -> TODO doesn't work
                        $_SESSION['loggedtime'] = time();

                        $_SESSION['login'] = true;
                    } else {
                        // TODO -> do something??
                    }
                }

                mysqli_free_result($res);
            }
        }
    }
}

// logout management
function logout() {
    // TODO
}

// insert bootstrap, jquery and other standard stuff
function insert_header() {
    echo "<link rel='stylesheet' href='css/bootstrap.min.css'>";
    echo "<script type='text/javascript' src='javascript/jquery-2.1.4.min.js'></script>";
    echo "<script type='text/javascript' src='javascript/bootstrap.min.js'></script>";
    echo "<style type='text/css'>
        body {
            padding-top: 50px;
            padding-bottom: 20px;
        }
    </style>";
}

// establish a connection to the db
function dbconnection() {
    $user = "root";
    $pass = "";
    $db = "dp1_exam";

    $conn = new mysqli("localhost", $user, $pass, $db);
    if (mysqli_connect_errno()) {
        return null;
    } else {
        return $conn;
    }
}

// prepare a query on a single table
// select must be a single string or a list of strings
// where must be a single string or a list of strings in the form 'KEY = VALUE'
// TODO -> check validity of where clause
// TODO -> add possibility of order the results
function query($select, $from, $where) {
    if (is_array($from)) {
        return null;
    } else {

        $query = "SELECT ";
        // insert select elements
        if (is_array($select)) {
            $length = count($select);
            for ($i = 0; $i < $length; $i++) {
                $query .= $select[$i];
                if ($i != ($length - 1)) {
                    $query .= ", ";
                }
            }
        } else {
            $query .= $select;
        }

        // insert from
        $query .= " FROM ".$from;

        // insert where clause
        if ($where != null) {
            $query .= " WHERE ";
            if (is_array($where)) {
                $length = count($where);
                for ($i = 0; $i < $length; $i++) {
                    $query .= $where[$i];
                    if ($i != ($length - 1)) {
                        $query .= " AND ";
                    }
                }
            } else {
                $query .= $where;
            }
        }

        return $query;
    }
}
?>