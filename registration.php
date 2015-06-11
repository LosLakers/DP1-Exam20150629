<?php
include 'common.php';
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <?php
    insert_header();
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
<div class="container">
    <form id="registration" class="col-lg-4 col-md-4" name="registrationForm" method="post" action="">
        <input type="hidden" name="status" value="registered"/>

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