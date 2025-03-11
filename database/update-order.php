<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => false, 'message' => 'Unauthorized.']);
    exit;
}

require_once('connection.php');

// استلام القيم
$orderId = $_POST['orderId'] ?? null;
$qtyDelivered = isset($_POST['qty_delivered']) ? (int)$_POST['qty_delivered'] : 0;
$status = $_POST['status'] ?? null;

if (!$orderId || $status === null) {
    echo json_encode(['status' => false, 'message' => 'Missing required fields.']);
    exit;
}

// التحقق من أن الكمية المستلمة ليست سالبة
if ($qtyDelivered < 0) {
    echo json_encode(['status' => false, 'message' => 'Quantity delivered cannot be negative.']);
    exit;
}

try {
    $con->beginTransaction();

    // جلب بيانات الطلب الحالي
    $stmt = $con->prepare("SELECT product, quantity_ordered, quantity_received FROM order_product WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $orderId]);
    $oldData = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$oldData) {
        echo json_encode(['status' => false, 'message' => 'Order not found.']);
        exit;
    }

    $oldQtyReceived = (int)$oldData['quantity_received'];
    $qtyOrdered     = (int)$oldData['quantity_ordered'];
    $productId      = $oldData['product'];

    // حساب الكمية المستلمة الجديدة مع عدم تجاوز الكمية المطلوبة
    $newQtyReceived = min($oldQtyReceived + $qtyDelivered, $qtyOrdered);

    // تحديث سجل الطلب في order_product
    $stmtUpdate = $con->prepare("
        UPDATE order_product
        SET quantity_received = :newQtyReceived,
            updated_at = NOW()
        WHERE id = :id
    ");
    $stmtUpdate->execute([
        ':newQtyReceived' => $newQtyReceived,
        ':id'             => $orderId
    ]);

    // تسجيل عملية التسليم في order_product_history
    $stmtHistory = $con->prepare("
        INSERT INTO order_product_history
        (order_product_id, qty_received, date_received, date_updated)
        VALUES (:order_product_id, :qty_delivered, NOW(), NOW())
    ");
    $stmtHistory->execute([
        ':order_product_id' => $orderId,
        ':qty_delivered'    => $qtyDelivered
    ]);

    // تحديث كمية المنتج في جدول المنتجات
    $stmtUpdateStock = $con->prepare("
        UPDATE products 
        SET stock = GREATEST(stock - :delivered, 0) 
        WHERE id = :pid
    ");
    $stmtUpdateStock->execute([
        ':delivered' => $qtyDelivered,
        ':pid'       => $productId
    ]);

    $con->commit();

    echo json_encode(['status' => true, 'message' => 'Order updated successfully.', 'newQtyReceived' => $newQtyReceived]);
} catch (PDOException $e) {
    $con->rollBack();
    echo json_encode(['status' => false, 'message' => 'Error updating order: ' . $e->getMessage()]);
}
?>
