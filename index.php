<?php
session_start();
if (!isset($_SESSION['user'])) { // تأكد من أن اسم الجلسة مطابق لما في login.php
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user']; // الآن ستحصل على بيانات المستخدم الصحيحة
?>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="نظام إدارة مخزون متكامل بإمكانيات متقدمة">
    <title>نظام إدارة المخزون - لوحة التحكم</title>
    <link rel="stylesheet" type="text/css" href="CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
        <div id="dashboardMainContainer">
    <?php include('partials/app-sidebar.php') ?>
        <div class="dashboard_content_container">
    <?php include('partials/app-topnav.php') ?>
            <div class="dashboard content">
                <div class="dashboard content main">
                    <!-- المحتوى الرئيسي هنا -->
                    <h1>مرحباً بك في النظام</h1>
                    <div class="stats-container">
                        <!-- إضافة عناصر إحصاءات هنا -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="floating-notification"></div>

    <script src="JS/script.js"></script>
</body>
</html>
