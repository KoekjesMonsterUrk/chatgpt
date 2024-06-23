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
    <title>Producten Beheren</title>
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

<div class="container mt-5">
    <h2>Producten Beheren</h2>
    <input class="form-control mb-3" id="searchInput" type="text" placeholder="Zoeken...">
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addProductModal">Nieuw Product</button>
    <div class="table-responsive">
        <table class="table table-bordered" id="productsTable">
            <thead>
            <tr>
                <th>Omschrijving</th>
                <th>Productsoort</th>
                <th>Inkoopprijs</th>
                <th>Verkoopprijs</th>
                <th>Foto</th>
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
                    <td><img src="<?= htmlspecialchars($product['foto']) ?>" alt="Product Foto" style="width: 50px; height: auto;"></td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-product-btn"
                                data-id="<?= $product['id'] ?>"
                                data-omschrijving="<?= htmlspecialchars($product['omschrijving']) ?>"
                                data-productsoort="<?= htmlspecialchars($product['productsoort']) ?>"
                                data-inkoopprijs="<?= htmlspecialchars($product['inkoopprijs']) ?>"
                                data-prijs="<?= htmlspecialchars($product['prijs']) ?>"
                                data-foto="<?= htmlspecialchars($product['foto']) ?>">Bewerken</button>
                        <button class="btn btn-danger btn-sm delete-product-btn"
                                data-id="<?= $product['id'] ?>">Verwijderen</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal voor het toevoegen van een nieuw product -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Nieuw Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addProductForm" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addOmschrijving">Omschrijving</label>
                        <input type="text" class="form-control" id="addOmschrijving" name="omschrijving" required>
                    </div>
                    <div class="form-group">
                        <label for="addProductsoort">Productsoort</label>
                        <select class="form-control" id="addProductsoort" name="productsoort" required>
                            <option value="Tabak">Tabak</option>
                            <option value="Sigaretten">Sigaretten</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addInkoopprijs">Inkoopprijs</label>
                        <input type="number" class="form-control" id="addInkoopprijs" name="inkoopprijs" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="addPrijs">Verkoopprijs</label>
                        <input type="number" class="form-control" id="addPrijs" name="prijs" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="addFoto">Foto</label>
                        <input type="file" class="form-control-file" id="addFoto" name="foto" required>
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

<!-- Modal voor het bewerken van een product -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Product Bewerken</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editProductForm" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="editProductId" name="id">
                    <div class="form-group">
                        <label for="editOmschrijving">Omschrijving</label>
                        <input type="text" class="form-control" id="editOmschrijving" name="omschrijving" required>
                    </div>
                    <div class="form-group">
                        <label for="editProductsoort">Productsoort</label>
                        <select class="form-control" id="editProductsoort" name="productsoort" required>
                            <option value="Tabak">Tabak</option>
                            <option value="Sigaretten">Sigaretten</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editInkoopprijs">Inkoopprijs</label>
                        <input type="number" class="form-control" id="editInkoopprijs" name="inkoopprijs" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="editPrijs">Verkoopprijs</label>
                        <input type="number" class="form-control" id="editPrijs" name="prijs" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="editFoto">Foto</label>
                        <input type="file" class="form-control-file" id="editFoto" name="foto">
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
        // Open edit product modal
        $(document).on('click', '.edit-product-btn', function() {
            var button = $(this);
            var id = button.data('id');
            var omschrijving = button.data('omschrijving');
            var productsoort = button.data('productsoort');
            var inkoopprijs = button.data('inkoopprijs');
            var prijs = button.data('prijs');
            var foto = button.data('foto');

            var modal = $('#editProductModal');
            modal.find('#editProductId').val(id);
            modal.find('#editOmschrijving').val(omschrijving);
            modal.find('#editProductsoort').val(productsoort);
            modal.find('#editInkoopprijs').val(inkoopprijs);
            modal.find('#editPrijs').val(prijs);

            modal.modal('show');
        });

        // Handle add product form submission
        $('#addProductForm').submit(function(event) {
            event.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: 'add_product.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert('Er is iets misgegaan. Probeer het opnieuw.');
                }
            });
        });

        // Handle edit product form submission
        $('#editProductForm').submit(function(event) {
            event.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: 'update_product.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert('Er is iets misgegaan. Probeer het opnieuw.');
                }
            });
        });

        // Handle delete product button click
        $(document).on('click', '.delete-product-btn', function() {
            if (confirm('Weet je zeker dat je dit product wilt verwijderen?')) {
                var button = $(this);
                var id = button.data('id');

                $.ajax({
                    url: 'delete_product.php',
                    type: 'POST',
                    data: {id: id},
                    success: function(response) {
                        location.reload();
                    },
                    error: function() {
                        alert('Er is iets misgegaan. Probeer het opnieuw.');
                    }
                });
            }
        });

        // Search function
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#productsTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
</body>
</html>
