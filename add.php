<?php
include 'db.php';

if(isset($_POST['add'])){
 $customerName = trim($_POST('cname'));
 $type= trim($_POST('invoice'));

 if(!empty('cname')){
    $stmt = $conn->prepare("INSERT INTO documents (customer_name, type) VALUES (?, ?)");
    $stmt->bind_param("se", $customerName, $type);
    $stmt->execute();
    echo "succesfully added";
 }
 echo "failed to add";

}
?>