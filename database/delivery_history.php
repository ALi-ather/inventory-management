<?php
// database/delivery_history.php

include_once __DIR__ . '/connection.php';

// استخدام $con بدلاً من $conn
$stmt = $con->prepare("SELECT qty_received, date_received 
                        FROM order_product_history
                        ORDER BY date_received ASC");
$stmt->execute();

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$line_data = [];

foreach($result as $row) {
    $timeStamp = strtotime($row['date_received']);
    if (!isset($line_data[$timeStamp])) {
        $line_data[$timeStamp] = (int)$row['qty_received'];
    } else {
        $line_data[$timeStamp] += (int)$row['qty_received'];
    }
}

$deliveryDates  = [];
$deliveryTotals = [];

ksort($line_data);

foreach($line_data as $timeKey => $qty) {
    $deliveryDates[]  = date('Y-m-d', $timeKey);
    $deliveryTotals[] = $qty;
}
?>
