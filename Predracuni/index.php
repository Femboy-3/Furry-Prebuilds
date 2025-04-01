<?php
    session_start();

    if(!isset($_SESSION['id']) && !isset($_SESSION['email'])){
        header("Location: login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main page</title>
    <link rel="icon" href="resources/icon_white.png" type="image/png">
    <link rel="stylesheet" href="style2.css">
</head>

<body>
    <p>Sigma boi</p>
</body>