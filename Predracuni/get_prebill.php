<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "predracuni_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header("Location: login.php?ex=2");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("
    SELECT p.id, p.name, p.description, p.price, pbp.amount
    FROM prebills_products pbp 
    JOIN products p ON pbp.product_id = p.id
    WHERE pbp.prebill_id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
?>
