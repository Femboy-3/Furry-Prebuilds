<?php
    session_start();

    if(!isset($_SESSION['id']) && !isset($_SESSION['email'])){
        //header("Location: login.php");
        //exit();
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
    <div id="navigation">
        <a href="#home" class="nav-link">Home</a>
        <a href="#manage" class="nav-link">Manage</a>
        <a href="#history" class="nav-link">History</a>
        <a href="#storage" class="nav-link">Storage</a>
        <a href="#info" class="nav-link">Info</a>
    </div>

    <div id="home" class="body-container active">
        <button>Create new</button>


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

// Selects everything for the bill (except products)
$query = "SELECT p.id AS prebill_id, p.user_info, p.active, p.date, p.discount, 
                 c.id AS company_id, c.name AS company_name, c.phone AS company_phone
          FROM Prebills p
          JOIN Company c ON p.company_id = c.id
          ORDER BY p.date DESC
          LIMIT 15";

$result = $conn->query($query);

if ($result) {
    $bills = $result->fetch_all(MYSQLI_ASSOC);
    foreach ($bills as &$bill) {
        $prebill_id = $bill['prebill_id'];
        
        // Sellects every products on the curent bill
        $productQuery = "SELECT pr.name AS product_name, pr.price, pp.amount 
                         FROM Prebills_Products pp
                         JOIN Products pr ON pp.product_id = pr.id
                         WHERE pp.prebill_id = ?";
        
        $productStmt = $conn->prepare($productQuery);
        $productStmt->bind_param("i", $prebill_id);
        $productStmt->execute();
        $productResult = $productStmt->get_result();
        $products = $productResult->fetch_all(MYSQLI_ASSOC);
        $bill['products'] = $products;
    }
    
    // Loop through the bills and display them
    foreach ($bills as $bill) {
        echo "Bill ID: {$bill['prebill_id']}<br>";
        echo "User Info: {$bill['user_info']}<br>";
        echo "Company: {$bill['company_name']} ({$bill['company_phone']})<br>";
        echo "Discount: {$bill['discount']}%<br>";
        echo "Active: " . ($bill['active'] ? 'Yes' : 'No') . "<br>";
        echo "Date: {$bill['date']}<br>";
        echo "<strong>Products:</strong><br>";
        
        // Loop through the products for the current bill
        foreach ($bill['products'] as $product) {
            echo "- {$product['product_name']} (x{$product['amount']}) @ {$product['price']}€<br>";
        }
        echo "<hr>";
    }
} else {
    echo "No bills found.";
}
?>

    </div>

    <div id="manage" class="body-container">
        <p>Manage Content</p>
    </div>

    <div id="history" class="body-container">
        <p>History Content</p>
    </div>

    <div id="storage" class="body-container">
        <p>Storage Content</p>
    </div>

    <div id="info" class="body-container">
        <p>[User@archlinux ~]$ </p>
    </div>

    <script>
        function showSection(hash) {
            document.querySelectorAll('.body-container').forEach(div => {
                div.classList.remove('active');
            });

            if (hash) {
                let activeSection = document.querySelector(hash);
                if (activeSection) {
                    activeSection.classList.add('active');
                }
            }

            // Remove .selected from all nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('selected');
            });

            // Add .selected to the clicked link
            let activeLink = document.querySelector(`a[href="${hash}"]`);
            if (activeLink) {
                activeLink.classList.add('selected');
            }
        }

        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function (event) {
                event.preventDefault(); // Stop default jumping behavior
                let hash = this.getAttribute('href');
                history.pushState(null, null, hash); // Change URL without reloading
                showSection(hash);
            });
        });

        // Show the correct section when the page loads
        window.addEventListener('load', () => {
            showSection(window.location.hash || '#home');
        });

        // Handle back/forward navigation
        window.addEventListener('popstate', () => {
            showSection(window.location.hash);
        });
    </script>
</body>
</html>