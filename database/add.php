<?php
session_start();

// تحديد الجدول الافتراضي في الجلسة إذا لم يكن محدداً
if (!isset($_SESSION['table'])) {
    $_SESSION['table'] = 'users';
}

$table_name = $_SESSION['table'];

// السماح فقط بالطلبات من نوع POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method.');
}

// تفعيل عرض الأخطاء لأغراض التطوير
error_reporting(E_ALL);
ini_set('display_errors', 1);

// تضمين خريطة أعمدة الجداول
include 'table_columns.php';
if (!isset($table_columns_mapping[$table_name])) {
    exit('Invalid table mapping.');
}

$columns = $table_columns_mapping[$table_name];

// تضمين الاتصال بقاعدة البيانات
include 'connection.php';

try {
    
    if ($table_name === 'users') {
        // **إضافة مستخدم جديد**
        $first_name = $_POST['first_name'] ?? '';
        $last_name  = $_POST['last_name'] ?? '';
        $email      = $_POST['email'] ?? '';
        $password   = $_POST['password'] ?? '';
        
        // التقاط الصلاحيات من النموذج (إن وُجدت) وإلا استخدام مصفوفة فارغة
        $permissions = isset($_POST['permissions']) && is_array($_POST['permissions']) ? $_POST['permissions'] : [];
        
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
            throw new Exception('الرجاء ملء جميع الحقول المطلوبة.');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('تنسيق البريد الإلكتروني غير صالح.');
        }
        
        $encrypted = password_hash($password, PASSWORD_DEFAULT);
        
        // ترميز الصلاحيات بصيغة JSON
        $permissions_encoded = json_encode($permissions);
        
        // تعديل استعلام الإدخال لإضافة عمود الصلاحيات
        $command = "INSERT INTO users (first_name, last_name, email, password, permissions, created_at, updated_at)
                    VALUES (:first_name, :last_name, :email, :encrypted, :permissions, NOW(), NOW())";
        
        $stmt = $con->prepare($command);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name',  $last_name);
        $stmt->bindParam(':email',      $email);
        $stmt->bindParam(':encrypted',  $encrypted);
        $stmt->bindValue(':permissions', $permissions_encoded);
        $stmt->execute();
        
    } elseif ($table_name === 'products') {
        // **إضافة منتج جديد**
        if (!isset($_SESSION['user']['id'])) {
            throw new Exception('يجب تسجيل الدخول لإضافة منتج.');
        }

        $product_name = $_POST['product_name'] ?? '';
        $description  = $_POST['description'] ?? '';
        $created_by   = $_SESSION['user']['id'];

        if (empty($product_name) || empty($description)) {
            throw new Exception('الرجاء إدخال اسم المنتج والوصف.');
        }

        // التحقق من صحة المستخدم
        $stmt_check = $con->prepare("SELECT id FROM users WHERE id = :id");
        $stmt_check->bindParam(':id', $created_by);
        $stmt_check->execute();
        if ($stmt_check->rowCount() == 0) {
            throw new Exception('المستخدم غير موجود.');
        }

        // **⬇️ معالجة رفع الصورة ⬇️**
        $uploadDirAbsolute = __DIR__ . '/../uploads/product/';
        $uploadDirRelative = 'uploads/product/';
        $imgPath = 'uploads/product/default.png';

        if (!is_dir($uploadDirAbsolute)) {
            mkdir($uploadDirAbsolute, 0777, true);
        }

        if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['product_img']['tmp_name'];
            $fileNameCmps = explode(".", $_FILES['product_img']['name']);
            $fileExtension = strtolower(end($fileNameCmps));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = md5(time() . $_FILES['product_img']['name']) . '.' . $fileExtension;
                $destAbsolute = "$uploadDirAbsolute$newFileName";
                $destRelative = "$uploadDirRelative$newFileName";

                if (move_uploaded_file($fileTmpPath, $destAbsolute)) {
                    $imgPath = $destRelative;
                }
            } else {
                throw new Exception('نوع الصورة غير مدعوم.');
            }
        }

        // **⬇️ بدء المعاملة (Transaction) لضمان الإدخال الصحيح ⬇️**
        $con->beginTransaction();

        // **إدخال المنتج في جدول `products`**
        $command = "INSERT INTO products (product_name, description, img, created_by, created_at, updated_at)
                    VALUES (:product_name, :description, :img, :created_by, NOW(), NOW())";

        $stmt = $con->prepare($command);
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':description',  $description);
        $stmt->bindParam(':img',          $imgPath);
        $stmt->bindParam(':created_by',   $created_by);

        if (!$stmt->execute()) {
            throw new Exception('فشل في إدخال المنتج.');
        }

        // **الحصول على معرف المنتج المدخل**
        $product_id = $con->lastInsertId();

        // **إدخال الموردين المرتبطين بالمنتج في جدول `productsupplier`**
        if (isset($_POST['suppliers']) && is_array($_POST['suppliers'])) {
            $insertJunction = "INSERT INTO productsupplier (product, supplier) VALUES (:product, :supplier)";
            $stmtJunction = $con->prepare($insertJunction);
            foreach ($_POST['suppliers'] as $supplier_id) {
                $stmtJunction->bindParam(':product', $product_id);
                $stmtJunction->bindParam(':supplier', $supplier_id);
                if (!$stmtJunction->execute()) {
                    throw new Exception('فشل في ربط المنتج بالمورد.');
                }
            }
        }
        
        // **إنهاء المعاملة**
        $con->commit();
        
    } elseif ($table_name === 'suppliers') {
        // **إضافة مورد جديد**
        if (!isset($_SESSION['user']['id'])) {
            throw new Exception('يجب تسجيل الدخول لإضافة مورد.');
        }

        $supplier_name     = $_POST['supplier_name'] ?? '';
        $supplier_location = $_POST['supplier_location'] ?? '';
        $email             = $_POST['email'] ?? '';
        $created_by        = $_SESSION['user']['id'];

        if (empty($supplier_name) || empty($supplier_location) || empty($email)) {
            throw new Exception('الرجاء إدخال اسم المورد، موقع المورد والبريد الإلكتروني.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('تنسيق البريد الإلكتروني غير صالح.');
        }

        // بدء المعاملة
        $con->beginTransaction();

        $command = "INSERT INTO suppliers (supplier_name, supplier_location, email, created_by, created_at, updated_at)
                    VALUES (:supplier_name, :supplier_location, :email, :created_by, NOW(), NOW())";

        $stmt = $con->prepare($command);
        $stmt->bindParam(':supplier_name', $supplier_name);
        $stmt->bindParam(':supplier_location', $supplier_location);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':created_by', $created_by);

        if (!$stmt->execute()) {
            throw new Exception('فشل في إدخال المورد.');
        }

        $con->commit();

    } else {
        throw new Exception('نوع الجدول غير مدعوم.');
    }

    $_SESSION['response'] = [
        'status'  => true,
        'message' => 'تمت الإضافة بنجاح.'
    ];
} catch (PDOException $e) {
    if ($con->inTransaction()) {
        $con->rollBack();
    }
    $_SESSION['response'] = [
        'status'  => false,
        'message' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()
    ];
} catch (Exception $ex) {
    if ($table_name === 'products' && $con->inTransaction()) {
        $con->rollBack();
    }
    $_SESSION['response'] = [
        'status'  => false,
        'message' => 'خطأ: ' . $ex->getMessage()
    ];
}

// **إعادة التوجيه حسب نوع الجدول**
if ($table_name === 'users') {
    header('Location: ../users-add.php');
} elseif ($table_name === 'products') {
    header('Location: ../product-add.php');
} elseif ($table_name === 'suppliers') {
    header('Location: ../supplier-add.php');
}
exit();
?>