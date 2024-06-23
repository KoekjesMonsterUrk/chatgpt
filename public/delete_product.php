<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $result = deleteProduct($id);

    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Er is iets misgegaan. Probeer het opnieuw.']);
    }
}
?>
