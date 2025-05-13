<?php
$conn = new mysqli("localhost", "root", "", "predracuni_db");

if (isset($_POST['id']) && isset($_POST['amount'])) {
  $id = intval($_POST['id']);
  $amount = intval($_POST['amount']);

  $stmt = $conn->prepare("UPDATE products SET amount = amount + ? WHERE id = ?");
  $stmt->bind_param("ii", $amount, $id);
  $stmt->execute();

  echo "Quantity updated.";
}