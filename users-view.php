<?php
session_start();

/** التحقق من تسجيل الدخول */
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

/** التحقق من اسم الجدول في الجلسة، إن لم يكن موجوداً نحدد القيمة الافتراضية */
if (!isset($_SESSION['table'])) {
    $_SESSION['table'] = 'users';
}

$user = $_SESSION['user'];

/** جلب قائمة المستخدمين من ملف show-users.php */
$users = include('database/show-users.php');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="نظام إدارة مخزون متكامل بإمكانيات متقدمة">
    <title>نظام إدارة المخزون - عرض المستخدمين</title>

    <!-- ربط ملفات CSS -->
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
                        <!-- عمود عرض المستخدمين (Column-7) -->
                        <div class="column column-7">
                            <h1 class="section_title"><i class="fa fa-users"></i> قائمة المستخدمين</h1>
                            <div class="section_content">
                                <div class="users">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>الاسم الأول</th>
                                                <th>اسم العائلة</th>
                                                <th>البريد الإلكتروني</th>
                                                <th>تاريخ الإنشاء</th>
                                                <th>تاريخ التعديل</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- تكرار صفوف الجدول بناءً على مصفوفة $users -->
                                            <?php foreach ($users as $index => $user): ?>
                                                <tr class="dataRow" id="dataRow-<?= $user['id'] ?>">
                                                    <td class="first_name"><?= htmlspecialchars($user['first_name']) ?></td>
                                                    <td class="last_name"><?= htmlspecialchars($user['last_name']) ?></td>
                                                    <td class="email"><?= htmlspecialchars($user['email']) ?></td>
                                                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                                                    <td><?= htmlspecialchars($user['updated_at']) ?></td>
                                                    <td>
                                                        <!-- زر تعديل المستخدم -->
                                                        <a href="#"
                                                           class="editUser"
                                                           data-userid="<?= $user['id'] ?>"
                                                           data-firstname="<?= htmlspecialchars($user['first_name']) ?>"
                                                           data-lastname="<?= htmlspecialchars($user['last_name']) ?>"
                                                           data-email="<?= htmlspecialchars($user['email']) ?>">
                                                            <i class="fa fa-edit custom-edit-icon"></i>
                                                        </a>
                                                        <!-- زر حذف المستخدم -->
                                                        <a href="#"
                                                           class="deleteUser"
                                                           data-userid="<?= $user['id'] ?>"
                                                           data-fname="<?= $user['first_name'] ?>"
                                                           data-lname="<?= $user['last_name'] ?>">
                                                            <i class="fa fa-trash custom-trash-icon"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <!-- صف نموذج التعديل المخفي -->
                                                <tr class="editRow" id="editRow-<?= $user['id'] ?>" style="display: none;">
                                                    <td colspan="6">
                                                        <form class="editForm" data-userid="<?= $user['id'] ?>">
                                                            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" placeholder="الاسم الأول" required>
                                                            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" placeholder="اسم العائلة" required>
                                                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="البريد الإلكتروني" required>
                                                            <button type="submit"><i class="fa fa-save"></i> تحديث</button>
                                                            <button type="button" class="cancelEdit" data-userid="<?= $user['id'] ?>">إلغاء</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div> <!-- نهاية .users -->
                            </div> <!-- نهاية .section_content -->
                        </div>
                        <!-- نهاية عمود عرض المستخدمين -->
                    </div> <!-- نهاية الصف (row) -->
                </div> <!-- نهاية dashboard_content_main -->
            </div> <!-- نهاية dashboard_content -->
        </div> <!-- نهاية dashboard_content_container -->
    </div> <!-- نهاية الحاوية الرئيسية -->

    <!-- إشعار عائم للنجاح أو الخطأ -->
    <div class="floating-notification"></div>

    <!-- ملفات الجافاسكربت -->
<?php include('partials/app-script.php'); ?>
    <script>
        // كود الجافاسكربت لإدارة الحذف والتعديل
        $(document).ready(function() {
            // حذف المستخدم عبر AJAX
            $(document).on('click', '.deleteUser', function(e) {
                e.preventDefault();
                let $row = $(this).closest('tr');
                let userId = $(this).data('userid');
                let fname  = $(this).data('fname');
                let lname  = $(this).data('lname');
                let fullName = fname + ' ' + lname;

                Swal.fire({
                    title: 'تأكيد الحذف',
                    html: `<p style="font-size:16px; margin:0;">هل أنت متأكد من حذف المستخدم <strong style="color:rgb(229, 126, 7);">${fullName}</strong>؟</p>
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
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'database/delete-user.php',
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                userId: userId,
                                f_name: fname,
                                l_name: lname
                            },
                            success: function(response) {
                                if (response.status === true || response.status === 'true') {
                                    $row.fadeOut(400, function() {
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
                            error: function() {
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
                        console.log('تم إلغاء الحذف.');
                    }
                });
            });

            // تفعيل نموذج التعديل عند الضغط على زر التعديل
            $(document).on('click', '.editUser', function(e) {
                e.preventDefault();
                var userId = $(this).data('userid');
                $('.editRow').hide();
                $('#editRow-' + userId).show();
            });

            // إلغاء التعديل وإخفاء النموذج
            $(document).on('click', '.cancelEdit', function() {
                var userId = $(this).data('userid');
                $('#editRow-' + userId).hide();
            });

            // إرسال بيانات نموذج التعديل عبر AJAX
            $(document).on('submit', '.editForm', function(e) {
                e.preventDefault();
                var form = $(this);
                var userId = form.data('userid');
                var data = form.serialize() + '&userId=' + userId;
                
                $.ajax({
                    url: 'database/update-user.php',
                    method: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function(response) {
                        if(response.status === true || response.status === 'true'){
                            var row = $('#dataRow-' + userId);
                            row.find('.first_name').text(form.find('input[name="first_name"]').val());
                            row.find('.last_name').text(form.find('input[name="last_name"]').val());
                            row.find('.email').text(form.find('input[name="email"]').val());
                            $('#editRow-' + userId).hide();
                            Swal.fire({
                                title: 'تم التحديث!',
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
        });
    </script>
</body>
</html>
