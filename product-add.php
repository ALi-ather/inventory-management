<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// ضبط اسم الجدول في الجلسة ليكون "products"
$_SESSION['table'] = 'products';
$user = $_SESSION['user'];

// تحميل الموردين من قاعدة البيانات باستخدام PDO
require_once('database/connection.php');

$query = "SELECT id, supplier_name FROM suppliers";
$stmt = $con->query($query);
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="نظام إدارة مخزون متكامل بإمكانيات متقدمة">
    <title>Add Product - IMS</title>
    <?php include('partials/app-header-scripts.php'); ?>
</head>

<body>
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php'); ?>
        <div class="dashboard_content_container">
            <?php include('partials/app-topnav.php'); ?>
            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <div class="row">
                        <!-- عمود إضافة المنتج -->
                        <div class="column column-5">
                            <h1 class="section_title"><i class="fa fa-plus"></i> Create Product</h1>

                            <!-- نموذج إضافة منتج جديد -->
                            <!-- لاحظ إضافة enctype="multipart/form-data" لرفع الصور -->
                            <form action="database/add.php" class="appForm" method="POST" enctype="multipart/form-data">
                                <div>
                                    <label for="product_name">اسم المنتج</label>
                                    <input type="text" name="product_name" id="product_name" placeholder="Enter product name.." required>
                                </div>
                                <div>
                                    <label for="description">الوصف</label>
                                    <textarea class="appFormInput productTextAreaInput"
                                        name="description"
                                        id="description"
                                        placeholder="Enter product description..."
                                        required></textarea>
                                </div>
                                <div class="appFormInputContainer">
                                    <label for="suppliers">الموردين</label>
                                    <select name="suppliers[]" id="suppliers" multiple required>
                                        <?php if (count($suppliers) > 0): ?>
                                            <?php foreach ($suppliers as $supplier): ?>
                                                <option value="<?= $supplier['id'] ?>"><?= htmlspecialchars($supplier['supplier_name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>لا يوجد موردين متاحين</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="appFormInputContainer">
                                    <label for="product_img">الصورة</label>
                                    <input type="file" name="product_img" id="product_img" accept="image/*" required>
                                </div>
                                <div class="appFormInputContainer">
                                    <button type="submit"><i class="fa fa-send"></i> Create Product</button>
                                </div>
                            </form>

                            <!-- رسالة الاستجابة (نجاح/خطأ) -->
                            <?php if (isset($_SESSION['response'])):
                                $response_message = $_SESSION['response']['message'];
                                $is_status        = $_SESSION['response']['status'];
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

    <div class="floating-notification"></div>
    <?php include('partials/app-script.php'); ?>
</body>

</html>