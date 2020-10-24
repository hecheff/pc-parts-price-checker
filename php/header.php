<div class="header">
    <div class="wrapper">
        <div class="title">
            PC Parts Price Checker Tool
            <div class="sub_title"><?php echo $GLOBALS['lang_data']['general_wip']; ?></div>
            <br>
            <div class="sub_title">
                Select Language (WIP): 
                <?php if ($_SESSION['lang'] != "en") : ?>
                    <a href="/php/setLang.php?lang=en">
                <?php endif; ?>
                    EN
                <?php if ($_SESSION['lang'] != "en") : ?>
                    </a>
                <?php endif; ?>
                | 
                <?php if ($_SESSION['lang'] != "jp") : ?>
                    <a href="/php/setLang.php?lang=jp">
                <?php endif; ?>
                    JP
                <?php if ($_SESSION['lang'] != "jp") : ?>
                    </a>
                <?php endif; ?>
            </div>
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