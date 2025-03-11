<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// ضبط اسم الجدول في الجلسة ليكون "suppliers" بدلاً من "users"
$_SESSION['table'] = 'suppliers';
$user = $_SESSION['user'];

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="نظام إدارة مخزون متكامل بإمكانيات متقدمة">
    <title>IMS - Add Supplier</title>

    <!-- ربط ملفات CSS والسكربتات -->
    <?= include('partials/app-header-scripts.php'); ?>
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
                    <div class="row">
                        <!-- عمود إضافة المورد (Column-5) -->
                        <div class="column column-5">
                            <h1 class="section_title"><i class="fa fa-plus"></i> إضافة مورد </h1>
                            
                            <!-- نموذج إضافة مورد جديد -->
                            <form action="database/add.php" class="appForm" method="POST">
                                <div>
                                    <label for="supplier_name">اسم المورد</label>
                                    <input type="text" name="supplier_name" id="supplier_name" 
                                           placeholder="أدخل اسم المورد" required>
                                </div>
                                <div>
                                    <label for="supplier_location">عنوان المورد</label>
                                    <input type="text" name="supplier_location" id="supplier_location" 
                                           placeholder="أدخل عنوان المورد" required>
                                </div>
                                <div>
                                    <label for="email">البريد الإلكتروني</label>
                                    <input type="email" name="email" id="email" 
                                           placeholder="أدخل البريد الإلكتروني للمورد" required>
                                </div>
                                <button type="submit"><i class="fa fa-send"></i> إضافة مورد</button>
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
                        <!-- نهاية عمود الإضافة -->
                    </div>
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
