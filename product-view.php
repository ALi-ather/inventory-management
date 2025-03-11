<?php
session_start();
require_once('database/connection.php');

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// Function to retrieve user's full name by ID
function getUserName($userId) {
    global $con;
    $stmt = $con->prepare("SELECT CONCAT(first_name, ' ', last_name) AS name FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ? $user['name'] : "Unknown";
}

// Retrieve all products along with their suppliers
$query = "SELECT p.*, 
       GROUP_CONCAT(s.supplier_name SEPARATOR ', ') AS supplier_names
FROM products p
LEFT JOIN productsupplier ps ON p.id = ps.product
LEFT JOIN suppliers s ON ps.supplier = s.id
GROUP BY p.id";

$stmt = $con->query($query);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض المنتجات - نظام إدارة المخزون</title>
    <!-- Include CSS and other header scripts -->
    <?php include('partials/app-header-scripts.php'); ?>
    <style>
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
        img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        .editRow {
            background: #f9f9f9;
        }
        .editRow form input[type="text"],
        .editRow form textarea,
        .editRow form input[type="file"] {
            width: 90%;
            padding: 5px;
            margin: 5px 0;
        }
        .editRow form button {
            margin: 5px;
            padding: 5px 10px;
        }
        .custom-edit-icon, .custom-trash-icon {
            margin: 0 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="dashboardMainContainer">
        <!-- Sidebar -->
        <?php include('partials/app-sidebar.php'); ?>

        <div class="dashboard_content_container">
            <!-- Top navigation -->
            <?php include('partials/app-topnav.php'); ?>

            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <h1 class="section_title"><i class="fa fa-cubes"></i> قائمة المنتجات</h1>
                    <div class="section_content">
                        <table>
                            <thead>
                                <tr>
                                    <th>الرقم</th>
                                    <th>اسم المنتج</th>
                                    <th>الوصف</th>
                                    <th>الصورة</th>
                                    <th>أضيف بواسطة</th>
                                    <th>الموردون</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                        <!-- Data row -->
                                        <tr id="dataRow-<?= $product['id'] ?>">
                                            <td><?= $product['id'] ?></td>
                                            <td class="product_name"><?= htmlspecialchars($product['product_name']) ?></td>
                                            <td class="description"><?= htmlspecialchars($product['description']) ?></td>
                                            <td>
                                                <img src="<?= !empty($product['img']) ? htmlspecialchars($product['img']) : 'uploads/product/default.png'; ?>" alt="Product Image">
                                            </td>
                                            <td><?= getUserName($product['created_by']) ?></td>
                                            <td><?= !empty($product['supplier_names']) ? htmlspecialchars($product['supplier_names']) : 'No Suppliers'; ?></td>
                                            <td>
                                                <a href="#" class="editProduct"
                                                   data-productid="<?= $product['id'] ?>"
                                                   data-productname="<?= htmlspecialchars($product['product_name']) ?>"
                                                   data-description="<?= htmlspecialchars($product['description']) ?>">
                                                    <i class="fa fa-edit custom-edit-icon"></i>
                                                </a>
                                                <a href="#" class="deleteProduct"
                                                   data-productid="<?= $product['id'] ?>"
                                                   data-productname="<?= htmlspecialchars($product['product_name']) ?>">
                                                    <i class="fa fa-trash custom-trash-icon"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <!-- Hidden edit row -->
                                        <tr class="editRow" id="editRow-<?= $product['id'] ?>" style="display: none;">
                                            <td colspan="7">
                                                <form class="editProductForm" data-productid="<?= $product['id'] ?>" enctype="multipart/form-data">
                                                    <input type="text" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>" placeholder="اسم المنتج" required>
                                                    <textarea name="description" placeholder="الوصف" required><?= htmlspecialchars($product['description']) ?></textarea>
                                                    <input type="file" name="product_img" accept="image/*">
                                                    <button type="submit"><i class="fa fa-save"></i> تحديث</button>
                                                    <button type="button" class="cancelEdit" data-productid="<?= $product['id'] ?>">إلغاء</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7">لا توجد منتجات متاحة.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div><!-- End of section_content -->
                </div><!-- End of dashboard_content_main -->
            </div><!-- End of dashboard_content -->
        </div><!-- End of dashboard_content_container -->
    </div><!-- End of dashboardMainContainer -->

    <!-- Include JavaScript files -->
    <?php include('partials/app-script.php'); ?>

    <script>
    $(document).ready(function(){
        // Show edit form when clicking the edit button
        $('.editProduct').click(function(e){
            e.preventDefault();
            var productId = $(this).data('productid');
            $('.editRow').hide(); // hide any open edit forms
            $('#editRow-' + productId).toggle();
        });

        // Cancel editing
        $('.cancelEdit').click(function(){
            var productId = $(this).data('productid');
            $('#editRow-' + productId).hide();
        });

        // Submit edit form via AJAX
        $('.editProductForm').submit(function(e){
            e.preventDefault();
            var form = $(this);
            var productId = form.data('productid');
            var formData = new FormData(this);
            formData.append('id', productId);

            $.ajax({
                url: 'database/update-product.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response){
                    if(response.status == true || response.status == 'true'){
                        var row = $('#dataRow-' + productId);
                        row.find('.product_name').text(form.find('input[name="product_name"]').val());
                        row.find('.description').text(form.find('textarea[name="description"]').val());
                        Swal.fire({
                            title: 'تم التحديث!',
                            text: response.message,
                            icon: 'success',
                            background: '#000',
                            iconColor: '#28a745',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#editRow-' + productId).hide();
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

        // Delete product using SweetAlert for confirmation
        $('.deleteProduct').click(function(e){
            e.preventDefault();
            var productId = $(this).data('productid');
            var productName = $(this).data('productname');

            Swal.fire({
                title: 'تأكيد الحذف',
                html: `<p style="font-size:16px; margin:0;">هل أنت متأكد من حذف المنتج <strong style="color:rgb(229, 126, 7);">${productName}</strong>؟</p>
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
                        url: 'database/delete.php',
                        method: 'POST',
                        dataType: 'json',
                        data: { id: productId },
                        success: function(response){
                            if(response.status === true || response.status === 'true'){
                                $('#dataRow-' + productId).fadeOut(400, function(){
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
                } else {
                    console.log('Deletion cancelled.');
                }
            });
        });
    });
    </script>
</body>
</html>