<?php
/** [1] بدء الجلسة */
session_start();

/** [2] التحقق من وجود مستخدم مسجل الدخول */
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

/** [3] التحقق من اسم الجدول في الجلسة، إذا غير موجود نضعه users */
if (!isset($_SESSION['table'])) {
    $_SESSION['table'] = 'users'; // اسم الجدول الافتراضي
}

/** [4] تخزين بيانات المستخدم في متغير */
$user = $_SESSION['user'];

/** [5] جلب قائمة المستخدمين من ملف show-users.php الذي يعيد مصفوفة */
$users = include('database/show-users.php');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <!-- [6] إعدادات الـmeta والتعريفات الأساسية -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="نظام إدارة مخزون متكامل بإمكانيات متقدمة">
    <title>نظام إدارة المخزون - لوحة التحكم</title>

    <!-- [7] ربط ملف CSS الخاص بالتصميم -->
    <link rel="stylesheet" type="text/css" href="CSS/dashboard.css">

    <!-- [8] ربط أيقونات Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- [9] استيراد خط Tajawal من Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- [10] الحاوية الرئيسية للصفحة -->
    <div id="dashboardMainContainer">
        
        <!-- [11] تضمين القائمة الجانبية -->
        <?php include('partials/app-sidebar.php'); ?>

        <!-- [12] حاوية محتوى اللوحة -->
        <div class="dashboard_content_container">

            <!-- [13] تضمين شريط التنقل العلوي -->
            <?php include('partials/app-topnav.php'); ?>

            <!-- [14] المحتوى الرئيسي للوحة -->
            <div class="dashboard_content">
                <div class="dashboard_content_main">

                    <!-- [15] صف عام يحتضن عمودين: عمود إنشاء المستخدم وعمود قائمة المستخدمين -->
                    <div class="row">

                        <!-- [16] العمود الأول (column-5): إنشاء مستخدم جديد -->
                        <div class="column column-5">
                            <h1 class="section_title"><i class="fa fa-plus"></i> إنشاء مستخدم</h1>

                            <!-- [17] نموذج إضافة مستخدم جديد -->
                            <form action="database/add.php" class="appForm" method="POST">
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
                                <button type="submit"><i class="fa fa-send"></i> إضافة مستخدم</button>
                            </form>

                            <!-- [18] عرض رسالة النجاح أو الخطأ إن وجدت في الجلسة -->
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

                            <!-- [19] قسم إحصاءات أو أي محتوى إضافي -->
                            <div class="stats-container">
                                <!-- إضافة عناصر الإحصاءات هنا -->
                            </div>
                        </div>
                        <!-- نهاية العمود الأول -->

                        <!-- [20] العمود الثاني (column-7): عرض قائمة المستخدمين -->
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
                                            <!-- [21] تكرار صفوف الجدول بناءً على مصفوفة $users -->
                                            <?php foreach ($users as $index => $user): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                                                    <td><?= htmlspecialchars($user['last_name']) ?></td>
                                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                                                    <td><?= htmlspecialchars($user['updated_at']) ?></td>
                                                    <td>
                                                        <!-- [22] زر تعديل المستخدم -->
                                                        <a href="users-edit.php?id=<?= $user['id'] ?>" class="edit">
                                                            <i class="fa fa-edit custom-edit-icon"></i>
                                                        </a>

                                                        <!-- [23] زر الحذف عبر AJAX:
                                                             نستخدم href="#" لتفادي الانتقال لصفحة أخرى،
                                                             ونضع بيانات المستخدم في data-attributes -->
                                                        <a href="#"
                                                           class="deleteUser"
                                                           data-userid="<?= $user['id'] ?>"
                                                           data-fname="<?= $user['first_name'] ?>"
                                                           data-lname="<?= $user['last_name'] ?>">
                                                            <i class="fa fa-trash custom-trash-icon"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div> <!-- نهاية .users -->
                            </div> <!-- نهاية .section_content -->
                        </div> <!-- نهاية العمود الثاني -->

                    </div> <!-- نهاية الصف (row) -->
                </div> <!-- نهاية dashboard_content_main -->
            </div> <!-- نهاية dashboard_content -->
        </div> <!-- نهاية dashboard_content_container -->
    </div> <!-- نهاية الحاوية الرئيسية (dashboardMainContainer) -->

    <!-- [24] عنصر إشعار عائم للنجاح أو الخطأ -->
    <div class="floating-notification"></div>

    <!-- [25] ملف جافاسكربت العام -->
    <script src="JS/script.js"></script>

    <!-- [26] استدعاء jQuery (ضروري قبل كود AJAX) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- [27] كود الجافاسكربت لإدارة حدث الحذف عبر AJAX -->

    <script>
$(document).ready(function() {
    $(document).on('click', '.deleteUser', function(e) {
        e.preventDefault();

        // تخزين الصف الذي سنقوم بحذفه في حال نجاح العملية
        let $row = $(this).closest('tr');

        // جلب بيانات المستخدم من الخصائص data-*
        let userId = $(this).data('userid');
        let fname  = $(this).data('fname');
        let lname  = $(this).data('lname');
        let fullName = fname + ' ' + lname;

        // استخدام SweetAlert2 مع تصميم مخصص بالألوان المطلوبة
        Swal.fire({
            title: 'تأكيد الحذف',
            html: `<p style="font-size:16px; margin:0;">هل أنت متأكد من حذف المستخدم <strong style="color:rgb(229, 126, 7);">${fullName}</strong>؟</p>
                   <p style="font-size:14px; color:rgb(166, 0, 0); margin:10px 0 0;">لن يمكن استعادة البيانات بعد الحذف!</p>`,
            icon: 'warning',
            iconColor: 'rgb(88, 8, 8)', // أيقونة برتقالية
            background: '#000',   // خلفية سوداء
            showCancelButton: true,
            reverseButtons: true, // يعكس ترتيب الأزرار
            confirmButtonText: '<i class="fa fa-trash"></i> نعم، احذف',
            cancelButtonText: '<i class="fa fa-times"></i> إلغاء',
            confirmButtonColor: 'rgb(88, 8, 8)', // زر تأكيد برتقالي
            cancelButtonColor: '#28a745',   // زر إلغاء أخضر
        }).then((result) => {
            if (result.isConfirmed) {
                // إرسال طلب AJAX إلى ملف الحذف
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
                        // التأكد من نوع قيمة status (قد تكون boolean أو string)
                        if (response.status === true || response.status === 'true') {
                            // إزالة صف المستخدم من الجدول مع تأثير fadeOut
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
});
</script>
</script>


</body>
</html>
