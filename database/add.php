<?php 
session_start();

// التأكد من وجود مفتاح 'table' في الجلسة، وإذا لم يكن موجودًا، ضبط قيمة افتراضية
if (!isset($_SESSION['table'])) {
    $_SESSION['table'] = 'users'; // غيّر اسم الجدول حسب الحاجة
}
$table_name = $_SESSION['table'];

// جلب بيانات النموذج مع التحقق من وجودها
$first_name = $_POST['first_name'] ?? '';
$last_name  = $_POST['last_name']  ?? '';
$email      = $_POST['email']      ?? '';
$password   = $_POST['password']   ?? '';

// تشفير كلمة المرور
$encrypted = password_hash($password, PASSWORD_DEFAULT);

// جملة SQL مع معاملات مسمّاة
$command = "INSERT INTO $table_name (first_name, last_name, email, password, created_at, updated_at)
            VALUES (:first_name, :last_name, :email, :encrypted, NOW(), NOW())";

include('connection.php');

try {
    // استخدام Prepared Statement للربط والتنفيذ
    $stmt = $con->prepare($command);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':encrypted', $encrypted);

    if($stmt->execute()){
        // نجاح الإضافة
        $_SESSION['response'] = [
            'status'  => true,
            'message' => 'تمت الإضافة بنجاح'
        ];
    } else {
        // فشل التنفيذ
        $_SESSION['response'] = [
            'status'  => false,
            'message' => 'حدث خطأ أثناء الإضافة'
        ];
    }
} catch (PDOException $e) {
    // التقاط أي أخطاء في قاعدة البيانات
    $_SESSION['response'] = [
        'status'  => false,
        'message' => 'حدث خطأ: ' . $e->getMessage()
    ];
}

// إعادة التوجيه إلى صفحة إضافة المستخدم
header('Location: ../users-add.php');
exit();
?>
