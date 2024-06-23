<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../fpdf/fpdf.php');
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

$producten = getProducts();

// Sort products alphabetically by description
usort($producten, function($a, $b) {
    return strcmp($a['omschrijving'], $b['omschrijving']);
});

// Separate products into sigaretten and tabak
$sigaretten = array_filter($producten, function($product) {
    return $product['productsoort'] == 'Sigaretten';
});
$tabak = array_filter($producten, function($product) {
    return $product['productsoort'] == 'Tabak';
});

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        // Helvetica bold 15
        $this->SetFont('Helvetica', 'B', 15);
        // Title
        $this->Cell(0, 10, 'Prijslijst (' . date('d-m-Y') . ')', 0, 1, 'C');
        $this->Ln(10);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-50);
        // Helvetica italic 15
        $this->SetFont('Helvetica', 'I', 15);
        // Footer text
        $this->MultiCell(0, 10, " Komt uit Luxemburg \nZaterdag t/m vrijdag bestellen voor volgende week zaterdag levering.\nPrijzen geldig t/m 28-06\nBestelling graag via WhatsApp doorgeven.\n0645232687\n\nArtikel \nAdres \n\nLater krijg je bevestiging. ", 0, 'C');
    }

    // Load data
    function LoadData($producten)
    {
        return $producten;
    }

    // Table
    function FancyTable($header, $data)
    {
        // Colors, line width and bold font
        $this->SetFillColor(46, 139, 87); // Groen kleur
        $this->SetTextColor(255);
        $this->SetDrawColor(46, 139, 87);
        $this->SetLineWidth(.3);
        $this->SetFont('Helvetica', 'B');
        // Header
        $w = array(30, 100, 60);
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica');
        // Data
        $fill = false;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row['id'], 'LR', 0, 'C', $fill);
            $this->Cell($w[1], 6, $row['omschrijving'], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, number_format($row['prijs'], 2, ',', '.'), 'LR', 0, 'R', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln(10); // Add space between tables
    }
}

$pdf = new PDF();
$pdf->AddPage();
$header = array('Product ID', 'Product', 'Verkoopprijs');

// Sigaretten Table
$pdf->SetFont('Helvetica', 'B', 15);
$pdf->Cell(0, 10, 'Sigaretten', 0, 1, 'C');
$pdf->FancyTable($header, $sigaretten);

// Tabak Table
$pdf->SetFont('Helvetica', 'B', 15);
$pdf->Cell(0, 10, 'Tabak', 0, 1, 'C');
$pdf->FancyTable($header, $tabak);

$pdf->Output('prijslijst.pdf', 'D');
?>
