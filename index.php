<?php
include 'common.php';
session_start();
session_expired();

$conn = dbconnection();

if (isset($_POST['status']) && $_POST['status'] == 'login') {
    login($conn);
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') {
        $_SESSION['HTTPS'] = 'on';
        $_SESSION['HTTP'] = 'off';
        $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $redirect");
        die();
    }
}
$islogged = is_loggedin();
if ($islogged && ($_SESSION['HTTPS'] != 'on' || $_SESSION['HTTP'] != 'off')) { // TODO -> doesn't work
    $_SESSION['HTTPS'] = 'on';
    $_SESSION['HTTP'] = 'off';
    $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $redirect");
    die();
}

if ($conn != null) {
    // TODO -> ordering is missing
    $query = query('*', 'activities', null);
    if ($query != null) {
        $res = mysqli_query($conn, $query);
    }

    //mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <?php
    insert_header();
    ?>
    <title>Index</title>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand"></a>
        </div>
        <div class="navbar-collapse collapse">
            <?php
            if (!$islogged) {
                ?>
                <!-- User is not logged in -->
                <form name="loginForm" action="index.php" class="navbar-form navbar-right" method="post">
                    <input type="hidden" name="status" value="login"/>

                    <div class="form-group">
                        <input type="text" name="username" placeholder="Username" class="form-control"
                               required="required"/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Password" class="form-control"
                               required="required"/>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                    <a href="registration.php" class="btn btn-default">Registration</a>
                </form>
            <?php
            } else {
                ?>
                <!-- User is logged in -->
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <form name="profileForm" action="" method="post" class="navbar-form">
                            <button type="submit" form="profileForm"
                                    class="btn btn-primary"><?= $_SESSION['username'] ?></button>
                        </form>
                    </li>
                    <li>
                        <form name="logoutForm" action="" method="post" class="navbar-form navbar-right">
                            <input type="hidden" name="status" value="logout"/>
                            <button type="submit" form="logoutForm" class="btn btn-danger">Logout</button>
                        </form>
                    </li>
                </ul>
            <?php
            }
            ?>
        </div>
    </div>
</div>
<div class="jumbotron">
    <div class="container">
        <table>
            <thead>
            <tr>
                <th>Activities</th>
                <th>Total Spaces</th>
                <th>Available Spaces</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($res != null) {
                while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['tot_spaces'] . "</td>";
                    echo "<td>" . $row['avail_spaces'] . "</td>";
                    echo "</tr>";
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
<?php
// free resources of db query
mysqli_free_result($res);
mysqli_close($conn);
?>
</html>