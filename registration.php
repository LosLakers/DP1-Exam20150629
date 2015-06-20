<?php
include 'common_functions.php';
include 'error_handling.php';

session_start();

// HTTPS redirect
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    redirect_to("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}

cookie_check();

// if the user is already logged in, a redirect him to his user page
if (isset($_SESSION['logged_time'])) {
    redirect_to("userpage.php");
}

if (isset($_POST['status']) && $_POST['status'] == 'registration') {
    $username = isset($_POST['username']) ? $_POST['username'] : "";
    $password = isset($_POST['password']) ? $_POST['password'] : "";
    $conf_pass = isset($_POST['conf_password']) ? $_POST['conf_password'] : "";

    // server validation of inserted data
    if ($username != "" && $password != "" && strcmp($password, $conf_pass) == 0) {
        $username = sql_clean_up($username);
        $password = sql_clean_up($password);
        $name = $_POST['name'] != '' ? sql_clean_up($_POST['name']) : "-";
        $surn = $_POST['surname'] != '' ? sql_clean_up($_POST['surname']) : "-";

        $conn = dbconnection();
        if (!mysqli_connect_error()) {
            // check if the username is unique or not
            $select = "username";
            $from = "user";
            $where = "username='" . $username . "'";
            $query = sql_query_select($select, $from, $where, null);
            $res = mysqli_query($conn, $query);
            if ($res != false) {
                if (mysqli_num_rows($res) != 0) {
                    // the username is already in the db
                    $error = 'ERROR_USERNAME_SELECT';
                    mysqli_free_result($res);
                    mysqli_close($conn);
                } else {
                    mysqli_free_result($res);
                    mysqli_autocommit($conn, false);
                    $insert = "user(username, password, name, surname)";
                    $values = "('" . $username . "', '" . $password . "' ,'" . $name . "', '" . $surn . "')";
                    $query = sql_query_insert($insert, $values);
                    try {
                        if ($query != null && !mysqli_query($conn, $query))
                            throw new Exception();
                        mysqli_commit($conn);
                        mysqli_close($conn);
                        $error = 'SUCCESS_USER_INSERT';
                    } catch (Exception $e) {
                        // error in performing insert in the db
                        $error = 'ERROR_USER_INSERT';
                        mysqli_rollback($conn);
                        mysqli_close($conn);
                    }
                }
            } else {
                session_start();
                // redirect to error page for database connection error
                mysqli_close($conn);
                error_page_redirect("Database error connection - registration.php line 55");
            }
        } else {
            session_start();
            // redirect to error page for database connection error
            mysqli_close($conn);
            error_page_redirect("Database error connection - registration.php line 55");
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <?php
    insert_head();
    ?>
    <title>Registration</title>
</head>
<body>
<?php
include 'header.php';

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
        <h3>Registration to the website</h3>

        <p>
            In this page, you can register to the website in the order to have full access to
            all the functionality of the website.
        </p>
        <br/>

        <div>
            <form id="registration" name="registrationForm" method="post" action="registration.php">
                <fieldset>
                    <legend><b>Registration From</b></legend>
                    <input type="hidden" name="status" value="registration"/>

                    <div>
                        <label>Insert Username</label><br/>
                        <input type="text" name="username" placeholder="Username" required="required"/>
                    </div>
                    <br/>

                    <div>
                        <label>Insert Password</label><br/>
                        <input id="password" type="password" name="password" placeholder="Password"
                               required="required"/>
                    </div>
                    <br/>

                    <div>
                        <label>Confirm Password</label><br/>
                        <input id="conf_password" type="password" name="conf_password" placeholder="Password"
                               required="required"/>
                    </div>
                    <br/>

                    <div>
                        <label>Insert Name</label><br/>
                        <input type="text" name="name" placeholder="Name"/>
                    </div>
                    <br/>

                    <div>
                        <label>Insert Surname</label><br/>
                        <input type="text" name="surname" placeholder="Surname"/>
                    </div>

                    <br/>
                    <button type="submit">Confirm</button>
                    <a href="index.php">Go Home</a>
                    <br/>
                </fieldset>
            </form>
        </div>
    </div>
</div>
</body>
<!-- load javascript files -->
<script type="text/javascript" src="javascript/registration_val.js"></script>
</html>