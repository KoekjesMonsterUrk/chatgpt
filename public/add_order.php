<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $klantnaam = $_POST['klantnaam'];
    $status = $_POST['status'];
    $totaalprijs = $_POST['totaalprijs'];
    $extra_info = $_POST['extra_info'];
    $producten = $_POST['producten'];
    $hoeveelheden = $_POST['hoeveelheden'];

    $sql = "INSERT INTO orders (klantnaam, status, totaalprijs, extra_info) VALUES ('$klantnaam', '$status', '$totaalprijs', '$extra_info')";
    if ($conn->query($sql) === TRUE) {
        $order_id = $conn->insert_id;
        for ($i = 0; $i < count($producten); $i++) {
            if ($hoeveelheden[$i] > 0) {
                $product_id = $producten[$i];
                $quantity = $hoeveelheden[$i];
                $sql = "INSERT INTO order_products (order_id, product_id, quantity) VALUES ('$order_id', '$product_id', '$quantity')";
                $conn->query($sql);
            }
        }
        echo json_encode(['status' => 'success', 'message' => 'Order succesvol toegevoegd!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $sql . '<br>' . $conn->error]);
    }

    $conn->close();
}
?>
