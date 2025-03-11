<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('connection.php'); // تأكد أن هذا الملف يُعرّف $con (PDO)

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'طريقة الطلب غير صالحة.']);
    exit();
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    echo json_encode(['status' => false, 'message' => 'معرف المنتج غير صالح.']);
    exit();
}

try {
    // استعلام لجلب بيانات المنتج للتأكد من وجوده والحصول على مسار الصورة
    $stmt = $con->prepare("SELECT img FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        echo json_encode(['status' => false, 'message' => 'المنتج غير موجود.']);
        exit();
    }
    
    // حذف الصورة إذا كانت موجودة وليست الصورة الافتراضية
    if (!empty($product['img']) && $product['img'] !== 'uploads/product/default.png' && file_exists($product['img'])) {
        unlink($product['img']);
    }
    
    // حذف المنتج من قاعدة البيانات
    $stmt = $con->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['status' => true, 'message' => 'تم حذف المنتج بنجاح.']);
} catch (PDOException $e) {
    echo json_encode(['status' => false, 'message' => 'حدث خطأ أثناء حذف المنتج.']);
}
exit();
?>
