<?php
// ✅ Show errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';
include 'db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

// --- Check document number ---
if (!isset($_GET['doc_no'])) {
    die("❌ Missing document number. Example usage: export_pdf.php?doc_no=1");
}

$docNo = intval($_GET['doc_no']);

// --- Fetch document data ---
$stmt = $conn->prepare("SELECT * FROM documents WHERE doc_no = ?");
$stmt->bind_param("i", $docNo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ No document found for doc_no = $docNo");
}
$row = $result->fetch_assoc();

// --- Determine correct Excel path ---
$exportFolder = ($row['type'] === 'invoice') ? '../exports/output/invoice' : '../exports/output/quotation';
$excelPath = "$exportFolder/{$row['custom_no']}.xlsx";

if (!file_exists($excelPath)) {
    die("❌ Excel file not found at: $excelPath");
}

// --- Define PDF output path ---
$pdfPath = "$exportFolder/{$row['custom_no']}.pdf";

// --- Load Excel and export to PDF ---
$spreadsheet = IOFactory::load($excelPath);
$writer = new Mpdf($spreadsheet);
$writer->save($pdfPath);

echo "✅ PDF exported successfully at: <b>$pdfPath</b>";
?>
