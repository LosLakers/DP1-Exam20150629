<?php
include 'common_functions.php';
include 'error_handling.php';

$error = null;

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
        // check if the username is unique or not
        $select = "username";
        $from = "user";
        $where = "username='" . $username . "'";
        $query = sql_query_select($select, $from, $where, null);
        $res = mysqli_query($conn, $query);
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
<div class="container">
    <legend>Registration to the website</legend>
    <p class="text-info">
        In this page, you can register to the website in the order to have full access to
        all the functionality of the website.
    </p>
</div>
<br/>
<!-- TODO -> manage error messages and translate it in php page to include -->
<?php
if ($error != null) {
    ?>
    <div class="container">
        <div class="col-lg-4 col-md-4 alert alert-dismissable <?=get_message_type($error)?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
            <p class="message"><?=get_message($error)?></p>
        </div>
    </div>
    <br/>
<?php
}
?>
<div class="container">
    <form id="registration" class="col-lg-4 col-md-4" name="registrationForm" method="post" action="registration.php">
        <input type="hidden" name="status" value="registration"/>

        <div class="form-group">
            <label class="control-label">Insert Username</label>
            <input type="text" class="form-control" name="username" placeholder="Username" required="required"/>
        </div>
        <br/>

        <div class="form-group">
            <label class="control-label">Insert Password</label>
            <input id="password" type="password" class="form-control" name="password" placeholder="Password" required="required"/>
        </div>
        <br/>

        <div class="form-group has-feedback">
            <label class="control-label">Confirm Password</label>
            <input id="conf_password" type="password" class="form-control" name="conf_password" placeholder="Password"
                   required="required"/>
            <span class="glyphicon form-control-feedback"></span>
        </div>
        <br/>

        <div class="form-group">
            <label class="control-label">Insert Name</label>
            <input type="text" class="form-control" name="name" placeholder="Name"/>
        </div>
        <br/>

        <div class="form-group">
            <label class="control-label">Insert Surname</label>
            <input type="text" class="form-control" name="surname" placeholder="Surname"/>
        </div>

        <br/>
        <button type="submit" class="btn btn-primary">Confirm</button>
        <a href="index.php" class="btn btn-warning">Go Home</a>
        <br/>
    </form>
</body>
<!-- load javascript files -->
<script type="text/javascript" src="javascript/registration_val.js"></script>
</html>