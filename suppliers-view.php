<?php
session_start();
require_once('database/connection.php');

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// جلب بيانات الموردين مع اسم المستخدم الذي أضافهم
$query = "SELECT s.*, CONCAT(u.first_name, ' ', u.last_name) AS created_by_name
          FROM suppliers s
          LEFT JOIN users u ON s.created_by = u.id
          ORDER BY s.id DESC";
$stmt = $con->query($query);
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عرض الموردين - نظام إدارة المخزون</title>
    <?php include('partials/app-header-scripts.php'); ?>
    <style>
        /* نسخ بعض التنسيقات الأساسية من عرض المنتجات */
        .dashboard_content_container {
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table thead th {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        table tbody td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .editRow {
            background: #f9f9f9;
        }
        .editRow form input[type="text"],
        .editRow form input[type="email"] {
            width: 90%;
            padding: 5px;
            margin: 5px 0;
        }
        .editRow form button {
            margin: 5px;
            padding: 5px 10px;
        }
        .custom-edit-icon,
        .custom-trash-icon {
            margin: 0 5px;
            cursor: pointer;
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
                <h1 class="section_title"><i class="fa fa-truck"></i> قائمة الموردين</h1>
                <div class="section_content">
                    <table>
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>اسم المورد</th>
                                <th>موقع المورد</th>
                                <th>البريد الإلكتروني</th>
                                <th>أضيف بواسطة</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($suppliers)): ?>
                            <?php foreach ($suppliers as $supplier): ?>
                                <!-- صف عرض البيانات -->
                                <tr id="dataRow-<?= $supplier['id'] ?>">
                                    <td><?= $supplier['id'] ?></td>
                                    <td class="supplier_name"><?= htmlspecialchars($supplier['supplier_name']) ?></td>
                                    <td class="supplier_location"><?= htmlspecialchars($supplier['supplier_location']) ?></td>
                                    <td class="email"><?= htmlspecialchars($supplier['email']) ?></td>
                                    <td><?= htmlspecialchars($supplier['created_by_name']) ?></td>
                                    <td><?= htmlspecialchars($supplier['created_at']) ?></td>
                                    <td>
                                        <a href="#"
                                           class="editSupplier"
                                           data-supplierid="<?= $supplier['id'] ?>"
                                           data-suppliername="<?= htmlspecialchars($supplier['supplier_name']) ?>"
                                           data-supplierlocation="<?= htmlspecialchars($supplier['supplier_location']) ?>"
                                           data-email="<?= htmlspecialchars($supplier['email']) ?>">
                                            <i class="fa fa-edit custom-edit-icon"></i>
                                        </a>
                                        <a href="#"
                                           class="deleteSupplier"
                                           data-supplierid="<?= $supplier['id'] ?>"
                                           data-suppliername="<?= htmlspecialchars($supplier['supplier_name']) ?>">
                                            <i class="fa fa-trash custom-trash-icon"></i>
                                        </a>
                                    </td>
                                </tr>
                                <!-- صف التعديل المخفي -->
                                <tr class="editRow" id="editRow-<?= $supplier['id'] ?>" style="display: none;">
                                    <td colspan="7">
                                        <form class="editSupplierForm" data-supplierid="<?= $supplier['id'] ?>">
                                            <input type="text"   name="supplier_name"     value="<?= htmlspecialchars($supplier['supplier_name']) ?>"     required>
                                            <input type="text"   name="supplier_location" value="<?= htmlspecialchars($supplier['supplier_location']) ?>" required>
                                            <input type="email"  name="email"             value="<?= htmlspecialchars($supplier['email']) ?>"             required>
                                            <button type="submit"><i class="fa fa-save"></i> تحديث</button>
                                            <button type="button" class="cancelEdit" data-supplierid="<?= $supplier['id'] ?>">إلغاء</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">لا توجد بيانات موردين.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div><!-- نهاية section_content -->
            </div><!-- نهاية dashboard_content_main -->
        </div><!-- نهاية dashboard_content -->
    </div><!-- نهاية dashboard_content_container -->
</div><!-- نهاية dashboardMainContainer -->

<!-- ملفات الجافاسكربت -->
<?php include('partials/app-script.php'); ?>
<script>
$(document).ready(function(){
    // عرض نموذج التعديل عند الضغط على زر التعديل
    $('.editSupplier').click(function(e){
        e.preventDefault();
        var supplierId = $(this).data('supplierid');
        // إخفاء أي نموذج مفتوح آخر
        $('.editRow').hide();
        $('#editRow-' + supplierId).toggle();
    });

    // إلغاء التعديل
    $('.cancelEdit').click(function(){
        var supplierId = $(this).data('supplierid');
        $('#editRow-' + supplierId).hide();
    });

    // إرسال بيانات التعديل عبر AJAX
    $('.editSupplierForm').submit(function(e){
        e.preventDefault();
        var form = $(this);
        var supplierId = form.data('supplierid');
        var formData = form.serialize() + '&id=' + supplierId;

        $.ajax({
            url: 'database/update-supplier.php', // ملف التحديث
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response){
                if(response.status === true || response.status === 'true'){
                    // تحديث القيم في الصف الأصلي
                    var row = $('#dataRow-' + supplierId);
                    row.find('.supplier_name').text(form.find('input[name="supplier_name"]').val());
                    row.find('.supplier_location').text(form.find('input[name="supplier_location"]').val());
                    row.find('.email').text(form.find('input[name="email"]').val());

                    Swal.fire({
                        title: 'تم التحديث!',
                        text: response.message,
                        icon: 'success',
                        background: '#000',
                        iconColor: '#28a745',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#editRow-' + supplierId).hide();
                } else {
                    Swal.fire({
                        title: 'خطأ',
                        text: response.message,
                        icon: 'error',
                        background: '#000',
                        iconColor: '#FFA500',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(){
                Swal.fire({
                    title: 'خطأ',
                    text: 'حدث خطأ أثناء محاولة التحديث. الرجاء المحاولة لاحقًا.',
                    icon: 'error',
                    background: '#000',
                    iconColor: '#FFA500',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });

    // حذف المورد عبر SweetAlert
    $('.deleteSupplier').click(function(e){
        e.preventDefault();
        var supplierId   = $(this).data('supplierid');
        var supplierName = $(this).data('suppliername');

        Swal.fire({
            title: 'تأكيد الحذف',
            html: `<p style="font-size:16px; margin:0;">هل أنت متأكد من حذف المورد <strong style="color:rgb(229, 126, 7);">${supplierName}</strong>؟</p>
                   <p style="font-size:14px; color:rgb(166, 0, 0); margin:10px 0 0;">لن يمكن استعادة البيانات بعد الحذف!</p>`,
            icon: 'warning',
            iconColor: 'rgb(88, 8, 8)',
            background: '#000',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: '<i class="fa fa-trash"></i> نعم، احذف',
            cancelButtonText: '<i class="fa fa-times"></i> إلغاء',
            confirmButtonColor: 'rgb(88, 8, 8)',
            cancelButtonColor: '#28a745',
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: 'database/delete-supplier.php', // ملف الحذف
                    method: 'POST',
                    dataType: 'json',
                    data: { id: supplierId },
                    success: function(response){
                        if(response.status === true || response.status === 'true'){
                            $('#dataRow-' + supplierId).fadeOut(400, function(){
                                $(this).remove();
                            });
                            Swal.fire({
                                title: 'تم الحذف!',
                                text: response.message,
                                icon: 'success',
                                background: '#000',
                                iconColor: '#28a745',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                title: 'خطأ',
                                text: response.message,
                                icon: 'error',
                                background: '#000',
                                iconColor: '#FFA500',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(){
                        Swal.fire({
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء محاولة الحذف. الرجاء المحاولة لاحقًا.',
                            icon: 'error',
                            background: '#000',
                            iconColor: '#FFA500',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    });
});
</script>
</body>
</html>
