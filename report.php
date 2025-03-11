<?php
session_start();
require_once('database/connection.php');

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// استعلامات لجلب البيانات (مثلاً المنتجات، الموردين، التوصيلات، طلبات الشراء)
$stmtProducts = $con->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

$stmtSuppliers = $con->query("SELECT * FROM suppliers ORDER BY created_at DESC");
$suppliers = $stmtSuppliers->fetchAll(PDO::FETCH_ASSOC);

$stmtDeliveries = $con->query("SELECT * FROM order_product_history ORDER BY date_updated DESC");
$deliveries = $stmtDeliveries->fetchAll(PDO::FETCH_ASSOC);

$stmtPurchaseOrders = $con->query("SELECT * FROM order_product ORDER BY created_at DESC");
$purchase_orders = $stmtPurchaseOrders->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير النظام - نظام إدارة المخزون</title>
    <?php include('partials/app-header-scripts.php'); ?>
    <style>
        /* التنسيقات المطلوبة: الخلفية سوداء، النص أبيض، البوكسات برتقالية، والأزرار أخضر (يمكن استبدالها بالأحمر) */
        body {
            background-color: #000;
            color: #fff;
            font-family: 'Tajawal', sans-serif;
            margin: 0;
            padding: 0;
        }
        #dashboardMainContainer {
            display: flex;
        }
        .dashboard_content_container {
            flex: 1;
            background-color: #1a1a1a;
            min-height: 100vh;
        }
        .dashboard_content {
            padding: 20px;
        }
        .dashboard_content_main {
            background-color: #222;
            padding: 20px;
            border-radius: 8px;
        }
        .section_title {
            color: #ffa500; /* برتقالي */
            margin-bottom: 20px;
        }
        .report-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 20px;
        }
        .report-box {
            flex: 1 1 250px;
            background-color: #ffa500; /* برتقالي */
            padding: 20px;
            color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            min-width: 200px;
            text-align: center;
        }
        .report-box h2 {
            margin-top: 0;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        .export-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        .export-btn {
            background-color: #28a745; /* أخضر، يمكن تغييره إلى أحمر مثلاً: #dc3545 */
            border: none;
            color: #fff;
            padding: 8px 16px;
            text-decoration: none;
            font-size: 0.9rem;
            border-radius: 4px;
            cursor: pointer;
        }
        .export-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
<div id="dashboardMainContainer">
    <!-- القائمة الجانبية -->
    <?php include('partials/app-sidebar.php'); ?>

    <div class="dashboard_content_container">
        <!-- الشريط العلوي -->
        <?php include('partials/app-topnav.php'); ?>

        <div class="dashboard_content">
            <div class="dashboard_content_main">
                <h1 class="section_title"><i class="fa fa-file-alt"></i> تقرير النظام</h1>

                <div class="report-container">
                    <!-- تصدير المنتجات -->
                    <div class="report-box">
                        <h2>تصدير المنتجات</h2>
                        <div class="export-buttons">
                            <a class="export-btn" href="database/report_csv.php?report=products" target="_blank">
                                Excel
                            </a>
                            <a class="export-btn" href="database/report_pdf.php?report=products" target="_blank">
                                PDF
                            </a>
                        </div>
                    </div>
                    <!-- تصدير الموردين -->
                    <div class="report-box">
                        <h2>تصدير الموردين</h2>
                        <div class="export-buttons">
                            <a class="export-btn" href="database/report_csv.php?report=suppliers" target="_blank">
                                Excel
                            </a>
                            <a class="export-btn" href="database/report_pdf.php?report=suppliers" target="_blank">
                                PDF
                            </a>
                        </div>
                    </div>
                    <!-- تصدير التوصيلات -->
                    <div class="report-box">
                        <h2>تصدير التوصيلات</h2>
                        <div class="export-buttons">
                            <a class="export-btn" href="database/report_csv.php?report=deliveries" target="_blank">
                                Excel
                            </a>
                            <a class="export-btn" href="database/report_pdf.php?report=deliveries" target="_blank">
                                PDF
                            </a>
                        </div>
                    </div>
                    <!-- تصدير طلبات الشراء -->
                    <div class="report-box">
                        <h2>تصدير طلبات الشراء</h2>
                        <div class="export-buttons">
                            <a class="export-btn" href="database/report_csv.php?report=purchase_orders" target="_blank">
                                Excel
                            </a>
                            <a class="export-btn" href="database/report_pdf.php?report=purchase_orders" target="_blank">
                                PDF
                            </a>
                        </div>
                    </div>
                </div><!-- نهاية report-container -->

                <!-- تم إخفاء الجدول السفلي بحيث لا يظهر للمستخدم -->
                <!--
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>اسم المورد</th>
                            <th>الموقع</th>
                            <th>البريد الإلكتروني</th>
                            <th>تاريخ الإنشاء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($suppliers)): ?>
                            <?php foreach ($suppliers as $supplier): ?>
                                <tr>
                                    <td><?= $supplier['id'] ?></td>
                                    <td><?= htmlspecialchars($supplier['supplier_name']) ?></td>
                                    <td><?= htmlspecialchars($supplier['supplier_location']) ?></td>
                                    <td><?= htmlspecialchars($supplier['email']) ?></td>
                                    <td><?= $supplier['created_at'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">لا توجد موردين.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                -->
            </div><!-- نهاية dashboard_content_main -->
        </div><!-- نهاية dashboard_content -->
    </div><!-- نهاية dashboard_content_container -->
</div><!-- نهاية dashboardMainContainer -->

<?php include('partials/app-script.php'); ?>
</body>
</html>
