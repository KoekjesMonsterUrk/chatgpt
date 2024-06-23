<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

$product_id = $_GET['id'];
$product = getProductById($product_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $omschrijving = $_POST['omschrijving'];
    $productsoort = $_POST['productsoort'];
    $prijs = $_POST['prijs'];
    $foto = $_FILES['foto']['name'];

    if ($foto) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($foto);
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);
        $sql = "UPDATE producten SET omschrijving='$omschrijving', productsoort='$productsoort', prijs='$prijs', foto='$foto' WHERE id='$product_id'";
    } else {
        $sql = "UPDATE producten SET omschrijving='$omschrijving', productsoort='$productsoort', prijs='$prijs' WHERE id='$product_id'";
    }

    if ($conn->query($sql) === TRUE) {
        header('Location: products.php');
        exit();
    } else {
        $error = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Bewerken</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Product Bewerken</h2>
    <a href="products.php" class="btn btn-secondary mb-3">Terug naar Producten Beheer</a>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="omschrijving">Omschrijving</label>
            <input type="text" class="form-control" id="omschrijving" name="omschrijving" value="<?= $product['omschrijving'] ?>" required>
        </div>
        <div class="form-group">
            <label for="productsoort">Productsoort</label>
            <select class="form-control" id="productsoort" name="productsoort" required>
                <option value="Tabak" <?= $product['productsoort'] === 'Tabak' ? 'selected' : '' ?>>Tabak</option>
                <option value="Sigaretten" <?= $product['productsoort'] === 'Sigaretten' ? 'selected' : '' ?>>Sigaretten</option>
            </select>
        </div>
        <div class="form-group">
            <label for="prijs">Prijs</label>
            <input type="number" class="form-control" id="prijs" name="prijs" step="0.01" value="<?= $product['prijs'] ?>" required>
        </div>
        <div class="form-group">
            <label for="foto">Foto</label>
            <input type="file" class="form-control" id="foto" name="foto">
            <?php if ($product['foto']): ?>
                <img src="../uploads/<?= $product['foto'] ?>" style="max-width: 100px;" class="mt-2">
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Opslaan</button>
    </form>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-3"><?= $error ?></div>
    <?php endif; ?>
</div>
</body>
</html>