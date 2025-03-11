<?php
session_start();
require_once('connection.php');

// التحقق من تسجيل الدخول والتأكد من وجود معرف المستخدم
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user']['id'];

// جلب بيانات النموذج
$batch = $_POST['batch'] ?? time();
$products = $_POST['product'] ?? [];
$suppliers = $_POST['supplier'] ?? [];
$quantities = $_POST['quantity_ordered'] ?? [];

// التحقق من أن المصفوفات تحتوي على نفس عدد العناصر
if (count($products) !== count($suppliers) || count($products) !== count($quantities)) {
    $_SESSION['response'] = [
        'status'  => false,
        'message' => 'خطأ: البيانات المدخلة غير متطابقة.'
    ];
    header('Location: ../product-order.php');
    exit;
}

try {
    $con->beginTransaction();

    // إدخال صف لكل منتج في النموذج
    for ($i = 0; $i < count($products); $i++) {
        $productId  = $products[$i];
        $supplierId = $suppliers[$i];
        $qty        = $quantities[$i];

        // التحقق من صحة البيانات (يمكنك توسيع هذا الفحص حسب الحاجة)
        if (empty($productId) || empty($supplierId) || $qty < 1) {
            continue; // أو يمكنك التعامل مع الخطأ بطريقة أخرى
        }

        $stmt = $con->prepare("
            INSERT INTO order_product 
            (batch, product, supplier, quantity_ordered, quantity_received, status, created_by, created_at)
            VALUES (:batch, :product, :supplier, :qty_ordered, 0, 'pending', :user_id, NOW())
        ");
        $stmt->execute([
            ':batch'        => $batch,
            ':product'      => $productId,
            ':supplier'     => $supplierId,
            ':qty_ordered'  => $qty,
            ':user_id'      => $userId
        ]);
    }

    $con->commit();
    $_SESSION['response'] = [
        'status'  => true,
        'message' => 'تم حفظ الطلب بنجاح!'
    ];
} catch (PDOException $e) {
    $con->rollBack();
    // يمكنك تسجيل الخطأ في ملف log هنا
    $_SESSION['response'] = [
        'status'  => false,
        'message' => 'خطأ في حفظ الطلب: ' . $e->getMessage()
    ];
}

header('Location: ../product-order.php');
exit;
