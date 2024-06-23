<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

if (!isset($_GET['id'])) {
    die('Order ID is required.');
}

$order_id = $_GET['id'];
$order = getOrderById($order_id);

if (!$order) {
    die('Order not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $klantnaam = $_POST['klantnaam'];
    $status = $_POST['status'];
    $totaalprijs = $_POST['totaalprijs'];
    $extra_info = $_POST['extra_info'];

    $sql = "UPDATE orders SET klantnaam='$klantnaam', status='$status', totaalprijs='$totaalprijs', extra_info='$extra_info' WHERE id='$order_id'";
    if ($conn->query($sql) === TRUE) {
        header('Location: orders.php');
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
    <title>Order Bewerken</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Order Bewerken</h2>
    <a href="orders.php" class="btn btn-secondary mb-3">Terug naar Orders Beheer</a>
    <form method="post">
        <div class="form-group">
            <label for="klantnaam">Klantnaam</label>
            <input type="text" class="form-control" id="klantnaam" name="klantnaam" value="<?= htmlspecialchars($order['klantnaam']) ?>" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="Onderweg" <?= $order['status'] === 'Onderweg' ? 'selected' : '' ?>>Onderweg</option>
                <option value="Bestellen" <?= $order['status'] === 'Bestellen' ? 'selected' : '' ?>>Bestellen</option>
                <option value="Afgerond betaald" <?= $order['status'] === 'Afgerond betaald' ? 'selected' : '' ?>>Afgerond betaald</option>
                <option value="Afgehaald nog niet betaald" <?= $order['status'] === 'Afgehaald nog niet betaald' ? 'selected' : '' ?>>Afgehaald nog niet betaald</option>
            </select>
        </div>
        <div class="form-group">
            <label for="totaalprijs">Totaalprijs</label>
            <input type="number" class="form-control" id="totaalprijs" name="totaalprijs" step="0.01" value="<?= htmlspecialchars($order['totaalprijs']) ?>" required>
        </div>
        <div class="form-group">
            <label for="extra_info">Extra Info</label>
            <textarea class="form-control" id="extra_info" name="extra_info" rows="3"><?= htmlspecialchars($order['extra_info']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Opslaan</button>
    </form>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-3"><?= $error ?></div>
    <?php endif; ?>
</div>
</body>
</html>
