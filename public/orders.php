<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

$orders = getOrders();
$producten = getProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders Beheren</title>
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
        .filter-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .filter-group .form-group {
            margin-bottom: 0;
        }
        @media (max-width: 768px) {
            .navbar-nav .nav-item .nav-link {
                font-size: 1.2em;
            }
            .table-responsive {
                margin-bottom: 20px;
            }
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-group .form-group {
                margin-bottom: 10px;
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

<div class="container mt-5">
    <h2>Orders Beheren</h2>
    <div class="filter-group">
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addOrderModal">Nieuwe Order</button>
        <div class="form-group">
            <input class="form-control" id="searchInput" type="text" placeholder="Zoeken...">
        </div>
        <div class="form-group">
            <div class="form-check form-check-inline">
                <input class="form-check-input status-filter" type="checkbox" id="statusOnderweg" value="Onderweg" checked>
                <label class="form-check-label" for="statusOnderweg">Onderweg</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input status-filter" type="checkbox" id="statusBestellen" value="Bestellen" checked>
                <label class="form-check-label" for="statusBestellen">Bestellen</label>
            </div>
                        <div class="form-check form-check-inline">
                <input class="form-check-input status-filter" type="checkbox" id="statusAfgehaaldNietBetaald" value="Afgehaald nog niet betaald">
                <label class="form-check-label" for="statusAfgehaaldNietBetaald">Afgehaald nog niet betaald</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input status-filter" type="checkbox" id="statusAfgerondBetaald" value="Afgerond betaald">
                <label class="form-check-label" for="statusAfgerondBetaald">Afgerond betaald</label>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered" id="ordersTable">
            <thead>
                <tr>
                    <th class=sticky">ID</th>
                    <th>Klantnaam</th>
                    <th>Status</th>
                    <th>Inkoopprijs</th>
                    <th>Verkoopprijs</th>
                    <th>Producten</th>
                    <th>Extra Info</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): 
                $orderProducts = getOrderProducts($order['id']);
                $inkoopprijs = 0;
                foreach ($orderProducts as $product) {
                    $inkoopprijs += ($product['quantity'] * ($product['inkoopprijs'] + 5)); // Voeg 5 euro verzendkosten per product toe
                }
            ?>
                <tr>
                    <td class="sticky"><?= $order['id'] ?></td>
                    <td class="sticky"><?= htmlspecialchars($order['klantnaam']) ?></td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td>€<?= number_format($inkoopprijs, 2, ',', '.') ?></td>
                    <td>€<?= number_format($order['totaalprijs'], 2, ',', '.') ?></td>
                    <td>
                        <?php foreach ($orderProducts as $product): ?>
                            <?= htmlspecialchars($product['omschrijving']) ?> (<?= $product['quantity'] ?>)<br>
                        <?php endforeach; ?>
                    </td>
                    <td><?= htmlspecialchars($order['extra_info']) ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-order-btn"
                                data-id="<?= $order['id'] ?>"
                                data-klantnaam="<?= htmlspecialchars($order['klantnaam']) ?>"
                                data-status="<?= htmlspecialchars($order['status']) ?>"
                                data-products='<?= json_encode($orderProducts, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>'
                                data-totaalprijs="<?= $order['totaalprijs'] ?>"
                                data-extra_info="<?= htmlspecialchars($order['extra_info']) ?>">Bewerken</button>
                        <button class="btn btn-danger btn-sm delete-order-btn"
                                data-id="<?= $order['id'] ?>">Verwijderen</button>
                        <button class="btn btn-info btn-sm share-order-btn"
                                data-id="<?= $order['id'] ?>"
                                data-klantnaam="<?= htmlspecialchars($order['klantnaam'], ENT_QUOTES, 'UTF-8') ?>"
                                data-totaalprijs="€<?= number_format($order['totaalprijs'], 2, ',', '.') ?>"
                                data-products='<?php foreach ($orderProducts as $product) {
                                    echo htmlspecialchars($product['omschrijving'], ENT_QUOTES, 'UTF-8') . " (" . $product['quantity'] . ")\\n";
                                } ?>'>Deel</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal voor het toevoegen van een nieuwe bestelling -->
<div class="modal fade" id="addOrderModal" tabindex="-1" role="dialog" aria-labelledby="addOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOrderModalLabel">Nieuwe Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addOrderForm" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addKlantnaam">Klantnaam</label>
                        <input type="text" class="form-control" id="addKlantnaam" name="klantnaam" required>
                    </div>
                    <div class="form-group">
                        <label for="addStatus">Status</label>
                        <select class="form-control" id="addStatus" name="status" required>
                            <option value="Onderweg">Onderweg</option>
                            <option value="Bestellen">Bestellen</option>
                            <option value="Afgerond betaald">Afgerond betaald</option>
                            <option value="Afgehaald nog niet betaald">Afgehaald nog niet betaald</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addTotaalprijs">Totaalprijs</label>
                        <input type="number" class="form-control" id="addTotaalprijs" name="totaalprijs" step="0.01" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="addExtraInfo">Extra Info</label>
                        <textarea class="form-control" id="addExtraInfo" name="extra_info"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="addProducten">Producten</label>
                        <?php foreach ($producten as $product): ?>
                        <div class="form-group">
                            <label><?= htmlspecialchars($product['omschrijving']) ?> (€<?= number_format($product['prijs'], 2, ',', '.') ?>)</label>
                            <input type="hidden" name="producten[]" value="<?= $product['id'] ?>" data-prijs="<?= $product['prijs'] ?>">
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

<!-- Modal voor het bewerken van een bestelling -->
<div class="modal fade" id="editOrderModal" tabindex="-1" role="dialog" aria-labelledby="editOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editOrderModalLabel">Order Bewerken</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editOrderForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="editOrderId" name="id">
                    <div class="form-group">
                        <label for="editKlantnaam">Klantnaam</label>
                        <input type="text" class="form-control" id="editKlantnaam" name="klantnaam" required>
                    </div>
                    <div class="form-group">
                        <label for="editStatus">Status</label>
                        <select class="form-control" id="editStatus" name="status" required>
                            <option value="Onderweg">Onderweg</option>
                            <option value="Bestellen">Bestellen</option>
                            <option value="Afgerond betaald">Afgerond betaald</option>
                            <option value="Afgehaald nog niet betaald">Afgehaald nog niet betaald</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editTotaalprijs">Totaalprijs</label>
                        <input type="number" class="form-control" id="editTotaalprijs" name="totaalprijs" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="editExtraInfo">Extra Info</label>
                        <textarea class="form-control" id="editExtraInfo" name="extra_info"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editProducten">Producten</label>
                        <div id="editProductList">
                            <?php foreach ($producten as $product): ?>
                            <div class="form-group">
                                <label><?= htmlspecialchars($product['omschrijving']) ?></label>
                                <input type="hidden" name="producten[]" value="<?= $product['id'] ?>" data-prijs="<?= $product['prijs'] ?>">
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

<!-- Modal voor het verwijderen van een bestelling -->
<div class="modal fade" id="deleteOrderModal" tabindex="-1" role="dialog" aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteOrderModalLabel">Order Verwijderen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteOrderForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="deleteOrderId" name="id">
                    <p>Weet je zeker dat je deze order wilt verwijderen?</p>
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
        // Open edit order modal
        $(document).on('click', '.edit-order-btn', function() {
            var button = $(this);
            var id = button.data('id');
            var klantnaam = button.data('klantnaam');
            var status = button.data('status');
            var products = JSON.parse(button.attr('data-products'));
            var totaalprijs = button.data('totaalprijs');
            var extra_info = button.data('extra_info');

            var modal = $('#editOrderModal');
            modal.find('#editOrderId').val(id);
            modal.find('#editKlantnaam').val(klantnaam);
            modal.find('#editStatus').val(status);
            modal.find('#editTotaalprijs').val(totaalprijs);
            modal.find('#editExtraInfo').val(extra_info);

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

        // Open delete order modal
        $(document).on('click', '.delete-order-btn', function() {
            var button = $(this);
            var id = button.data('id');

            var modal = $('#deleteOrderModal');
            modal.find('#deleteOrderId').val(id);
            modal.modal('show');
        });

        $('#addOrderForm').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: 'add_order.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert('Er is iets misgegaan. Probeer het opnieuw.');
                }
            });
        });

        $('#editOrderForm').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: 'update_order.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert('Er is iets misgegaan. Probeer het opnieuw.');
                }
            });
        });

        $('#deleteOrderForm').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: 'delete_order.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert('Er is iets misgegaan. Probeer het opnieuw.');
                }
            });
        });

        $('#addOrderForm, #editOrderForm').on('input', '.product-quantity', function() {
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

        // Filter logic
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#ordersTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $('.status-filter').on('change', function() {
            var selectedStatuses = $('.status-filter:checked').map(function() {
                return this.value;
            }).get();

            if (selectedStatuses.length === 0) {
                $('#ordersTable tbody tr').show();
            } else {
                $('#ordersTable tbody tr').each(function() {
                    var row = $(this);
                    var status = row.find('td:nth-child(3)').text();
                    if (selectedStatuses.includes(status)) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });
            }
        });

        // Check if the URL contains the openModal parameter and open the modal if present
        if (window.location.search.indexOf('openModal=true') !== -1) {
            $('#addOrderModal').modal('show');
        }

        // Deel knoppen
        $(document).on('click', '.share-order-btn', function() {
            var button = $(this);
            var orderId = button.data('id');
            var klantnaam = button.data('klantnaam');
            var totaalprijs = button.data('totaalprijs');
            var products = button.data('products').replace(/\\n/g, '\n');

            var shareText = "Order ID: " + orderId + "\n" +
                            "Klantnaam: " + klantnaam + "\n" +
                            "Producten:\n" + products + "\n" +
                            "Verkoopprijs: " + totaalprijs;

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


            function filterOrders() {
            var selectedStatuses = $('.status-filter:checked').map(function() {
                return $(this).val();
            }).get();

            $('#ordersTable tbody tr').each(function() {
                var status = $(this).find('td:nth-child(3)').text();
                if (selectedStatuses.includes(status)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Initial filter on page load
        filterOrders();
    });
</script>
</body>
</html>
