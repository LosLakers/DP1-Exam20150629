<?php
include 'common_functions.php';
include 'error_handling.php';

session_start();

// HTTPS redirect
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    redirect_to("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}

// check if cookies are enabled
cookie_check();
if (isset($_SESSION['cookieEn']) && $_SESSION['cookieEn'] == false) {
    setcookie("cookie_enabled", "enabled", null, "/");
    $_SESSION['cookieEn'] = true;
}

// if user is logged in, then check if the session is expired or not
session_expired();

// double check user validity - security reason
check_user_validity();

$conn = dbconnection();
if (!mysqli_connect_error()) {
    if (isset($_POST['status'])) {
        switch ($_POST['status']) {
            case 'logout': { // manage logout
                logout();
                break;
            }
            case 'login': { // manage login
                $username = isset($_POST['username']) ? $_POST['username'] : '';
                $password = isset($_POST['password']) ? $_POST['password'] : '';
                if (!login($conn, $username, $password)) {
                    $error = "ERROR LOGIN";
                }
                if (isset($_SESSION['logged_time'])) {
                    // set a cookie with a md5 random value saved in the $_SESSION for double check
                    $id_rand = rand();
                    $id_rand = md5($id_rand);
                    setcookie("user_cookie", $id_rand, 0, "/", null, false, true);
                    $_SESSION['user_cookie'] = $id_rand;
                }
                break;
            }
            default:
                break;
        }
    }
} else {
    // redirect to error page because of db error
    error_page_redirect("Database connection error - index.php line 41");
}
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <?php
    insert_head();
    ?>
    <title>Index</title>
</head>
<body>
<?php
include "header.php";

include 'error_message.php';
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
        <h3>Overview of the available Activities</h3>
        <table>
            <thead>
            <tr>
                <th>Activities</th>
                <th>Reserved Spaces</th>
                <th>Available Spaces</th>
                <?php
                if (isset($_SESSION['logged_time'])) {
                    echo "<th># Children</th>";
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            if (isset($_SESSION['logged_time'])) {
                // get all reservations already performed
				mysqli_query($conn, "LOCK TABLES reservations READ, activities READ");
                $where = "username='" . $_SESSION['username'] . "'";
                $query = sql_query_select('id_activity', 'reservations', $where, null);
                $reservations = array(
                    0 => true,
                );
                if ($query != null) {
                    $res = mysqli_query($conn, $query);
                    if ($res != false) {
                        if (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_array($res, MYSQL_ASSOC)) {
                                $reservations[$row['id_activity']] = true;
                            }
                            mysqli_free_result($res);
                        }
                    } else {
                        mysqli_close($conn);
                        error_page_redirect("Error in loading activities, please contact the administrator");
                    }
                }
            }

            // get activities data
            $order = "avail_spaces DESC";
            $query = sql_query_select('*', 'activities', null, $order);
            if ($query != null) {
                $res = mysqli_query($conn, $query);
                if ($res != false) {
                    while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['reserv_spaces'] . "</td>";
                        echo "<td>" . $row['avail_spaces'] . "</td>";
                        if (isset($_SESSION['logged_time'])) {
                            if (!isset($reservations[$row['id']])) {
                                ?>
                                <td>
                                    <?php
                                    if ($row['avail_spaces'] > 0) {
                                        $children = $row['avail_spaces'] - 1;
                                        ?>
                                        <form action="userpage.php" method="post">
                                            <select name="children" required="required">
                                                <option value="0">0</option>
                                                <?php
                                                for ($i = 1; $i <= $children && $i <= Common::get_max_children(); $i++) {
                                                    echo "<option value='$i'>$i</option>";
                                                }
                                                ?>
                                            </select>
                                            <input type="hidden" name="activity" value="<?= $row['id'] ?>">
                                            <input type="hidden" name="status" value="reservation">
                                            <button type="submit">Confirm</button>
                                        </form>
                                    <?php
                                    } else {
                                        echo "No places available";
                                    }
                                    ?>
                                </td>
                            <?php
                            } else {
                                echo "<td>Already Reserved</td>";
                            }
                        }
                        echo "</tr>";
                    }
                    mysqli_free_result($res);
                } else {
                    mysqli_close($conn);
                    error_page_redirect("Error in loading the activities, please contact the administrator");
                }
            }
			mysqli_query($conn, "UNLOCK TABLES");
            ?>
            </tbody>
        </table>
        <?php
        if (isset($_SESSION['logged_time'])) {
            ?>
            <br/>
            <h4>Reservation rules</h4>
            <ul>
                <li>Each reservation consists in a place for the user and 0, 1 or more places for the children</li>
                <li>Actually, the maximum number of children can be <?= Common::get_max_children() ?></li>
                <li>Is not possible to make more than one reservation for each activity</li>
            </ul>
        <?php
        }
        ?>
    </div>
</div>
</body>
<?php
// close db connection
mysqli_close($conn);
?>
<!-- load javascript files -->
<script type="text/javascript" src="javascript/common.js"></script>
</html>