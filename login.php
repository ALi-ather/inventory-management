<?php
session_start();
ob_start(); // لمنع أي إخراج قبل التوجيه

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'database/connection.php'; // ملف الاتصال

    // جلب القيم من النموذج
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // استعلام التحقق من المستخدم
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $con->prepare($query);
    $stmt->execute(['email' => $username]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // التحقق من كلمة المرور
        if ($password === $user['password']) {
            $_SESSION['user'] = $user;
            session_write_close(); // تأكد من حفظ الجلسة
            header("Location: index.php");
            exit;
        } else {
            $error_message = "كلمة المرور غير صحيحة!";
        }
    } else {
        $error_message = "المستخدم غير موجود!";
    }
}
ob_end_flush(); // تأكد من إنهاء المخرجات
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>IMS Login - نظام إدارة المخزون</title>
    <link rel="stylesheet" href="CSS/login.css"> <!-- ملف التنسيقات الخاص بك -->
    <!-- أسلوب بسيط لإشعار الخطأ العائم -->
    <style>
        .floating-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #f44336;
            color: #fff;
            padding: 12px 20px;
            border-radius: 4px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            opacity: 0;
            transition: opacity 0.4s ease-in-out;
            z-index: 9999;
            font-family: 'Tajawal', sans-serif;
        }
        .floating-notification.show {
            opacity: 1;
        }
    </style>
</head>
<body id="login-page">
    <div class="login-container">
        <!-- عنوان تسجيل الدخول -->
        <div class="login-header">
            <h1>تسجيل الدخول</h1>
            <p>مرحبًا بك في نظام إدارة المخزون</p>
        </div>

        <!-- نموذج تسجيل الدخول -->
        <form action="login.php" method="post" class="login-form">
            <div class="input-group">
                <label for="username">اسم المستخدم:</label>
                <input type="text" name="username" id="username" placeholder="أدخل اسم المستخدم" required>
            </div>

            <div class="input-group">
                <label for="password">كلمة المرور:</label>
                <input type="password" name="password" id="password" placeholder="أدخل كلمة المرور" required>
            </div>

            <button type="submit" class="btn-primary">تسجيل الدخول</button>
        </form>
    </div>

    <!-- إشعار الخطأ العائم -->
    <div class="floating-notification" id="errorNotification"></div>

    <!-- جافاسكربت لعرض رسالة الخطأ إن وجدت -->
    <script>
        const errorMessage = "<?php echo $error_message; ?>";
        if (errorMessage.trim() !== "") {
            const notification = document.getElementById('errorNotification');
            notification.textContent = errorMessage;
            notification.classList.add('show');

            // إخفاء الإشعار بعد 3 ثوانٍ
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
    </script>
</body>
</html>
