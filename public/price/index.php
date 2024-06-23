<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verbinden met de database en productgegevens ophalen
$servername = "tb-nl01-linweb564.srv.teamblue-ops.net";
$username = "urksho_urkadmin";
$password = "Urkadmin123!";
$dbname = "urksho_urktabak";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, omschrijving, productsoort, inkoopprijs FROM producten";
$result = $conn->query($sql);

$producten = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $producten[] = $row;
    }
}
$conn->close();

// Simple HTML DOM library inladen
require 'libs/simple_html_dom.php';

function getHTML($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

function getPrices($url) {
    $html = getHTML($url);

    // Sleep voor 5 seconden (werkt alleen als de inhoud in de eerste request wordt geladen)
    sleep(5);

    // Laad de HTML in Simple HTML DOM Parser
    $dom = str_get_html($html);

    // Debug: Controleer of de HTML correct wordt geparsed
    if (!$dom) {
        die("Kon de HTML niet parsen van $url");
    }

    $prices = [];

    // Pas de selectors aan naar gelang de structuur van de webpagina
    foreach($dom->find('.product-item') as $element) {
        $product_name = $element->find('.product-name a', 0)->plaintext;
        $price = $element->find('.price', 0)->plaintext;
        $prices[$product_name] = floatval(str_replace(['€', ','], ['', '.'], $price)); // Convert price to float
    }
    
    return $prices;
}

$sigaretten_prices = getPrices('https://dfs.lu/en/products/48/201');
$tabak_prices = getPrices('https://dfs.lu/en/products/48/203');

function comparePrices($producten, $current_prices) {
    $results = [];
    
    foreach ($producten as $product) {
        $omschrijving = $product['omschrijving'];
        $inkoopprijs = $product['inkoopprijs'];
        $actuele_prijs = isset($current_prices[$omschrijving]) ? $current_prices[$omschrijving] : 'N/A';
        
        $results[] = [
            'omschrijving' => $omschrijving,
            'inkoopprijs' => $inkoopprijs,
            'actuele_prijs' => $actuele_prijs,
            'verschil' => ($actuele_prijs !== 'N/A') ? $actuele_prijs - $inkoopprijs : 'N/A'
        ];
    }
    
    return $results;
}

$sigaretten_results = comparePrices(array_filter($producten, function($product) {
    return $product['productsoort'] == 'Sigaretten';
}), $sigaretten_prices);

$tabak_results = comparePrices(array_filter($producten, function($product) {
    return $product['productsoort'] == 'Tabak';
}), $tabak_prices);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Prijsvergelijking</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Sigaretten Prijsvergelijking</h1>
    <table>
        <tr>
            <th>Omschrijving</th>
            <th>Inkoopprijs</th>
            <th>Actuele Prijs</th>
            <th>Verschil</th>
        </tr>
        <?php foreach ($sigaretten_results as $result): ?>
        <tr>
            <td><?php echo htmlspecialchars($result['omschrijving']); ?></td>
            <td><?php echo number_format($result['inkoopprijs'], 2); ?></td>
            <td><?php echo ($result['actuele_prijs'] !== 'N/A') ? number_format($result['actuele_prijs'], 2) : 'N/A'; ?></td>
            <td><?php echo ($result['verschil'] !== 'N/A') ? number_format($result['verschil'], 2) : 'N/A'; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h1>Tabak Prijsvergelijking</h1>
    <table>
        <tr>
            <th>Omschrijving</th>
            <th>Inkoopprijs</th>
            <th>Actuele Prijs</th>
            <th>Verschil</th>
        </tr>
        <?php foreach ($tabak_results as $result): ?>
        <tr>
            <td><?php echo htmlspecialchars($result['omschrijving']); ?></td>
            <td><?php echo number_format($result['inkoopprijs'], 2); ?></td>
            <td><?php echo ($result['actuele_prijs'] !== 'N/A') ? number_format($result['actuele_prijs'], 2) : 'N/A'; ?></td>
            <td><?php echo ($result['verschil'] !== 'N/A') ? number_format($result['verschil'], 2) : 'N/A'; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
