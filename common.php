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

// login management
function login() {
    // TODO
}

// logout management
function logout() {
    // TODO
}

// insert bootstrap, jquery and other standard stuff
function insert_header() {
    echo "<link rel='stylesheet' href='bootstrap.min.css'>";
    echo "<script type='text/javascript' src='jquery-2.1.4.min.js'></script>";
    echo "<script type='text/javascript' src='bootstrap.min.js'></script>";
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