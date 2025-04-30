<?php
// search.php
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Your database connection
    // Make sure to change the credentials accordingly
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "predracuni_db";
    
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to fetch product details
    $sql = "SELECT name, description, price, amount FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($name, $description, $price, $stock);

    // Check if a result is found
    if ($stmt->fetch()) {
        // Return data as a string, separate by a delimiter (e.g., ||)
        echo "{$name}||{$description}||{$price}||{$stock}";
    } else {
        echo "No product found.";
    }

    // Close connection
    $stmt->close();
    $conn->close();
} else {
    echo "No ID provided.";
}
?>
