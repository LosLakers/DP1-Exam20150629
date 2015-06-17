<?php
include 'common_functions.php';
session_start();
if (session_expired()) {
    // redirect to index.php
//    http_redirect('index.php');
//    die();
}

if (isset($_POST['status']) && $_POST['status'] == 'logout') {
    logout();
}

$conn = dbconnection();

if (isset($_POST['status']) && $_POST['status'] == 'login') {
    login($conn);
    $islogged = true;
    /*if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') {
        $_SESSION['HTTPS'] = 'on';
        $_SESSION['HTTP'] = 'off';
        $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        //header("HTTP/1.1 301 Moved Permanently");
        header("Location: $redirect");
        die();
    }*/
}

/*if ($islogged && ($_SESSION['HTTPS'] != 'on' || $_SESSION['HTTP'] != 'off')) { // TODO -> doesn't work
    $_SESSION['HTTPS'] = 'on';
    $_SESSION['HTTP'] = 'off';
    $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    //header("HTTP/1.1 301 Moved Permanently");
    header("Location: $redirect");
    die();
}*/

if ($conn != null) {
    $islogged = isset($islogged) ? $islogged : is_loggedin($conn);

    $order = "avail_spaces DESC";
    $query = sql_query_select('*', 'activities', null, $order);
    if ($query != null) {
        $res = mysqli_query($conn, $query);
    }
} else {
    // TODO -> redirect to error page because db error
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
                if ($islogged) {
                    echo "<th># Children</th>";
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($res != null) {
                while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['reserv_spaces'] . "</td>";
                    echo "<td>" . $row['avail_spaces'] . "</td>";
                    if ($islogged) {
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