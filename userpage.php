<?php
include 'common_functions.php';
include 'error_handling.php';

session_start();

cookie_check();

if (isset($_SESSION['logged_time'])) {
    if (session_expired()) {
        // redirect to index.php
        redirect_to("index.php");
    }

    check_user_validity();

    $referer = "http";
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referer = strpos(strtolower($_SERVER['HTTP_REFERER']), 'https') === FALSE ? 'http' : 'https';
    }
    if (isset($_SESSION['HTTPS']) && strcmp($referer, "https") != 0) {

        if ($_SESSION['HTTPS'] != false) {
            $_SESSION['HTTPS'] = false;
            $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            redirect_to($redirect);
        } else {
            $_SESSION['HTTPS'] = true;
        }
    }

    $conn = dbconnection();
    if (!mysqli_connect_error()) {
        if (isset($_POST['status'])) {
            switch ($_POST['status']) {
                case 'reservation' : {
                    // if the value is not a number, it will set the variable to 0
                    // check for non empty array that returns 1
                    $activity = 0;
                    $children = 0;
                    if (isset($_POST['activity']) && !is_array($_POST['activity'])) {
                        $activity = intval($_POST['activity']);
                    }
                    if (isset($_POST['children']) && !is_array($_POST['children'])) {
                        $children = intval($_POST['children']);
                    }

                    if ($activity > 0 && $children >= 0 && $children <= $MAX_CHILDREN) {
                        $where = "username='" . $_SESSION['username'] . "' AND id_activity='" . $activity . "'";
                        $query = sql_query_select("*", "reservations", $where, null);
                        if ($query != null) {
                            $res = mysqli_query($conn, $query);
                            // if the user doesn't have any reservation for that activity, than he could add it
                            if ($res != false && mysqli_num_rows($res) == 0) {
                                mysqli_free_result($res);
                                // check if it is possible to reserve the places
                                $query = sql_query_select("avail_spaces", "activities", "id=$activity", null);
                                if ($query != null) {
                                    $res = mysqli_query($conn, $query);
                                    $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
                                    if ($res != false && $row['avail_spaces'] >= ($children + 1)) {
                                        mysqli_free_result($res);
                                        try {
                                            mysqli_autocommit($conn, false);

                                            // create the children and get their id
                                            $children_array = array();
                                            for ($i = 1; $i <= $MAX_CHILDREN; $i++) {
                                                $children_array[$i] = 0;
                                            }
                                            for ($i = 1; $i <= $children; $i++) {
                                                $query = sql_query_insert("children()", "()");
                                                if (!mysqli_query($conn, $query)) throw new Exception();
                                                $id = mysqli_insert_id($conn);
                                                $children_array[$i] = $id;
                                            }

                                            // insert the reservation
                                            $insert = "reservations(id_activity, username, id_child1, id_child2, id_child3)";
                                            $values = "('" . $activity . "', '" . $_SESSION['username'] . "', '" . $children_array[1] . "', '"
                                                . $children_array[2] . "', '" . $children_array[3] . "')";
                                            $query = sql_query_insert($insert, $values);
                                            if (!mysqli_query($conn, $query)) throw new Exception();

                                            // modify the activity places
                                            $values = "reserv_spaces=reserv_spaces+1+" . $children . ", avail_spaces=avail_spaces-1-" . $children;
                                            $query = sql_query_update("activities", $values, "id='" . $activity . "'");
                                            if (!mysqli_query($conn, $query)) throw new Exception();
                                            mysqli_commit($conn);
                                            $error = 'SUCCESS CREATE RESERVATION';
                                        } catch (Exception $e) {
                                            mysqli_rollback($conn);
                                            $error = 'ERROR CREATE RESERVATION';
                                        }
                                    } else {
                                        $error = 'ERROR CREATE RESERVATION';
                                    }
                                }
                            } else {
                                // redirect to error page because of an unauthorized action
                                mysqli_close($conn);
                                error_page_redirect("I'm sorry, but you're trying something that is unauthorized");
                            }
                        }
                    } else {
                        // redirect to error page because of unusual activity
                        mysqli_close($conn);
                        error_page_redirect("I'm sorry, but you're trying something that is unauthorized");
                    }
                    break;
                }
                case 'delete' : {
                    // delete one or more reservations
                    if (isset($_POST['reservation']) && is_array($_POST['reservation'])) {
                        $reservations = array();

                        // check of reservations' id
                        $num_elem = count($_POST['reservation']);
                        for ($i = 0; $i < $num_elem; $i++) {
                            // intval return 1 if array is not empty
                            $id_reserv = !is_array($_POST['reservation'][$i]) ? intval($_POST['reservation'][$i]) : 0;
                            if ($id_reserv == 0) {
                                // manage the error with a redirect
                                error_page_redirect("I'm sorry, but you're trying something that is unauthorized");
                            }
                            $reservations[$id_reserv] = array(
                                "activity" => 0,
                                "child1" => 0,
                                "child2" => 0,
                                "child3" => 0
                            );

                            // check if at each reservation corresponds the user that want to perform the delete
                            // and create an array with each reservation with the corresponding data needed
                            $where = "username='" . $_SESSION['username'] . "' AND id='" . $id_reserv . "'";
                            $query = sql_query_select("*", "reservations", $where, null);
                            if ($query != null) {
                                $res = mysqli_query($conn, $query);
                                if ($res != false && mysqli_num_rows($res) == 1) {
                                    $row = mysqli_fetch_array($res, MYSQL_ASSOC);

                                    // get activity
                                    $reservations[$id_reserv]['activity'] = $row['id_activity'];

                                    // get children
                                    $reservations[$id_reserv]['child1'] = $row['id_child1'];
                                    $reservations[$id_reserv]['child2'] = $row['id_child2'];
                                    $reservations[$id_reserv]['child3'] = $row['id_child3'];
                                    mysqli_free_result($res);
                                } else {
                                    mysqli_free_result($res);
                                    // database error
                                    mysqli_close($conn);
                                    error_page_redirect("Database connection error - userpage.php line 131");
                                }
                            }
                        }
                        try {
                            mysqli_autocommit($conn, false);
                            //mysqli_begin_transaction($conn);
                            foreach ($reservations as $key => $values) {
                                $num_children = 0;
                                // delete children
                                for ($i = 1; $i <= 3; $i++) {
                                    $id_child = $values['child' . $i];
                                    if ($id_child != 0) { // 0 is the null value
                                        $where = "id='" . $values['child' . $i] . "'";
                                        $query = sql_query_delete("children", $where);
                                        if ($query != null) {
                                            if (!mysqli_query($conn, $query)) {
                                                throw new Exception("ERROR DELETE RESERVATION");
                                            }
                                        }
                                        $num_children++;
                                    }
                                }

                                // increase and decrease spaces in the activity
                                $value = "reserv_spaces=reserv_spaces-1-" . $num_children
                                    . ", avail_spaces=avail_spaces+1+" . $num_children;
                                $where = "id=" . $values['activity'];
                                $query = sql_query_update("activities", $value, $where);
                                if ($query != null) {
                                    if (!mysqli_query($conn, $query)) {
                                        throw new Exception("ERROR DELETE RESERVATION");
                                    }
                                }

                                // delete reservation
                                $query = sql_query_delete("reservations", "id=" . $key);
                                if ($query != null) {
                                    if (!mysqli_query($conn, $query)) {
                                        throw new Exception("ERROR DELETE RESERVATION");
                                    }
                                }
                            }
                            mysqli_commit($conn);
                            $error = 'SUCCESS_DELETE_RESERVATION';
                        } catch (Exception $e) {
                            mysqli_rollback($conn);
                            $error = $e->getMessage();
                        }
                    }
                    break;
                }
            }
        }
    } else {
        // database error
        error_page_redirect("Database error connection - userpage.php line 195");
    }
} else {
    // user is not logged in -> redirect to index.php
    session_destroy();
    redirect_to("index.php");
}
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <?php
    insert_head();
    ?>
    <title>User Page</title>
</head>
<body>
<?php
include "header.php";

include 'error_message.php'
?>
<!-- Notify if Javascript is enabled or not -->
<noscript>
    <?php
    $error = "ERROR JAVASCRIPT DISABLED";
    include 'error_message.php';
    ?>
</noscript>
<div>
    <?php
    include 'navigation_bar.php';
    ?>
    <div class="right-half">
        <h3>Name</h3>

        <p><?= $_SESSION['name'] ?></p>
        <br/>

        <h3>Surname</h3>

        <p><?= $_SESSION['surname'] ?></p>
        <br/>
        <?php
        $select = "reservations.id, name, id_child1, id_child2, id_child3";
        $from = "reservations JOIN activities ON reservations.id_activity=activities.id";
        $where = "username='" . $_SESSION['username'] . "'";
        $query = sql_query_select($select, $from, $where, null);
        if ($query != null) {
            $res = mysqli_query($conn, $query);
            if (mysqli_num_rows($res) > 0) {
                ?>
                <h3>Overview of the reserved Activities</h3>
                <table>
                    <thead>
                    <tr>
                        <th>Select</th>
                        <th>Activities</th>
                        <th># Children</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                        $i = 0;
                        // count the number of children
                        if ($row['id_child1'] != 0) {
                            $i = 1;
                            if ($row['id_child2'] != 0) {
                                $i = 2;
                                if ($row['id_child3'] != 0) {
                                    $i = 3;
                                }
                            }
                        }
                        echo "<tr>";
                        echo "<td><input type='checkbox' name='reservation' value='" . $row['id'] . "'></td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $i . "</td>";
                        echo "</tr>";
                    }
                    mysqli_free_result($res);
                    ?>
                    </tbody>
                </table>
                <!-- Reservations to delete will be added as hidden input through jQuery -->
                <form id="deleteReservations" action="userpage.php" method="post">
                    <input type="hidden" name="status" value="delete">
                    <button type="submit">Delete</button>
                </form>
            <?php
            } else {
                ?>
                <h3>Message</h3>
                <p>No reservation are present for this user</p>
            <?php
            }
        }
        ?>
    </div>
</div>
<?php
// close db connection
mysqli_close($conn);
?>
</body>
<script type="text/javascript" src="javascript/userpage.js"></script>
</html>