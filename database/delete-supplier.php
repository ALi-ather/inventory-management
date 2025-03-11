<?php
session_start();
require_once('connection.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'طلب غير صالح.']);
    exit();
}

$supplier_id = $_POST['id'] ?? 0;
if (empty($supplier_id)) {
    echo json_encode(['status' => false, 'message' => 'معرف المورد غير صالح.']);
    exit();
}

try {
    // حذف المورد
    $stmt = $con->prepare("DELETE FROM suppliers WHERE id = :id");
    $stmt->bindParam(':id', $supplier_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo json_encode(['status' => true, 'message' => 'تم حذف المورد بنجاح.']);
    } else {
        echo json_encode(['status' => false, 'message' => 'فشل الحذف، الرجاء المحاولة لاحقًا.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => false, 'message' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()]);
}
exit();
