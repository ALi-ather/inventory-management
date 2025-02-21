<?php
header('Content-Type: application/json');
include('connection.php');

$data = $_POST;

if (isset($data['userId'])) {
    $userId = $data['userId'];

    try {
        $command = "DELETE FROM users WHERE id = :userId";
        $stmt = $con->prepare($command);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $response = [
                'status'  => true,
                'message' => 'تم حذف المستخدم بنجاح'
            ];
        } else {
            $response = [
                'status'  => false,
                'message' => 'لم يتم العثور على المستخدم أو فشل الحذف'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'status'  => false,
            'message' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()
        ];
    }
} else {
    $response = [
        'status'  => false,
        'message' => 'معرف المستخدم غير موجود'
    ];
}

echo json_encode($response);
exit;
?>
// الخطوة 3: تحديث الجدول
