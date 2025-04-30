<?php
require('fpdf/fpdf.php');

if (isset($_GET['id'])) {
    $prebill_id = intval($_GET['id']);

    $conn = new mysqli("localhost", "root", "", "predracuni_db");
    if ($conn->connect_error) {
        die("Connection failed");
    }

    $prebillStmt = $conn->prepare("
        SELECT p.id, p.date, p.user_info
        FROM Prebills p
        WHERE p.id = ?
    ");
    $prebillStmt->bind_param("i", $prebill_id);
    $prebillStmt->execute();
    $prebillResult = $prebillStmt->get_result()->fetch_assoc();

    $date = new DateTime($prebillResult['date']);
    $formattedDate = $date->format('F j, Y');

    $productStmt = $conn->prepare("
        SELECT pr.name, pr.description, pr.price, pbp.amount
        FROM prebills_products pbp
        JOIN products pr ON pbp.product_id = pr.id
        WHERE pbp.prebill_id = ?
    ");
    $productStmt->bind_param("i", $prebill_id);
    $productStmt->execute();
    $products = $productStmt->get_result();

    // Generate PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,"Prebill #{$prebill_id}",0,1);
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(0,10,"Created at: " . $formattedDate,0,1);
    $pdf->Cell(0,10,"Buyer: " . $prebillResult['user_info'],0,1);
    $pdf->Ln(10);

    // Table Header
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(60,10,"Product",1);
    $pdf->Cell(30,10,"Price",1);
    $pdf->Cell(30,10,"Quantity",1);
    $pdf->Cell(30,10,"Total",1);
    $pdf->Ln();

    $pdf->SetFont('Arial','',12);
    $totalSum = 0;
    while ($row = $products->fetch_assoc()) {
        $name = $row['name'];
        $price = $row['price'];
        $qty = $row['amount'];
        $lineTotal = $price * $qty;
        $totalSum += $lineTotal;

        $pdf->Cell(60,10,$name,1);
        $pdf->Cell(30,10,number_format($price, 2) . '$',1);
        $pdf->Cell(30,10,$qty,1);
        $pdf->Cell(30,10,number_format($lineTotal, 2) . '$',1);
        $pdf->Ln();
    }

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(120,10,"Total",1);
    $pdf->Cell(30,10,number_format($totalSum, 2) . '$',1);
    $pdf->Ln();

    $pdf->Output("D", "prebill_{$prebill_id}.pdf");
}
?>