<div class="header">
    <div class="wrapper">
        <div class="title">
            PC Parts Price Checker Tool
            <div class="sub_title">WORK IN PROGRESS!</div>
        </div>
        <div class="user_panel">
            <div class="user_panel_heading">User Login</div>
            <?php if (!isset($_SESSION['username']) || empty($_SESSION['username']) || !isset($_SESSION['password']) || empty($_SESSION['password'])) : ?>
                <form action="/php/login.php" method="post">
                    <input type="text" id="username" name="username" placeholder="Username" maxlength="32" required>
                    <input type="password" id="password" name="password" placeholder="Password" maxlength="32" required>
                    <input type="submit" class="input_button" value="Login">
                </form>
            <?php else: ?>
                Welcome, <?php echo $_SESSION['user_details']['username']; ?>.
                <button onclick="window.location.href='/php/logout.php';" class="input_button">Logout</button>
            <?php endif; ?>
        </div>
        <div class="divider"></div>
    </div>
</div>