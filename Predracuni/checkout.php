<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "predracuni_db";

session_start();

if(!isset($_SESSION['id']) && !isset($_SESSION['email'])){
    // header("Location: login.php");
    // exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['ids']) && isset($_GET['quantities']) && isset($_GET['info'])) {
    $ids = explode(',', $_GET['ids']);
    $quantities = explode(',', $_GET['quantities']);
    $info = htmlspecialchars($_GET['info']);

    if (count($ids) !== count($quantities)) {
        die("Product and quantity count mismatch.");
    }

    $stmt = $conn->prepare("INSERT INTO prebills (user_info, company_id) VALUES (?, ?)");
    $stmt->bind_param("si", $info, $_SESSION['id']);
    if (!$stmt->execute()) {
        die("Failed to insert prebill: " . $stmt->error);
    }

    $prebillId = $stmt->insert_id;

    $stmt = $conn->prepare("INSERT INTO prebills_products (prebill_id, product_id, amount) VALUES (?, ?, ?)");
    $updateStmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");

    for ($i = 0; $i < count($ids); $i++) {
        $productId = intval($ids[$i]);
        $quantity = intval($quantities[$i]);

        // Insert into prebill
        $stmt->bind_param("iii", $prebillId, $productId, $quantity);
        if (!$stmt->execute()) {
            echo "Error inserting product ID $productId: " . $stmt->error . "<br>";
            continue;
        }

        // Subtract from storage
        $updateStmt->bind_param("ii", $quantity, $productId);
        if (!$updateStmt->execute()) {
            echo "Error updating storage for product ID $productId: " . $updateStmt->error . "<br>";
        }
    }

    $stmt->close();
    $updateStmt->close();
}

$conn->close();
header("Location: generate_pdf.php?id=$prebillId");
exit();
?>
