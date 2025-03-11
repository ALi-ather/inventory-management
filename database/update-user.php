<?php
// بدء الجلسة
session_start();

// عرض جميع الأخطاء
error_reporting(E_ALL);
ini_set('display_errors', 1);

// تضمين ملف الاتصال بقاعدة البيانات
require_once 'connection.php';

// التحقق من نجاح الاتصال بقاعدة البيانات
if (!$con) {
    die(json_encode(['status' => false, 'message' => 'فشل الاتصال بقاعدة البيانات']));
}

// استقبال بيانات التعديل
$userId = $_POST['userId'] ?? null;
$first_name = $_POST['first_name'] ?? '';
$last_name  = $_POST['last_name'] ?? '';
$email      = $_POST['email'] ?? '';

// التحقق من استقبال البيانات بشكل صحيح
if (!$userId || empty($first_name) || empty($last_name) || empty($email)) {
    die(json_encode([
        'status' => false,
        'message' => 'بيانات غير كاملة.',
        'received_data' => $_POST // عرض البيانات المستقبلة للمساعدة في التشخيص
    ]));
}

// تحضير استعلام التحديث
$stmt = $con->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, updated_at = NOW() WHERE id = ?");

// التحقق من نجاح تحضير الاستعلام
if (!$stmt) {
    die(json_encode([
        'status' => false,
        'message' => 'خطأ في تحضير الاستعلام: ' . $con->error
    ]));
}

// تنفيذ الاستعلام
if ($stmt->execute([$first_name, $last_name, $email, $userId])) {
    echo json_encode([
        'status' => true,
        'message' => 'تم تحديث بيانات المستخدم بنجاح.'
    ]);
} else {
    echo json_encode([
        'status' => false,
        'message' => 'فشل التحديث، الرجاء المحاولة لاحقًا.',
        'error' => $stmt->errorInfo() // عرض تفاصيل الخطأ
    ]);
}
?>
