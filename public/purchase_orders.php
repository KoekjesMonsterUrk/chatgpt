<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

$purchaseOrders = getPurchaseOrders(); // Zorg ervoor dat je een functie hebt die de inkoopbestellingen ophaalt
$producten = getProducts(); // Alle producten voor gebruik in de bewerkingsmodal

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inkoop Bestellingen Beheren</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
        .container {
            margin-top: 20px;
        }
        @media (max-width: 768px) {
            .navbar-nav .nav-item .nav-link {
                font-size: 1.2em;
            }
            .table-responsive {
                margin-bottom: 20px;
            }
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

<div class="container">
    <h2>Inkoop Bestellingen Beheren</h2>
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addPurchaseOrderModal">Nieuwe Inkoop Bestelling</button>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Status</th>
                    <th>Totaalprijs</th>
                    <th>Producten</th>
                    <th>Verzendkosten</th>
                    <th>Totaal order</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($purchaseOrders as $order): 
                $orderProducts = getPurchaseOrderProducts($order['id']);
                $verzendkosten = 0;
                $inkoopprijs = 0;
                foreach ($orderProducts as $product) {
                    if ($product['quantity'] > 0) {
                        $verzendkosten += $product['quantity'] * 5;
                        $inkoopprijs += $product['quantity'] * $product['inkoopprijs'];
                    }
                }
                $totaalOrder = $inkoopprijs + $verzendkosten;
            ?>
                <tr>
                    <td><?= htmlspecialchars($order['datum']) ?></td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td>€<?= number_format($inkoopprijs, 2, ',', '.') ?></td>
                    <td>
                        <?php foreach ($orderProducts as $product): ?>
                            <?php if ($product['quantity'] > 0): ?>
                                <?= htmlspecialchars($product['omschrijving']) ?> (<?= $product['quantity'] ?>)<br>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </td>
                    <td>€<?= number_format($verzendkosten, 2, ',', '.') ?></td>
                    <td>€<?= number_format($totaalOrder, 2, ',', '.') ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-order-btn"
                                data-id="<?= $order['id'] ?>"
                                data-status="<?= htmlspecialchars($order['status']) ?>"
                                data-products='<?= json_encode($orderProducts, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>'>Bewerken</button>
                        <button class="btn btn-danger btn-sm delete-order-btn"
                                data-id="<?= $order['id'] ?>">Verwijderen</button>
                        <button class="btn btn-info btn-sm share-order-btn"
                                data-id="<?= $order['id'] ?>"
                                data-products='<?php foreach ($orderProducts as $product) {
                                    if ($product['quantity'] > 0) {
                                        echo htmlspecialchars($product['omschrijving']) . " (" . $product['quantity'] . ")\\n";
                                    }
                                } ?>'
                                data-totaalprijs="€<?= number_format($inkoopprijs, 2, ',', '.') ?>"
                                data-verzendkosten="€<?= number_format($verzendkosten, 2, ',', '.') ?>"
                                data-totaalorder="€<?= number_format($totaalOrder, 2, ',', '.') ?>">Deel</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal voor het toevoegen van een nieuwe inkoop bestelling -->
<div class="modal fade" id="addPurchaseOrderModal" tabindex="-1" role="dialog" aria-labelledby="addPurchaseOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPurchaseOrderModalLabel">Nieuwe Inkoop Bestelling</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addPurchaseOrderForm" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addStatus">Status</label>
                        <select class="form-control" id="addStatus" name="status" required>
                            <option value="Nieuw">Nieuw</option>
                            <option value="Besteld">Besteld</option>
                            <option value="Ontvangen">Ontvangen</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addProducten">Producten</label>
                        <?php foreach ($producten as $product): ?>
                        <div class="form-group">
                            <label><?= htmlspecialchars($product['omschrijving']) ?> (€<?= number_format($product['inkoopprijs'], 2, ',', '.') ?>)</label>
                            <input type="hidden" name="producten[]" value="<?= $product['id'] ?>" data-prijs="<?= $product['inkoopprijs'] ?>">
                            <input type="number" class="form-control product-quantity" name="hoeveelheden[]" value="0" min="0" required>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-primary">Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal voor het bewerken van een inkoop bestelling -->
<div class="modal fade" id="editPurchaseOrderModal" tabindex="-1" role="dialog" aria-labelledby="editPurchaseOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPurchaseOrderModalLabel">Inkoop Bestelling Bewerken</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editPurchaseOrderForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="editOrderId" name="id">
                    <div class="form-group">
                        <label for="editStatus">Status</label>
                        <select class="form-control" id="editStatus" name="status" required>
                            <option value="Nieuw">Nieuw</option>
                            <option value="Besteld">Besteld</option>
                            <option value="Ontvangen">Ontvangen</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editProducten">Producten</label>
                        <div id="editProductList">
                            <?php foreach ($producten as $product): ?>
                            <div class="form-group">
                                <label><?= htmlspecialchars($product['omschrijving']) ?></label>
                                <input type="hidden" name="producten[]" value="<?= $product['id'] ?>" data-prijs="<?= $product['inkoopprijs'] ?>">
                                <input type="number" class="form-control product-quantity" name="hoeveelheden[]" value="0" min="0">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-primary">Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal voor het verwijderen van een inkoop bestelling -->
<div class="modal fade" id="deletePurchaseOrderModal" tabindex="-1" role="dialog" aria-labelledby="deletePurchaseOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePurchaseOrderModalLabel">Inkoop Bestelling Verwijderen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deletePurchaseOrderForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="deleteOrderId" name="id">
                    <p>Weet je zeker dat je deze inkoop bestelling wilt verwijderen?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-danger">Verwijderen</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Open edit purchase order modal
    $(document).on('click', '.edit-order-btn', function() {
        var button = $(this);
        var id = button.data('id');
        var status = button.data('status');
        var products = JSON.parse(button.attr('data-products'));

        var modal = $('#editPurchaseOrderModal');
        modal.find('#editOrderId').val(id);
        modal.find('#editStatus').val(status);

        var productList = modal.find('#editProductList');
        productList.find('.product-quantity').each(function() {
            $(this).val(0); // Reset the quantity to 0
        });

        products.forEach(function(product) {
            var productId = product.product_id;
            var quantity = product.quantity;
            productList.find('input[name="producten[]"][value="' + productId + '"]').next('.product-quantity').val(quantity);
        });

        modal.modal('show');
    });

    // Open delete purchase order modal
    $(document).on('click', '.delete-order-btn', function() {
        var button = $(this);
        var id = button.data('id');

        var modal = $('#deletePurchaseOrderModal');
        modal.find('#deleteOrderId').val(id);
        modal.modal('show');
    });

    $('#addPurchaseOrderForm').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: 'add_purchase_order.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    location.reload();
                } else {
                    console.error('Error:', response.message);
                    alert(response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                alert('Er is iets misgegaan. Probeer het opnieuw.');
            }
        });
    });

    $('#editPurchaseOrderForm').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: 'update_purchase_order.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    location.reload();
                } else {
                    console.error('Error:', response.message);
                    alert(response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                alert('Er is iets misgegaan. Probeer het opnieuw.');
            }
        });
    });

    $('#deletePurchaseOrderForm').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: 'delete_purchase_order.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    location.reload();
                } else {
                    console.error('Error:', response.message);
                    alert(response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                alert('Er is iets misgegaan. Probeer het opnieuw.');
            }
        });
    });

    $('#addPurchaseOrderForm, #editPurchaseOrderForm').on('input', '.product-quantity', function() {
        var modal = $(this).closest('.modal');
        calculateTotalPrice(modal);
    });

    function calculateTotalPrice(modal) {
        let total = 0;
        modal.find('.product-quantity').each(function() {
            let quantity = $(this).val();
            let price = $(this).prev('input').data('prijs');
            total += quantity * price;
        });
        modal.find('input[name="totaalprijs"]').val(total.toFixed(2));
    }

    // Share order information
$(document).on('click', '.share-order-btn', function() {
    var button = $(this);
    var products = button.data('products').replace(/\\n/g, '\n');
    var totaalprijs = button.data('totaalprijs');
    var verzendkosten = button.data('verzendkosten');
    var totaalorder = button.data('totaalorder');

    // Calculate the total number of products
    var totalProducts = 0;
    button.data('products').split('\\n').forEach(function(product) {
        if (product) {
            var quantity = product.match(/\((\d+)\)/);
            if (quantity && quantity[1]) {
                totalProducts += parseInt(quantity[1]);
            }
        }
    });

    var shareText = "Aantal producten: " + totalProducts + "\n" +
                    "Producten:\n" + products + "\n" +
                    "Totaalprijs: " + totaalprijs + "\n" +
                    "Verzendkosten: " + verzendkosten + "\n" +
                    "Totaal order: " + totaalorder;

    if (navigator.share) {
        navigator.share({
            title: 'Order Details',
            text: shareText,
        }).then(() => {
            console.log('Deelactie geslaagd');
        }).catch((error) => {
            console.log('Deelactie mislukt', error);
        });
    } else {
        // Fallback for WebView
        var fallbackShare = document.createElement('textarea');
        fallbackShare.value = shareText;
        document.body.appendChild(fallbackShare);
        fallbackShare.select();
        document.execCommand('copy');
        document.body.removeChild(fallbackShare);
        alert('Deel API wordt niet ondersteund in deze browser. Details zijn gekopieerd naar het klembord.');
    }
});

});
</script>
</body>
</html>
