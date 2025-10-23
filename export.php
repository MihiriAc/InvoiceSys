<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require 'vendor/autoload.php';
include 'db.php';


use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\IOFactory;

// --- PATHS ---
$templatePath = '../exports/Invoice.xlsx';

// Define clean folder path (no double names)
$exportFolderInvoice = '../exports/output/invoice';
$exportFolderQuotation = '../exports/output/quotation';
// --- Ensure folder exists ---

if (!is_dir($exportFolderInvoice)) {
    mkdir($exportFolderInvoice, 0777, true);
}
if (!is_dir($exportFolderQuotation)) {
    mkdir($exportFolderQuotation, 0777, true);
}


// --- Get document number ---
if (!isset($_GET['doc_no'])) {
    die("❌ Missing document number.");
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

// --- Load template ---
$spreadsheet = IOFactory::load($templatePath);
$sheet = $spreadsheet->getActiveSheet();

// --- Fill cells ---
$sheet->setCellValue('C10', $row['customer_name']);
$sheet->setCellValue('D11', $row['job_name']);
$sheet->setCellValue('H9', $row['created_at']);
$sheet->setCellValue('G13', ucfirst($row['type']));
$sheet->setCellValue('H13', $row['custom_no']);

// change the location based on the type
if (trim($row['type']) === 'invoice') { 
    $exportFolder = $exportFolderInvoice;
} else {
    $exportFolder = $exportFolderQuotation;
}


// --- Save new file ---
$filename = $exportFolder . '/' . $row['custom_no'] . '.xlsx'; 
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save($filename);

echo "✅ Excel file generated successfully at: $filename";
