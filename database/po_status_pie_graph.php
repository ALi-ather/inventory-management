<?php
/**
 * هذا الملف مسؤول عن جلب إحصائيات حالات الطلبات (incomplete, pending, complete)
 * من قاعدة البيانات، وتخزينها في متغيرات لاستخدامها في المخطط البياني.
 */

require_once 'connection.php'; // تأكد من صحة المسار

try {
    // الاستعلام عن عدد الطلبات في كل حالة
    $stmt = $con->prepare("SELECT status, COUNT(*) AS total FROM order_product GROUP BY status");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // متغيرات لحفظ القيم
    $incompleteCount = 0;
    $pendingCount = 0;
    $completeCount = 0;

    // توزيع النتائج حسب الحالة
    foreach ($rows as $row) {
        switch ($row['status']) {
            case 'incomplete':
                $incompleteCount = (int)$row['total'];
                break;
            case 'pending':
                $pendingCount = (int)$row['total'];
                break;
            case 'complete':
                $completeCount = (int)$row['total'];
                break;
        }
    }

} catch (PDOException $e) {
    // في حال حدوث خطأ في الاتصال أو الاستعلام
    // يمكنك تسجيل الخطأ أو عرضه للتصحيح
    $incompleteCount = 0;
    $pendingCount = 0;
    $completeCount = 0;
}
