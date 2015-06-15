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
                <form action="index.php" class="navbar-form navbar-right" method="post">
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
                </form>
            <?php
            } else {
                ?>
                <!-- User is logged in -->
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <form class="navbar-form" action="userpage.php" method="post">
                            <button type="submit" class="btn btn-primary"><?= $_SESSION['username'] ?></button>
                        </form>
                    </li>
                    <li>
                        <form class="navbar-form navbar-right" name="logoutForm" action="index.php" method="post">
                            <input type="hidden" name="status" value="logout"/>
                            <button type="submit" class="btn btn-danger">Logout</button>
                        </form>
                    </li>
                </ul>
            <?php
            }
            ?>
        </div>
    </div>
</div>