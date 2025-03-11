<?php
session_start();
require_once('connection.php'); // تأكد أن الاتصال معرف (PDO)

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'طريقة الطلب غير صالحة.']);
    exit();
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$product_name = trim($_POST['product_name'] ?? '');
$description  = trim($_POST['description'] ?? '');
$suppliers    = isset($_POST['suppliers']) && is_array($_POST['suppliers']) ? $_POST['suppliers'] : [];

if ($id <= 0 || empty($product_name) || empty($description)) {
    echo json_encode(['status' => false, 'message' => 'بيانات غير صالحة.']);
    exit();
}

// تحديد مسار رفع الصور (مسار مطلق للمجلد)
$uploadDirAbsolute = __DIR__ . '/../uploads/product/';
$uploadDirRelative = 'uploads/product/';

// التأكد من وجود المجلد، وإن لم يكن موجودًا ننشئه
if (!is_dir($uploadDirAbsolute)) {
    mkdir($uploadDirAbsolute, 0777, true);
}

$imgPath = null;
if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath   = $_FILES['product_img']['tmp_name'];
    $fileName      = $_FILES['product_img']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName   = md5(time() . $fileName) . '.' . $fileExtension;
        $destAbsolute  = $uploadDirAbsolute . $newFileName;
        $destRelative  = $uploadDirRelative . $newFileName;
        
        if (move_uploaded_file($fileTmpPath, $destAbsolute)) {
            $imgPath = $destRelative; // سنخزن المسار النسبي في قاعدة البيانات
        } else {
            echo json_encode(['status' => false, 'message' => 'حدث خطأ أثناء رفع الصورة.']);
            exit();
        }
    } else {
        echo json_encode(['status' => false, 'message' => 'صيغة الصورة غير مدعومة.']);
        exit();
    }
}

try {
    // بدء المعاملة لضمان عدم حدوث أخطاء جزئية
    $con->beginTransaction();

    // تحديث بيانات المنتج مع أو بدون الصورة الجديدة
    if ($imgPath) {
        // جلب الصورة القديمة لحذفها إن وُجدت
        $stmt = $con->prepare("SELECT img FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $oldProduct = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($oldProduct && !empty($oldProduct['img']) && $oldProduct['img'] !== 'uploads/product/default.png' && file_exists(__DIR__ . '/../' . $oldProduct['img'])) {
            unlink(__DIR__ . '/../' . $oldProduct['img']);
        }

        $stmt = $con->prepare("UPDATE products SET product_name = ?, description = ?, img = ? WHERE id = ?");
        $stmt->execute([$product_name, $description, $imgPath, $id]);
    } else {
        $stmt = $con->prepare("UPDATE products SET product_name = ?, description = ? WHERE id = ?");
        $stmt->execute([$product_name, $description, $id]);
    }

    // تحديث جدول العلاقة بين المنتجات والموردين:
    // حذف العلاقات القديمة
    $stmt = $con->prepare("DELETE FROM productsupplier WHERE product = ?");
    $stmt->execute([$id]);

    // إدخال العلاقات الجديدة إن وُجدت
    if (!empty($suppliers)) {
        $stmtInsert = $con->prepare("INSERT INTO productsupplier (product, supplier) VALUES (?, ?)");
        foreach ($suppliers as $supplier_id) {
            $supplier_id = intval($supplier_id);
            if ($supplier_id > 0) {
                $stmtInsert->execute([$id, $supplier_id]);
            }
        }
    }

    $con->commit();
    echo json_encode(['status' => true, 'message' => 'تم تحديث المنتج والموردين بنجاح.']);
} catch (PDOException $e) {
    $con->rollBack();
    echo json_encode(['status' => false, 'message' => 'حدث خطأ أثناء تحديث المنتج: ' . $e->getMessage()]);
}
exit();
?>
