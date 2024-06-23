<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

function createPurchaseOrder() {
    global $conn;

    // Verkrijg de producten die nog besteld moeten worden
    $quantitiesToOrder = getTotalQuantitiesToOrder();
    if (empty($quantitiesToOrder)) {
        return ['success' => false, 'message' => 'Geen producten om te bestellen.'];
    }

    // Voeg de inkooporder toe aan de bestellingen tabel
    $stmt = $conn->prepare("INSERT INTO bestellingen (status, totaalprijs) VALUES ('Besteld', 0)");
    if (!$stmt->execute()) {
        return ['success' => false, 'message' => 'Kon de bestelling niet aanmaken.'];
    }

    // Verkrijg het ID van de nieuw aangemaakte bestelling
    $orderId = $conn->insert_id;

    $totaalprijs = 0;

    // Voeg de producten toe aan de bestelling_producten tabel en bereken de totaalprijs
    foreach ($quantitiesToOrder as $product) {
        $stmt = $conn->prepare("INSERT INTO bestelling_producten (bestelling_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param('iii', $orderId, $product['id'], $product['total_quantity']);
        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Kon product niet toevoegen aan de bestelling.'];
        }
        $totaalprijs += ($product['total_quantity'] * ($product['inkoopprijs'] + 5)); // Voeg 5 euro verzendkosten per product toe
    }

    // Update de totaalprijs van de bestelling
    $stmt = $conn->prepare("UPDATE bestellingen SET totaalprijs = ? WHERE id = ?");
    $stmt->bind_param('di', $totaalprijs, $orderId);
    if (!$stmt->execute()) {
        return ['success' => false, 'message' => 'Kon de totaalprijs van de bestelling niet bijwerken.'];
    }

    // Update de statussen van de betrokken orders naar "Onderweg"
    $stmt = $conn->prepare("UPDATE orders SET status = 'Onderweg' WHERE status = 'Bestellen'");
    if (!$stmt->execute()) {
        return ['success' => false, 'message' => 'Kon de statussen van de orders niet bijwerken.'];
    }

    return ['success' => true];
}

$response = createPurchaseOrder();
echo json_encode($response);
