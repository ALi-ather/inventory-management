<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('connection.php');// يُفترض أن هذا الملف يُعيد متغير الاتصال $con كـ PDO

// عرض بيانات POST للتأكد من استقبالها
if (!isset($_POST['userId'])) {
    echo json_encode(['status' => false, 'message' => 'بيانات POST غير مكتملة: لم يتم استقبال userId']);
    exit();
}

$userId = $_POST['userId'];
if (!is_numeric($userId)) {
    echo json_encode(['status' => false, 'message' => 'userId يجب أن يكون رقم']);
    exit();
}
$userId = (int)$userId;

// حاول حذف المستخدم من قاعدة البيانات باستخدام PDO
try {
    $stmt = $con->prepare("DELETE FROM users WHERE id = :id");
    $result = $stmt->execute([':id' => $userId]);
    
    if ($result) {
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => true, 'message' => 'تم حذف المستخدم بنجاح.']);
        } else {
            echo json_encode(['status' => false, 'message' => 'لم يتم حذف المستخدم؛ ربما تم حذفه مسبقاً.']);
        }
    } else {
        // إذا فشل تنفيذ الاستعلام، اطبع معلومات الخطأ
        $errorInfo = $stmt->errorInfo();
        echo json_encode([
            'status' => false,
            'message' => 'فشل تنفيذ الاستعلام.',
            'error' => $errorInfo
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => false,
        'message' => 'حدث خطأ أثناء حذف المستخدم: ' . $e->getMessage()
    ]);
}
?>
