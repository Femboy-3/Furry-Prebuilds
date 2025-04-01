<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="icon" href="resources/icon_white.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="icon-container">
            <img id="icon-img" src="resources/icon_white.png" alt="Icon">
            <span id="titles">Website Name</span>
        </div>
        <form action="db_register.php" method="GET">
            <div class="input-container">
                <input type="text" name="firstname" placeholder="Firstname" required>
                <input type="text" name="lastname" placeholder="Lastname" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="cpassword" placeholder="Password (confirm)" required>
                <?php 
                    if(isset($_GET['ex']))
                        switch ($_GET['ex']) { 
                            case 1:
                                echo '<p id="exception">Passwords does not match</p>'; 
                                break;
                            case 2:
                                echo '<p id="exception">User with this email already exist</p>';
                                break;
                            }
                ?>
            </div>
            <button type="submit">Register</button></br>
        </form>
        <p id="normalText">Already have a account</p><a href="login.php"> Login</a>
    </div>
</body>
</html>