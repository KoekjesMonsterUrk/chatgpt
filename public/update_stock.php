<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['id'];
    $newStock = $_POST['voorraad'];

    if (updateProductStock($productId, $newStock)) {
        echo json_encode(['status' => 'success', 'message' => 'Voorraad succesvol bijgewerkt!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Er is een fout opgetreden bij het bijwerken van de voorraad.']);
    }
}
?>
