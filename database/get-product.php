<?php
require_once('connection.php'); // تأكد أن الاتصال معرف

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['status' => false, 'message' => 'رقم المنتج غير موجود.']);
    exit();
}

$id = intval($_GET['id']);

// استعلام يجلب بيانات المنتج بالإضافة إلى معلومات المُنشئ والموردين (إذا وُجدوا)
$stmt = $con->prepare("
    SELECT 
        p.id AS product_id,
        p.product_name AS product_name,
        p.description AS product_description,
        p.img AS product_img,
        u.first_name AS creator_first,
        u.last_name AS creator_last,
        s.id AS supplier_id,
        s.supplier_name AS supplier_name
    FROM products p
    LEFT JOIN users u ON p.created_by = u.id
    LEFT JOIN productsupplier ps ON ps.product = p.id
    LEFT JOIN suppliers s ON ps.supplier = s.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($rows) {
    // تجميع بيانات المنتج، مع الموردين إن وُجدوا
    $product = [
        'product_id'          => $rows[0]['product_id'],
        'product_name'        => $rows[0]['product_name'],
        'product_description' => $rows[0]['product_description'],
        'product_img'         => $rows[0]['product_img'],
        'created_by'          => trim($rows[0]['creator_first'] . ' ' . $rows[0]['creator_last']),
        'suppliers'           => []
    ];

    // جلب الموردين (قد يكون هناك أكثر من مورد)
    foreach ($rows as $row) {
        if (!empty($row['supplier_id'])) {
            $product['suppliers'][] = [
                'supplier_id'   => $row['supplier_id'],
                'supplier_name' => $row['supplier_name']
            ];
        }
    }

    echo json_encode(['status' => true, 'product' => $product]);
} else {
    echo json_encode(['status' => false, 'message' => 'المنتج غير موجود.']);
}
exit();
?>
