<?php
$conn = new mysqli("localhost", "root", "", "predracuni_db");

$result = $conn->query("SELECT id, name, amount FROM products");
$products = [];

while ($row = $result->fetch_assoc()) {
  $products[] = $row;
}

echo json_encode($products);