<?php
include 'db.php';

if (isset($_POST['add'])) {
    $customerName = trim($_POST['cname']);
    $type = trim($_POST['type']);
    $jobName = trim($_POST['jname']);

    if (!empty($customerName) && !empty($type) &&!empty($jobName)) {

        // Get the next document number (shared between bills & quotations)
        $result = $conn->query("SELECT IFNULL(MAX(doc_no), 0) + 1 AS next_no FROM documents");
        $row = $result->fetch_assoc();
        $nextNo = $row['next_no'];

        //creating custom number for invoice or quoatation
        $today = date('Ymd');
        $preFix = ($type == 'invoice') ? 'INV' : 'QUO';
        $customNo = "{$preFix}-{$today}-{$nextNo}";

        // Insert the new record
        $stmt = $conn->prepare("INSERT INTO documents (doc_no, customer_name, type, job_name, custom_no) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $nextNo, $customerName, $type, $jobName, $customNo);

        if ($stmt->execute()) {
            echo "✅ Successfully added {$type} No: " . str_pad($nextNo, 3, '0', STR_PAD_LEFT) . " for {$customerName}";
            header("Location: export.php?doc_no=$nextNo");
            exit;
        } else {
            echo "❌ Failed to add: " . $stmt->error;
        }

    } else {
        echo "⚠️ Please fill in all fields.";
    }
}
?>
