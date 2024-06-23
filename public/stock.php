<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

$producten = getProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voorraad Beheren</title>
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
    <h2>Voorraad Beheren</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Omschrijving</th>
                    <th>Productsoort</th>
                    <th>Inkoopprijs</th>
                    <th>Verkoopprijs</th>
                    <th>Voorraad</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($producten as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['omschrijving']) ?></td>
                    <td><?= htmlspecialchars($product['productsoort']) ?></td>
                    <td>€<?= number_format($product['inkoopprijs'], 2, ',', '.') ?></td>
                    <td>€<?= number_format($product['prijs'], 2, ',', '.') ?></td>
                    <td><?= $product['voorraad'] ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-stock-btn"
                                data-id="<?= $product['id'] ?>"
                                data-voorraad="<?= htmlspecialchars($product['voorraad']) ?>">Bijwerken</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal voor het bijwerken van voorraad -->
<div class="modal fade" id="editStockModal" tabindex="-1" role="dialog" aria-labelledby="editStockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStockModalLabel">Voorraad Bijwerken</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editStockForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="editProductId" name="id">
                    <div class="form-group">
                        <label for="editVoorraad">Voorraad</label>
                        <input type="number" class="form-control" id="editVoorraad" name="voorraad" required>
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

<script>
    $(document).ready(function() {
        // Open edit stock modal
        $(document).on('click', '.edit-stock-btn', function() {
            var button = $(this);
            var id = button.data('id');
            var voorraad = button.data('voorraad');

            var modal = $('#editStockModal');
            modal.find('#editProductId').val(id);
            modal.find('#editVoorraad').val(voorraad);

            modal.modal('show');
        });

        $('#editStockForm').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: 'update_stock.php',
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
    });
</script>
</body>
</html>
