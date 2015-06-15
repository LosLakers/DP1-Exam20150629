<?php
// check if a user is logged in
function is_loggedin($conn)
{
    if (!isset($_SESSION['login'])) {
        return false;
    } else {
        $where = "username='" . $_SESSION['username'] . "' AND password='" . $_SESSION['password'] . "'";
        $query = sql_query_select("*", "user", $where, null);
        if ($query != null) {
            $res = mysqli_query($conn, $query);
            if (mysqli_num_rows($res) == 1) {
                mysqli_free_result($res);
                return true;
            } else {
                mysqli_free_result($res);
                return false;
            }
        } else {
            return false;
        }
    }
}

function session_expired()
{
    $session_duration = 2 * 60; // 2 minutes
    $current_time = time();
    if (isset($_SESSION['loggedtime']))
        if (($current_time - $_SESSION['loggedtime']) > $session_duration) {
            session_destroy();
            session_start();
            return true;
        } else {
            // update session time
            $_SESSION['loggedtime'] = time();
            return false;
        }
    return false;
}

// login management
function login($conn)
{
    // TODO -> check if the connection is at a db
    if ($conn != null) {
        $user = isset($_POST['username']) ? $_POST['username'] : '';
        $pass = isset($_POST['password']) ? $_POST['password'] : '';

        if ($user != '' && $pass != '') {
            // protection against SQL injection
            $user = sql_clean_up($user);
            $pass = sql_clean_up($pass);

            // to change with an array
            $where = "username='" . $user . "' AND password='" . $pass . "'";

            $query = sql_query_select('*', 'user', $where, null);
            if ($query != null) {
                $res = mysqli_query($conn, $query);
                if ($res != false) {
                    $count = mysqli_num_rows($res);
                    if ($count == 1) {
                        $_SESSION['username'] = $user;
                        $_SESSION['password'] = $pass;
                        $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
                        $_SESSION['name'] = $row['name'];
                        $_SESSION['surname'] = $row['surname'];
                        // session is valid only for 2 minutes
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
function logout()
{
    session_destroy();
    session_start();
}

// insert bootstrap, jquery and other standard stuff
function insert_head()
{
    echo "<link rel='stylesheet' href='css/bootstrap.min.css'>";
    echo "<script type='text/javascript' src='javascript/jquery-2.1.4.min.js'></script>";
    echo "<script type='text/javascript' src='javascript/bootstrap.min.js'></script>";
    echo "<style type='text/css'>
        body {
            padding-top: 50px;
            padding-bottom: 20px;
        }
    </style>";
    echo "<style type='text/css'>
/* make sidebar nav vertical */
@media (min-width: 768px) {
  .sidebar-nav .navbar .navbar-collapse {
    padding: 0;
    max-height: none;
  }
  .sidebar-nav .navbar ul {
    float: none;
  }
  .sidebar-nav .navbar ul:not {
    display: block;
  }
  .sidebar-nav .navbar li {
    float: none;
    display: block;
  }
  .sidebar-nav .navbar li a {
    padding-top: 12px;
    padding-bottom: 12px;
  }
}
</style>";
}

// establish a connection to the db
function dbconnection()
{
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

// function to insert a value into a table
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

// function to update a row into a table
function sql_query_update($from, $values, $where) {
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