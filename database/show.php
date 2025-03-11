<?php
session_start();
require_once('connection.php'); // يجب أن يُعرّف هذا الملف $conn

header('Content-Type: application/json');

$query = "SELECT * FROM products";
$result = $conn->query($query);
$products = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode(['status' => true, 'products' => $products]);
exit();
?>
