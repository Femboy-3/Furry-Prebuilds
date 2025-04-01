<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="resources/icon_white.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="icon-container">
            <img id="icon-img" src="resources/icon.png" alt="Icon">
            <span id="titles">Website Name</span>
        </div>
        <form action="db_login.php" method="GET">
            <div class="input-container">
                <input type="text" name="email" placeholder="Company name" required>
                <input type="password" name="password" placeholder="Password" required>
                <?php if(isset($_GET['ex'])) if($_GET['ex'] == 1) echo '<p id="exception">Wrong email or password</p>'; ?>
            </div>
            <button type="submit">Login</button></br>
        </form>
        <p id="normalText">Don't have a account</p><a href="register.php"> Register</a>
    </div>
</body>
</html>