<?php
// ✅ Show all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';
include 'db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

// --- Validate input ---
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



// --- Load Excel and prepare for PDF export ---
$spreadsheet = IOFactory::load($excelPath);
$sheet = $spreadsheet->getActiveSheet();

// --- Adjust print settings ---
$sheet->getPageSetup()
    ->setPaperSize(PageSetup::PAPERSIZE_A4)
    ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
    ->setFitToWidth(1)
    ->setFitToHeight(1); // One page only

// Optional margins
$sheet->getPageMargins()->setTop(0.25);
$sheet->getPageMargins()->setRight(0.25);
$sheet->getPageMargins()->setLeft(0.25);
$sheet->getPageMargins()->setBottom(0.25);

// Optional center
$sheet->getPageSetup()->setHorizontalCentered(true);

// Define print area (only the used cells)
$highestColumn = $sheet->getHighestColumn();
$highestRow = $sheet->getHighestRow();
$sheet->getPageSetup()->setPrintArea("A1:{$highestColumn}{$highestRow}");

$pdfPath = "$exportFolder/{$row['custom_no']}.pdf";
// --- Save PDF ---
$writer = new Mpdf($spreadsheet);
$writer->save($pdfPath);



echo "✅ PDF exported successfully at: <b>$pdfPath</b>";

?>
