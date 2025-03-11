<?php
session_start();
require_once('database/connection.php');

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$user_id = $user['id'];

// جلب قائمة المنتجات (لاختيارها في النموذج)
$stmtProducts = $con->query("SELECT id, product_name FROM products ORDER BY id DESC");
$productsList = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

// (اختياري) توليد رقم الدُفعة تلقائياً
// مثلاً يمكنك استخدام الوقت الحالي أو أي صيغة أخرى:
$batchNumber = date('YmdHis'); // 20250312123059 مثلاً
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء طلب شراء - نظام إدارة المخزون</title>
    <?php include('partials/app-header-scripts.php'); ?>

    <!-- مثال للوضع الداكن (Dark Mode) - اختياري -->
    <style>
        body {
            background-color: #1e1e1e; /* خلفية داكنة */
            color: #ffffff;           /* نص أبيض */
            margin: 0;
            padding: 0;
            font-family: 'Tajawal', sans-serif;
        }
        .ordersTable {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            background-color: #2c2c2c; /* خلفية داكنة للجدول */
            color: #fff;
        }
        .ordersTable th, .ordersTable td {
            border: 1px solid #444;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }
        .ordersTable thead {
            background: #ff6a00;
            color: #fff;
        }
        .section_title {
            margin-bottom: 1rem;
            color: #fff;
        }
        .removeRowBtn {
            background: #ff4444;
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .removeRowBtn:hover {
            background: #cc0000;
        }
        .addRowBtn {
            background: #28a745;
            color: #fff;
            padding: 8px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 10px 0;
        }
        .addRowBtn:hover {
            background: #218838;
        }
        .saveOrderBtn {
            padding: 10px 20px;
            background: rgb(255, 115, 0);
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .saveOrderBtn:hover {
            background: #e66900;
        }
        /* رسالة الاستجابة */
        .responseMessage p.success {
            background-color: #155724;
            color: #fff;
            padding: 10px;
            border-radius: 4px;
        }
        .responseMessage p.error {
            background-color: #721c24;
            color: #fff;
            padding: 10px;
            border-radius: 4px;
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
                <h1 class="section_title"><i class="fa fa-cart-plus"></i> إنشاء طلب شراء</h1>
                <div class="section_content">
                    <!-- نموذج الطلب 
                         ملاحظة: نخزّن batchNumber في حقل مخفي إذا أردنا تمريره إلى save-order.php -->
                    <form id="purchaseOrderForm" action="database/save-order.php" method="POST">
                        <!-- إذا أردت إرسال رقم الدفعة آلياً -->
                        <input type="hidden" name="batch" value="<?php echo $batchNumber; ?>">

                        <table class="ordersTable" id="orderTable">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>المورد</th>
                                    <th>الكمية المطلوبة</th>
                                    <th>إزالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- إنشاء الصف الأول افتراضيًا -->
                                <tr>
                                    <td>
                                        <select name="product[]" class="productSelect" required>
                                            <option value="">اختر المنتج</option>
                                            <?php foreach ($productsList as $prod): ?>
                                                <option value="<?= $prod['id'] ?>">
                                                    <?= htmlspecialchars($prod['product_name'], ENT_QUOTES, 'UTF-8') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="supplier[]" class="supplierSelect" required>
                                            <option value="">اختر المنتج أولاً</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="quantity_ordered[]" min="1" value="1" required>
                                    </td>
                                    <td>
                                        <button type="button" class="removeRowBtn" onclick="removeRow(this)">حذف</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <button type="button" class="addRowBtn" id="addRowBtn">+ إضافة صف</button>
                        
                        <div style="margin-top: 1rem;">
                            <button type="submit" class="saveOrderBtn">حفظ الطلب</button>
                        </div>
                    </form>

                    <!-- رسالة الاستجابة (نجاح/خطأ) -->
                    <?php if (isset($_SESSION['response'])):
                        $response_message = $_SESSION['response']['message'];
                        $is_status        = $_SESSION['response']['status'];
                    ?>
                        <div class="responseMessage" style="margin-top: 1rem;">
                            <p class="<?= $is_status ? 'success' : 'error' ?>">
                                <?= htmlspecialchars($response_message, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    <?php
                        unset($_SESSION['response']);
                    endif; ?>
                </div><!-- نهاية section_content -->
            </div><!-- نهاية dashboard_content_main -->
        </div><!-- نهاية dashboard_content -->
    </div><!-- نهاية dashboard_content_container -->
</div><!-- نهاية dashboardMainContainer -->

<?php include('partials/app-script.php'); ?>

<script>
// عند تغيير المنتج، نجلب الموردين المرتبطين به عبر AJAX
document.addEventListener('change', function(e){
    if(e.target.classList.contains('productSelect')){
        let productId = e.target.value;
        let supplierSelect = e.target.closest('tr').querySelector('.supplierSelect');
        
        if(productId === ''){
            supplierSelect.innerHTML = '<option value="">اختر المنتج أولاً</option>';
            return;
        }

        // جلب الموردين عبر AJAX
        fetch('database/get-product-suppliers.php?id=' + productId)
        .then(response => response.json())
        .then(data => {
            supplierSelect.innerHTML = '';
            if(data.length > 0){
                supplierSelect.innerHTML = '<option value="">اختر المورد</option>';
                data.forEach(function(supplier){
                    let opt = document.createElement('option');
                    opt.value = supplier.id;
                    opt.textContent = supplier.supplier_name;
                    supplierSelect.appendChild(opt);
                });
            } else {
                supplierSelect.innerHTML = '<option value="">لا يوجد موردون لهذا المنتج</option>';
            }
        })
        .catch(err => {
            console.error(err);
            supplierSelect.innerHTML = '<option value="">خطأ في جلب الموردين</option>';
        });
    }
});

// دالة لحذف الصف
function removeRow(btn){
    let tableBody = document.querySelector('#orderTable tbody');
    if(tableBody.rows.length === 1){
        alert('لا يمكنك حذف جميع الصفوف!');
        return;
    }
    btn.closest('tr').remove();
}

// إضافة صف جديد عند الضغط على زر "إضافة صف"
document.getElementById('addRowBtn').addEventListener('click', function(){
    let tableBody = document.querySelector('#orderTable tbody');
    let newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>
            <select name="product[]" class="productSelect" required>
                <option value="">اختر المنتج</option>
                <?php foreach ($productsList as $prod): ?>
                    <option value="<?= $prod['id'] ?>">
                        <?= htmlspecialchars($prod['product_name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select name="supplier[]" class="supplierSelect" required>
                <option value="">اختر المنتج أولاً</option>
            </select>
        </td>
        <td>
            <input type="number" name="quantity_ordered[]" min="1" value="1" required>
        </td>
        <td>
            <button type="button" class="removeRowBtn" onclick="removeRow(this)">حذف</button>
        </td>
    `;
    tableBody.appendChild(newRow);
});
</script>
</body>
</html>
