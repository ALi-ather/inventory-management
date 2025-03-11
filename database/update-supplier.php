<?php
session_start();
require_once('connection.php');
header('Content-Type: application/json');

// التحقق من أن الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'طلب غير صالح.']);
    exit();
}

$supplier_id       = $_POST['id'] ?? 0;
$supplier_name     = $_POST['supplier_name'] ?? '';
$supplier_location = $_POST['supplier_location'] ?? '';
$email             = $_POST['email'] ?? '';

if (empty($supplier_id) || empty($supplier_name) || empty($supplier_location) || empty($email)) {
    echo json_encode(['status' => false, 'message' => 'الرجاء ملء جميع الحقول.']);
    exit();
}

try {
    // تحقق من صحة البريد الإلكتروني
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => false, 'message' => 'تنسيق البريد الإلكتروني غير صالح.']);
        exit();
    }

    $stmt = $con->prepare("UPDATE suppliers
                           SET supplier_name = :name,
                               supplier_location = :location,
                               email = :email,
                               updated_at = NOW()
                           WHERE id = :id");
    $stmt->bindParam(':name', $supplier_name);
    $stmt->bindParam(':location', $supplier_location);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $supplier_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['status' => true, 'message' => 'تم تحديث بيانات المورد بنجاح.']);
    } else {
        echo json_encode(['status' => false, 'message' => 'فشل التحديث، الرجاء المحاولة لاحقًا.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => false, 'message' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()]);
}
exit();
