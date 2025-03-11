<?php
// database/get-product-suppliers.php

header('Content-Type: application/json');
session_start();

require_once('connection.php');

// التحقق من وصول معرف المنتج
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($productId <= 0) {
    echo json_encode([]);
    exit();
}

// جلب الموردين المرتبطين بالمنتج
// نفترض أن هناك جدول productsupplier يربط بين products و suppliers
// وأن اسمه productsupplier(product, supplier)
// نستخرج بيانات المورد من جدول suppliers
try {
    $stmt = $con->prepare("
        SELECT s.id, s.supplier_name
        FROM productsupplier ps
        JOIN suppliers s ON ps.supplier = s.id
        WHERE ps.product = :productId
    ");
    $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($suppliers);
} catch (PDOException $e) {
    // في حال حدوث خطأ، نعيد مصفوفة فارغة أو رسالة خطأ
    echo json_encode([]);
}
