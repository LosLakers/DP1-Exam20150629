<?php
include 'common_functions.php';
include 'error_handling.php';

session_start();
cookie_check();

session_expired();

$prot = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https')
=== FALSE ? 'http' : 'https';

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
            case 'logout': { // manage logout
                logout();
                break;
            }
            case 'login': { // manage login
                $username = isset($_POST['username']) ? $_POST['username'] : '';
                $password = isset($_POST['password']) ? $_POST['password'] : '';
                login($conn, $username, $password);
                if (isset($_SESSION['logged_time'])) {
                    // set a cookie with a md5 random value saved in the $_SESSION for double check
                    $id_rand = rand();
                    $id_rand = md5($id_rand);
                    setcookie("user_cookie", $id_rand, 0, "/", null, false, true);
                    $_SESSION['user_cookie'] = $id_rand;
                    // redirect to https page
                    $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    $_SESSION['HTTPS'] = false;
                    redirect_to($redirect);
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
                            // TODO manage activities reservation already done
                            ?>
                            <td>
                                <form action="userpage.php" method="post">
                                    <select name="children" required="required">
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                    <input type="hidden" name="activity" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="status" value="reservation">
                                    <button type="submit">Confirm</button>
                                </form>
                            </td>
                        <?php
                        }
                        echo "</tr>";
                    }
                    mysqli_free_result($res);
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
<?php
// close db connection
mysqli_close($conn);
?>
</html>