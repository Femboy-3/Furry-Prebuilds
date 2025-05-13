<?php

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "predracuni_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header("Location: login.php?ex=2");
    exit();
}

$company_name = $_GET['email'];
$password = sha1($_GET['password']);

$stmt = $conn->prepare("SELECT Login(?, ?) AS id");
$stmt->bind_param("ss", $company_name, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $row = $result->fetch_assoc();
    
    if ($row['id'] != -1) {
        $_SESSION['id'] = $row['id'];
        $_SESSION['email'] = $company_name;
        header("Location: index.php");
        exit();
    }
}

header("Location: login.php?ex=1");
exit();

?>
