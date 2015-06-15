<?php
include 'common_functions.php';
session_start();
if (session_expired()) {
    // redirect to index.php
    $redirect = "http://" . $_SERVER['HTTP_HOST'] . "index.php";
    header("HTTP/1.1 200");
    header("Location $redirect");
    die();
}

$conn = dbconnection();
if ($conn != null) {
    $islogged = is_loggedin($conn);
    if ($islogged) {
        if (isset($_POST['status']) && $_POST['status'] == 'reservation') {
            // if the value is not a number, it will set the variable to 0
            $activity = isset($_POST['activity']) ? intval($_POST['activity']) : -1;
            $children = isset($_POST['children']) ? intval($_POST['children']) : -1;

            if ($activity > 0 && $children >= 0 && $children <= 3) { // change children <= with a global constant variable
                $where = "username='" . $_SESSION['username'] . "' AND id_activity='" . $activity . "'";
                $query = sql_query_select("*", "reservations", $where, null);
                if ($query != null) {
                    $res = mysqli_query($conn, $query);
                    // if the user doesn't have any reservation for that activity, than he could add it
                    if (mysqli_num_rows($res) == 0) {
                        mysqli_free_result($res);
                        // check if it is possible to reserve the places
                        $query = sql_query_select("avail_spaces", "activities", "id=$activity", null);
                        if ($query != null) {
                            $res = mysqli_query($conn, $query);
                            $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
                            if ($res != null && $row['avail_spaces'] >= ($children+1)) {
                                mysqli_free_result($res);
                                try {
                                    mysqli_autocommit($conn, false);

                                    // create the children and get their id
                                    $children_array = array(
                                        1 => null,
                                        2 => null,
                                        3 => null
                                    );
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
                                    $values = "reserv_spaces=reserv_spaces+1+".$children.", avail_spaces=avail_spaces-1-".$children;
                                    $query = sql_query_update("activities", $values, "id='".$activity."'");
                                    if (!mysqli_query($conn, $query)) throw new Exception();
                                    mysqli_commit($conn);
                                } catch (Exception $e) {
                                    mysqli_rollback($conn);
                                    // TODO -> error message
                                }
                            } else {
                                // TODO -> set an error message??
                            }
                        }
                    } else {
                        // TODO -> set an error message??
                    }
                }
            }
        }
    } else {
        // redirect to index.php
        $redirect = "http://" . $_SERVER['HTTP_HOST'] . "index.php";
        header("HTTP/1.1 200");
        header("Location $redirect");
        die();
    }
} else {
    // TODO -> redirect to error page
}
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <?php
    insert_head();
    ?>
    <title>User Page</title>
</head>
<body>
<?php
include "header.php";
?>
<div class="container">
    <br/>

    <div class="row">
        <div class="col-sm-3">
            <div class="sidebar-nav">
                <div class="navbar navbar-default" role="navigation">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse"
                                data-target=".sidebar-navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <span class="visible-xs navbar-brand">Available Operations</span>
                    </div>
                    <div class="navbar-collapse collapse sidebar-navbar-collapse">
                        <ul class="nav navbar-nav">
                            <?php
                            if (!$islogged) {
                                ?>
                                <li>
                                    <a href="registration.php">Registration</a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-9">
            <legend>Name</legend>
            <p><?=$_SESSION['name']?></p>
            <br/>
            <legend>Surname</legend>
            <p><?=$_SESSION['surname']?></p>
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
                    <legend>Overview of the reserved Activities</legend>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Select</th>
                            <th>Activities</th>
                            <th># Children</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($res != null) {
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
                                echo "<td><input class='checkbox' type='checkbox' name='reservation' value='".$row['id']."'></td>";
                                echo "<td>" . $row['name'] . "</td>";
                                echo "<td>" . $i . "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                    <!-- Reservations to delete will be added as hidden input through jQuery -->
                    <form id="deleteReservations" action="userpage.php" method="post">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                <?php
                } else {
                    ?>
                    <legend>Message</legend>
                    <p>No reservation are present for this user</p>
                    <?php
                }
                mysqli_free_result($res);
            }
            ?>
        </div>
    </div>
</div>
</body>
<?php
// close db connection
mysqli_close($conn);
?>
</body>
</html>