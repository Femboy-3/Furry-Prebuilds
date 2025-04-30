<?php

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "predracuni_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header("Location: login.php?ex=3");
    exit();
}

$email = $_GET['email'];
$password = sha1($_GET['password']);
$cpassword = sha1($_GET['cpassword']);

if($password != $cpassword){
    header("Location: register.php?ex=1");
    exit();
}

$stmt = $conn->prepare("SELECT Register(?, ?) AS id");
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $row = $result->fetch_assoc();
    
    if ($row['id'] != -1) {
        $_SESSION['id'] = $row['id'];
        $_SESSION['name'] = $email;
        header("Location: index.php");
        exit();
    }
}

header("Location: register.php?ex=2");
exit();
?>