<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$id = $_POST['id'];
$klantnaam = $_POST['klantnaam'];
$status = $_POST['status'];
$totaalprijs = $_POST['totaalprijs'];
$extra_info = $_POST['extra_info'];
$producten = $_POST['producten'];
$hoeveelheden = $_POST['hoeveelheden'];

// Begin transactie
$conn->begin_transaction();

try {
    // Update order details
    $stmt = $conn->prepare("UPDATE orders SET klantnaam = ?, status = ?, totaalprijs = ?, extra_info = ? WHERE id = ?");
    $stmt->bind_param('ssdsi', $klantnaam, $status, $totaalprijs, $extra_info, $id);
    $stmt->execute();

    // Delete existing order products
    $stmt = $conn->prepare("DELETE FROM order_products WHERE order_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // Insert new order products
    $stmt = $conn->prepare("INSERT INTO order_products (order_id, product_id, quantity) VALUES (?, ?, ?)");
    for ($i = 0; $i < count($producten); $i++) {
        $product_id = $producten[$i];
        $quantity = $hoeveelheden[$i];
        if ($quantity > 0) {
            $stmt->bind_param('iii', $id, $product_id, $quantity);
            $stmt->execute();
        }
    }

    // Commit transactie
    $conn->commit();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    // Rollback transactie
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
