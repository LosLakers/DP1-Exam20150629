<?php
include 'common.php';
session_start();

$conn = dbconnection();

if (isset($_POST['status']) && $_POST['status'] == 'login') {
    $user = isset($_POST['username']) ? $_POST['username'] : '';
    $pass = isset($_POST['password']) ? $_POST['password'] : '';

    if ($user != '' && $pass != '') {
        // protection against SQL injection -> TODO as to be improved??
        $user = stripslashes($user);
        $pass = stripslashes($pass);
        $user = mysql_real_escape_string($user);
        $pass = mysql_real_escape_string($pass);

        // to change with an array
        $where = "username='".$user."' AND password='".$pass."'";

        $query = query('*', 'user', $where);
        if ($query != null) {
            $res = mysqli_query($conn, $query);
            if ($res != false) {
                $count = mysqli_num_rows($res);
                if ($count == 1) {
                    $_SESSION['username'] = $user;
                    $_SESSION['password'] = $pass;
                    // session is valid only for 2 minutes -> TODO doesn't work
                    //ini_set('session.gc_maxlifetime', 2 * 60);

                    $_SESSION['login'] = true;
                } else {
                    // TODO -> do something??
                }
            }

            mysqli_free_result($res);
        }
    }
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
            if (!is_loggedin()) {
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
                            <button type="submit" form="profileForm" class="btn btn-primary"><?=$_SESSION['username']?></button>
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
                    echo "<td>".$row['name']."</td>";
                    echo "<td>".$row['tot_spaces']."</td>";
                    echo "<td>".$row['avail_spaces']."</td>";
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