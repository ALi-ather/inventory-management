<?php
session_start();
$_SESSION['table'] = 'users';

/** التحقق من تسجيل الدخول */
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

/** التحقق من اسم الجدول في الجلسة، إن لم يكن موجوداً نحدد القيمة الافتراضية */
if (!isset($_SESSION['table'])) {
    $_SESSION['table'] = 'users';
}
error_log('Table: ' . $_SESSION['table']);

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="نظام إدارة مخزون متكامل بإمكانيات متقدمة">
    <title>نظام إدارة المخزون - إضافة مستخدم</title>

    <!-- ربط ملفات CSS -->
    <?php include('partials/app-header-scripts.php'); ?>
</head>
<body>
    <!-- الحاوية الرئيسية للوحة التحكم -->
    <div id="dashboardMainContainer">
        <!-- القائمة الجانبية -->
        <?php include('partials/app-sidebar.php'); ?>

        <!-- محتوى اللوحة -->
        <div class="dashboard_content_container">
            <!-- شريط التنقل العلوي -->
            <?php include('partials/app-topnav.php'); ?>

            <!-- المحتوى الرئيسي -->
            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    
                    <!-- عنوان الصفحة الرئيسي -->
                    <div class="row">
                        <div class="column column-12">
                            <h1 class="section_title"><i class="fa fa-plus"></i> إنشاء مستخدم</h1>
                        </div>
                    </div>

                    <!-- نموذج إضافة مستخدم جديد (بعمودين) -->
                    <form action="database/add.php" class="appForm" method="POST">
                        <div class="row">
                            <!-- عمود معلومات المستخدم -->
                            <div class="column column-5">
                                <div>
                                    <label for="first_name">الاسم الأول</label>
                                    <input type="text" name="first_name" id="first_name" placeholder="أدخل الاسم الأول" required>
                                </div>
                                <div>
                                    <label for="last_name">اسم العائلة</label>
                                    <input type="text" name="last_name" id="last_name" placeholder="أدخل اسم العائلة" required>
                                </div>
                                <div>
                                    <label for="email">البريد الإلكتروني</label>
                                    <input type="email" name="email" id="email" placeholder="أدخل البريد الإلكتروني" required>
                                </div>
                                <div>
                                    <label for="password">كلمة المرور</label>
                                    <input type="password" name="password" id="password" placeholder="أدخل كلمة المرور" required>
                                </div>
                            </div>

                            <!-- عمود الصلاحيات -->
                            <div class="column column-7">
                                <h2 class="section_title"><i class="fa fa-lock"></i> الصلاحيات</h2>
                                <?php include('partials/permission.php'); ?>
                            </div>
                        </div>

                        <!-- زر الإرسال -->
                        <div class="row">
                            <div class="column column-12" style="margin-top: 1rem;">
                                <button type="submit"><i class="fa fa-send"></i> إضافة مستخدم</button>
                            </div>
                        </div>
                    </form>

                    <!-- رسالة الاستجابة (نجاح/خطأ) -->
                    <?php if (isset($_SESSION['response'])):
                        $response_message = $_SESSION['response']['message'];
                        $is_status = $_SESSION['response']['status'];
                    ?>
                        <div class="responseMessage">
                            <p class="<?= $is_status ? 'success' : 'error' ?>">
                                <?= $response_message ?>
                            </p>
                        </div>
                    <?php
                        unset($_SESSION['response']);
                    endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- إشعار عائم للنجاح أو الخطأ -->
    <div class="floating-notification"></div>

    <!-- ملفات الجافاسكريبت -->
    <?php include('partials/app-script.php'); ?>
</body>
</html>
