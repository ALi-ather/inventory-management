<?php
session_start();

// التأكد من تسجيل الدخول
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$permissions = isset($user['permissions']) ? $user['permissions'] : [];
// فك ترميز JSON إذا كانت الصلاحيات محفوظة كسلسلة
if (is_string($permissions)) {
    $permissions = json_decode($permissions, true);
}
if (!is_array($permissions)) {
    $permissions = [];
}

// تحديد الصلاحية المطلوبة (يمكن تمريرها كمتغير)
$requiredPermission = 'dashboard_view';
if (!in_array($requiredPermission, $permissions)) {
    // يمكن عرض رسالة منع أو إعادة التوجيه إلى صفحة خطأ
    header('Location: not-authorized.php');
    exit;
}
?>
