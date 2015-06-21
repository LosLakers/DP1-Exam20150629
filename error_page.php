<?php
include 'common_functions.php';

session_start();
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : "Nothing";
// to check if cookies are enabled
if (isset($_SESSION['cookie_en'])) {
    if (strcmp($error_message, "Nothing") == 0)
        redirect_to("index.php");
} else {
    $error_message = "Cookies are not enabled, you cannot visit the site";
}
session_destroy();
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Error Page</title>
</head>
<body>
<div>
    <p>Something wrong happen - report the following message to the site administrator</p>
    <code><?= $error_message ?></code>
    <br/>
    <a href="index.php">Return to Home</a>
</div>
</body>
</html>