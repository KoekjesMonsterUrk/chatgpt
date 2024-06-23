<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $omschrijving = $_POST['omschrijving'];
    $productsoort = $_POST['productsoort'];
    $inkoopprijs = $_POST['inkoopprijs'];
    $prijs = $_POST['prijs'];
    $foto = $_FILES['foto'];

    // Handle file upload if a new file is uploaded
    if ($foto['size'] > 0) {
        $targetDir = "../uploads/";
        $targetFile = $targetDir . basename($foto["name"]);
        move_uploaded_file($foto["tmp_name"], $targetFile);
    } else {
        $targetFile = null; // No new file uploaded
    }

    $result = updateProduct($id, $omschrijving, $productsoort, $inkoopprijs, $prijs, $targetFile);

    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Er is iets misgegaan. Probeer het opnieuw.']);
    }
}
?>
