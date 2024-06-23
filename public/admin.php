<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

$orderStats = getOrderStats();
$totalOrders = getTotalOrders();
$totalAmount = getTotalAmount();
$totalShippingCosts = getTotalShippingCosts();
$quantitiesToOrder = getTotalQuantitiesToOrder();

$orders = getOrders();
$producten = getProducts();

function calculateTotalProfit($orders) {
    $totalProfit = 0;
    foreach ($orders as $order) {
        $orderProducts = getOrderProducts($order['id']);
        $orderInkoopprijs = 0;
        foreach ($orderProducts as $product) {
            $orderInkoopprijs += ($product['quantity'] * ($product['inkoopprijs'] + 5)); // Voeg 5 euro verzendkosten per product toe
        }
        $orderVerkoopprijs = $order['totaalprijs'];
        $totalProfit += ($orderVerkoopprijs - $orderInkoopprijs);
    }
    return $totalProfit;
}

$totalProfit = calculateTotalProfit($orders);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .navbar {
            background-color: #2e8b57;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .card-header {
            background-color: #2e8b57;
            color: white;
        }
        @media (max-width: 768px) {
            .navbar-nav .nav-item .nav-link {
                font-size: 1.2em;
            }
            .table-responsive {
                margin-bottom: 20px;
            }
        }
        .card-header {
            background-color: #2e8b57;
            color: white;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .container {
            max-width: 100%;
            padding: 0 15px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="#">Admin Panel</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="admin.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="products.php">Producten Beheren</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="orders.php">Orders Beheren</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="stock.php">Voorraad Beheren</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="purchase_orders.php">Inkoop Bestellingen Beheren <span class="sr-only">(current)</span></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Uitloggen</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Admin Dashboard</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Totaal Orders</div>
                <div class="card-body">
                    <?= $totalOrders ?>
                </div>
            </div>
        </div>

<div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <button class="btn btn-link text-white" type="button" data-toggle="collapse" data-target="#profitCollapse" aria-expanded="false" aria-controls="profitCollapse">
                        Totaal Winst
                    </button>
                </div>
                <div id="profitCollapse" class="collapse">
                    <div class="card-body">
                        Totale Winst: €<?= number_format($totalProfit, 2, ',', '.') ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

     <a href="https://Wa.me/310640169384?text=Order ID: 20
     Klantnaam: John Vd Broek
     Producten:
     West Red Xxl 650 (2)
     Verkoopprijs: €200,00
     " class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
             Chat with Us
                 </a>
    <button class="btn btn-primary mt-3" onclick="window.location.href='generate_pricelist.php'">Prijslijst Genereren</button>
    <button class="btn btn-success mt-3 btn-custom" onclick="window.location.href='orders.php?openModal=true'">Nieuwe Order Maken</button>
 
    <div class="table-responsive mt-5">




        <h3>Orders Status</h3>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Status</th>
                <th>Aantal Orders</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orderStats as $status => $count): ?>
                <tr>
                    <td><?= htmlspecialchars($status) ?></td>
                    <td><?= $count ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="table-responsive mt-5">
        <h3>Producten Bestellen</h3>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Product</th>
                <th>Aantal</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($quantitiesToOrder as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['omschrijving']) ?></td>
                    <td><?= $product['total_quantity'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <button class="btn btn-primary mt-3" id="maakBestellingBtn">Maak Bestelling</button>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#maakBestellingBtn').click(function() {
            $.ajax({
                url: 'create_purchase_order.php',
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        alert('Bestelling succesvol aangemaakt.');
                        location.reload();
                    } else {
                        alert('Er is iets misgegaan. Probeer het opnieuw.');
                    }
                },
                error: function() {
                    alert('Er is iets misgegaan. Probeer het opnieuw.');
                }
            });
        });
    });
</script>
</body>
</html>
