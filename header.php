<header>
    <?php
    if (!isset($_SESSION['logged_time'])) {
        ?>
        <!-- User is not logged in -->
        <form action="index.php" method="post">
            <input type="hidden" name="status" value="login"/>
            <input type="text" name="username" placeholder="Username" required="required"/>
            <input type="password" name="password" placeholder="Password" required="required"/>
            <button type="submit">Login</button>
        </form>
    <?php
    } else {
        ?>
        <!-- User is logged in -->
        <form name="logoutForm" action="index.php" method="post">
            <input type="hidden" name="status" value="logout"/>
            <button type="submit">Logout</button>
        </form>
    <?php
    }
    ?>
</header>