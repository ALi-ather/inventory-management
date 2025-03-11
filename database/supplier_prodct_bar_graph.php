<?php
require_once 'connection.php';

// مصفوفتان لتخزين أسماء المورّدين وعدد المنتجات لكل مورّد
$supplierNames = [];
$productCounts = [];

try {
    // استعلام افتراضي: كم منتج لكل مورّد
    // عدّل حسب جداولك الحقيقية
    $stmt = $con->prepare("
        SELECT s.supplier_name, COUNT(ps.id) AS total
        FROM productsupplier ps
        JOIN suppliers s ON ps.supplier = s.id
        GROUP BY s.supplier_name
        ORDER BY total DESC
        LIMIT 25
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $supplierNames[] = $row['supplier_name'];
        $productCounts[] = (int)$row['total'];
    }
} catch (PDOException $e) {
    // في حال حدوث خطأ، يمكن تسجيله أو عرضه
    error_log($e->getMessage());
}
?>
