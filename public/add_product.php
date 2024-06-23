<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $omschrijving = $_POST['omschrijving'];
    $productsoort = $_POST['productsoort'];
    $prijs = $_POST['prijs'];

    // Foto uploaden
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["foto"]["name"]);
    move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);
    $foto = "uploads/" . basename($_FILES["foto"]["name"]);

    $sql = "INSERT INTO producten (omschrijving, productsoort, prijs, foto) VALUES ('$omschrijving', '$productsoort', '$prijs', '$foto')";
    if ($conn->query($sql) === TRUE) {
        echo "Product succesvol toegevoegd!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
