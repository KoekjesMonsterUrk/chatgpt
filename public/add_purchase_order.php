<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $producten = $_POST['producten'];
    $hoeveelheden = $_POST['hoeveelheden'];

    $totaalprijs = 0;

    foreach ($producten as $index => $product_id) {
        $quantity = $hoeveelheden[$index];
        if ($quantity > 0) {
            $stmt = $conn->prepare("SELECT inkoopprijs FROM producten WHERE id = ?");
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $totaalprijs += $quantity * $product['inkoopprijs'];
        }
    }

    $stmt = $conn->prepare("INSERT INTO bestellingen (status, totaalprijs) VALUES (?, ?)");
    $stmt->bind_param('sd', $status, $totaalprijs);
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        foreach ($producten as $index => $product_id) {
            $quantity = $hoeveelheden[$index];
            if ($quantity > 0) {
                $stmt = $conn->prepare("INSERT INTO bestelling_producten (bestelling_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->bind_param('iii', $order_id, $product_id, $quantity);
                $stmt->execute();
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Inkoop bestelling succesvol toegevoegd.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Er is iets misgegaan bij het toevoegen van de bestelling.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ongeldige invoer.']);
}
?>
