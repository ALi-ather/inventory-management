<?php
// database/report_csv.php
require_once('connection.php'); // الاتصال بقاعدة البيانات

// استلام نوع التقرير
$type = isset($_GET['report']) ? $_GET['report'] : '';

// مصفوفة أنواع التقارير وعناوين الملفات
$mapping_filenames = [
    'products'         => 'Product Report',
    'suppliers'        => 'Supplier Report',
    'deliveries'       => 'Deliveries Report',
    'purchase_orders'  => 'Purchase Orders Report'
];

// إذا كان النوع غير موجود في المصفوفة، يعرض رسالة خطأ
if (!array_key_exists($type, $mapping_filenames)) {
    die("Invalid report type.");
}

// اسم الملف (سيكون اسم التقرير + .xls)
$file_name = $mapping_filenames[$type] . '.xls';

// تهيئة الرؤوس لجعل المتصفح ينزّل الملف كملف Excel (أو TSV)
header("Content-Disposition: attachment; filename=\"$file_name\"");
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");

// تنفيذ الاستعلام بناءً على نوع التقرير
$data = [];

switch ($type) {
    case 'products':
        // جدول المنتجات
        $stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'suppliers':
        // جدول الموردين
        $stmt = $conn->prepare("SELECT * FROM suppliers ORDER BY created_at DESC");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'deliveries':
        // جدول التوصيلات (order_product_history)
        $stmt = $conn->prepare("SELECT * FROM order_product_history ORDER BY date_updated DESC");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'purchase_orders':
        // جدول طلبات الشراء (order_product)
        $stmt = $conn->prepare("SELECT * FROM order_product ORDER BY created_at DESC");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;
}

// إذا توجد بيانات
if (!empty($data)) {
    // طباعة عناوين الأعمدة
    $is_header = true;
    foreach ($data as $row) {
        if ($is_header) {
            echo implode("\t", array_keys($row)) . "\n";
            $is_header = false;
        }

        // معالجة النصوص لمنع المشاكل مع علامات التبويب أو السطور الجديدة
        array_walk($row, function(&$str) {
            $str = preg_replace("/\t/", "\\t", $str);
            $str = preg_replace("/\r?\n/", "\\n", $str);
            if (strstr($str, '"')) {
                $str = '"' . str_replace('"', '""', $str) . '"';
            }
        });

        // طباعة الصف
        echo implode("\t", array_values($row)) . "\n";
    }
} else {
    echo "No data found.\n";
}

exit;
