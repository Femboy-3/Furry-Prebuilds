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

$firstname = $_GET['firstname'];
$lastname = $_GET['lastname'];
$email = $_GET['email'];
$password = password_hash($_GET['password'], PASSWORD_BCRYPT);
$cpassword = password_hash($_GET['cpassword'], PASSWORD_BCRYPT);

if($password != $cpassword){
    header("Location: register.php?ex=1");
    exit();
}

$stmt = $conn->prepare("SELECT Register(?, ?, ?, ?) AS id");
$stmt->bind_param("ssss", $firstname, $lastname, $email, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $row = $result->fetch_assoc();
    
    if ($row['id'] != -1) {
        $_SESSION['id'] = $row['id'];
        $_SESSION['email'] = $email;
        header("Location: index.php");
        exit();
    }
}

header("Location: register.php?ex=2");
exit();
?>