<?php

?>
<div class="left-half">
    <h3>Available Operations</h3>
    <ul class="nav">
        <li>
            <a href="index.php">Index</a>
        </li>
        <?php
        if (!$islogged) {
            ?>
            <li>
                <a href="registration.php">Registration</a>
            </li>
        <?php
        } else {
            ?>
            <li>
                <a href="userpage.php">Profile</a>
            </li>
        <?php
        }
        ?>
    </ul>
</div>