<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderId = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM bestellingen WHERE id = ?");
    $stmt->bind_param('i', $orderId);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Bestelling succesvol verwijderd!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Er is een fout opgetreden bij het verwijderen van de bestelling.']);
    }
}
?>
