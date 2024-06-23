<?php
require_once 'db.php';

function getOrderById($id) {
    global $conn;
    $sql = "SELECT * FROM orders WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

function getCustomers() {
    global $conn;
    $sql = "SELECT * FROM customers ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

function getPurchaseOrders() {
    global $conn;
    $sql = "SELECT * FROM bestellingen"; 
    $result = $conn->query($sql);
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    return $orders;
}

function getProducts() {
    global $conn;
    $sql = "SELECT * FROM producten";
    $result = $conn->query($sql);
    $producten = [];
    while ($row = $result->fetch_assoc()) {
        $producten[] = $row;
    }
    return $producten;
}


function getPurchaseOrderProducts($orderId) {
    global $conn;
    $stmt = $conn->prepare("SELECT bp.*, p.omschrijving, p.inkoopprijs FROM bestelling_producten bp JOIN producten p ON bp.product_id = p.id WHERE bp.bestelling_id = ?");
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    return $products;
}

function updateOrderStatus($orderId, $status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE bestellingen SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $orderId);
    return $stmt->execute();
}

function addOrder($status, $totaalprijs) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO bestellingen (status, totaalprijs) VALUES (?, ?)");
    $stmt->bind_param('sd', $status, $totaalprijs);
    return $stmt->execute();
}

function addOrderProduct($orderId, $productId, $quantity) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO bestelling_producten (bestelling_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param('iii', $orderId, $productId, $quantity);
    return $stmt->execute();
}

function updateStockOnReceive($orderId) {
    global $conn;
    $products = getOrderProducts($orderId);
    foreach ($products as $product) {
        $stmt = $conn->prepare("UPDATE producten SET voorraad = voorraad + ? WHERE id = ?");
        $stmt->bind_param('ii', $product['quantity'], $product['product_id']);
        $stmt->execute();
    }
}

function getOrderProducts($orderId) {
    global $conn;
    $stmt = $conn->prepare("SELECT bp.*, p.omschrijving, p.inkoopprijs, p.prijs FROM order_products bp JOIN producten p ON bp.product_id = p.id WHERE bp.order_id = ?");
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    return $products;
}

function updateProductStock($productId, $newStock) {
    global $conn;
    $stmt = $conn->prepare("UPDATE producten SET voorraad = ? WHERE id = ?");
    $stmt->bind_param('ii', $newStock, $productId);
    return $stmt->execute();
}

function getTotalShippingCosts() {
    global $conn;
    $shippingCostPerProduct = 5; // Verzendkosten per product

    $sql = "SELECT SUM(op.quantity) as total_quantity
            FROM order_products op
            JOIN orders o ON op.order_id = o.id
            WHERE o.status IN ('Onderweg', 'Bestellen', 'Afgerond betaald', 'Afgehaald nog niet betaald')";
    $result = $conn->query($sql);
    $totalQuantity = 0;
    if ($row = $result->fetch_assoc()) {
        $totalQuantity = $row['total_quantity'];
    }

    return $totalQuantity * $shippingCostPerProduct;
}

function getOrders() {
    global $conn;
    $sql = "SELECT o.*, op.product_id, p.omschrijving, op.quantity
            FROM orders o
            LEFT JOIN order_products op ON o.id = op.order_id
            LEFT JOIN producten p ON op.product_id = p.id";
    $result = $conn->query($sql);
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $order_id = $row['id'];
        if (!isset($orders[$order_id])) {
            $orders[$order_id] = [
                'id' => $row['id'],
                'klantnaam' => $row['klantnaam'],
                'status' => $row['status'],
                'totaalprijs' => $row['totaalprijs'],
                'extra_info' => $row['extra_info'],
                'products' => []
            ];
        }
        if ($row['product_id']) {
            $orders[$order_id]['products'][] = [
                'product_id' => $row['product_id'],
                'omschrijving' => $row['omschrijving'],
                'quantity' => $row['quantity']
            ];
        }
    }
    return $orders;
}
function getOrderStats() {
    global $conn;
    $sql = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
    $result = $conn->query($sql);
    $stats = [
        'Onderweg' => 0,
        'Bestellen' => 0,
        'Afgerond betaald' => 0,
        'Afgehaald nog niet betaald' => 0,
    ];
    while ($row = $result->fetch_assoc()) {
        $stats[$row['status']] = $row['count'];
    }
    return $stats;
}

function getTotalQuantitiesToOrder() {
    global $conn;
    $sql = "SELECT p.id, p.omschrijving, p.inkoopprijs, SUM(op.quantity) as total_quantity
            FROM order_products op
            JOIN orders o ON op.order_id = o.id
            JOIN producten p ON op.product_id = p.id
            WHERE o.status = 'Bestellen'
            GROUP BY p.id, p.omschrijving, p.inkoopprijs";
    $result = $conn->query($sql);
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    return $products;
}


function getTotalAmount() {
    global $conn;
    $sql = "SELECT SUM(totaalprijs) as total FROM orders";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

function getTotalOrders() {
    global $conn;
    $sql = "SELECT COUNT(*) as total FROM orders";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

function addProduct($omschrijving, $productsoort, $inkoopprijs, $prijs, $foto) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO producten (omschrijving, productsoort, inkoopprijs, prijs, foto) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('ssdds', $omschrijving, $productsoort, $inkoopprijs, $prijs, $foto);
    return $stmt->execute();
}

function updateProduct($id, $omschrijving, $productsoort, $inkoopprijs, $prijs, $foto) {
    global $conn;
    if ($foto) {
        $stmt = $conn->prepare("UPDATE producten SET omschrijving = ?, productsoort = ?, inkoopprijs = ?, prijs = ?, foto = ? WHERE id = ?");
        $stmt->bind_param('ssddsi', $omschrijving, $productsoort, $inkoopprijs, $prijs, $foto, $id);
    } else {
        $stmt = $conn->prepare("UPDATE producten SET omschrijving = ?, productsoort = ?, inkoopprijs = ?, prijs = ? WHERE id = ?");
        $stmt->bind_param('ssddi', $omschrijving, $productsoort, $inkoopprijs, $prijs, $id);
    }
    return $stmt->execute();
}

function deleteProduct($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM producten WHERE id = ?");
    $stmt->bind_param('i', $id);
    return $stmt->execute();
}

?>
