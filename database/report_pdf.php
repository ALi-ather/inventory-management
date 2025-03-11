<?php
// database/report_pdf.php
require_once('connection.php');  // الاتصال بقاعدة البيانات
require_once('../fpdf186/fpdf.php');

// استقبال نوع التقرير
$type = isset($_GET['report']) ? $_GET['report'] : '';

// تحديد العناوين
$mapping_filenames = [
    'products'        => 'تقرير المنتجات',
    'suppliers'       => 'تقرير الموردين',
    'deliveries'      => 'تقرير التوصيلات',
    'purchase_orders' => 'تقرير طلبات الشراء'
];

// التحقق من صحة النوع
if (!array_key_exists($type, $mapping_filenames)) {
    die("Invalid report type.");
}

// جلب البيانات المناسبة
$data = [];
$query = "";
switch ($type) {
    case 'products':
        $query = "SELECT * FROM products ORDER BY created_at DESC";
        break;
    case 'suppliers':
        $query = "SELECT * FROM suppliers ORDER BY created_at DESC";
        break;
    case 'deliveries':
        $query = "SELECT * FROM order_product_history ORDER BY date_updated DESC";
        break;
    case 'purchase_orders':
        $query = "SELECT * FROM order_product ORDER BY created_at DESC";
        break;
}

$stmt = $con->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// إنشاء كلاس مخصص لملف الـPDF لإضافة هيدر وفوتر
class MyPDF extends FPDF
{
    public $title = ''; // إصلاح المشكلة بتعريف خاصية title

    // يمكنك إضافة شعار أو نص أعلى كل صفحة
    function Header()
    {
        // ضبط الخط
        $this->SetFont('Arial', 'B', 15);

        // خلفية المستطيل العلوي
        $this->SetFillColor(255, 102, 0); // برتقالي
        $this->SetTextColor(255, 255, 255); // أبيض للنص

        // عرض عنوان التقرير في منتصف الصفحة
        $this->Cell(0, 15, $this->title, 0, 1, 'C', true);

        // مسافة بعد العنوان
        $this->Ln(5);

        // إعادة اللون الافتراضي للنص (أسود)
        $this->SetTextColor(0, 0, 0);
    }

    // فوتر الصفحة: يظهر أسفل كل صفحة
    function Footer()
    {
        // مكان الطباعة 15 ملم من أسفل الصفحة
        $this->SetY(-15);

        // اختيار نوع الخط وحجمه
        $this->SetFont('Arial', 'I', 8);

        // نص في الفوتر (رقم الصفحة)
        $this->Cell(0, 10, 'الصفحة ' . $this->PageNo() . ' / {nb}', 0, 0, 'C');
    }

    // دالة لطباعة جدول بهيئة جميلة
    function FancyTable($header, $data)
    {
        // ألوان للخلفية والخطوط
        $this->SetFillColor(0, 0, 0); // خلفية العناوين (أسود)
        $this->SetTextColor(255, 255, 255); // لون نص العناوين (أبيض)
        $this->SetDrawColor(255, 255, 255); // لون الإطار
        $this->SetLineWidth(.3);
        $this->SetFont('Arial', 'B', 10);

        // عرض وارتفاع الخلية
        $cellWidth  = 40;
        $cellHeight = 10;

        // طباعة عناوين الأعمدة
        foreach ($header as $col) {
            $this->Cell($cellWidth, $cellHeight, $col, 1, 0, 'C', true);
        }
        $this->Ln();

        // إعادة الألوان والضبط العادي للصفوف
        $this->SetFillColor(224, 224, 224); // لون تعبئة للصفوف المتعاقبة
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 9);

        $fill = false; // للتحكم بتعبئة خلفية كل صف بالتناوب
        foreach ($data as $row) {
            foreach ($header as $col) {
                // قيمة الحقل
                $value = isset($row[$col]) ? $row[$col] : '';

                // إذا كان العمود هو الصورة
                if ($col === 'img' && !empty($value)) {
                    $image_path = '../uploads/product/' . $value;
                    if (file_exists($image_path)) {
                        $startX = $this->GetX();
                        $startY = $this->GetY();

                        // خلية فارغة بنفس المساحة
                        $this->Cell($cellWidth, $cellHeight, '', 1, 0, 'C', $fill);

                        // رسم الصورة داخل الخلية
                        $this->Image($image_path, $startX + 2, $startY + 2, $cellWidth - 4, $cellHeight - 4);
                    } else {
                        $this->Cell($cellWidth, $cellHeight, 'No Image', 1, 0, 'C', $fill);
                    }
                } else {
                    // خلية نصية عادية
                    $this->Cell($cellWidth, $cellHeight, $value, 1, 0, 'C', $fill);
                }
            }
            $this->Ln();
            $fill = !$fill; // تبديل التعبئة للصف التالي
        }
    }
}

// إنشاء كائن PDF مخصص
$pdf = new MyPDF('L', 'mm', 'A4');

// إسناد العنوان للخاصية title
$pdf->title = $mapping_filenames[$type];

// تعيين عنوان الـPDF (لبيانات الميتا) - اختياري
$pdf->SetTitle($mapping_filenames[$type], true);

$pdf->AliasNbPages();  // لتتبع عدد الصفحات في الفوتر
$pdf->AddPage();

// إذا كان هناك بيانات
if (!empty($data)) {
    // العناوين هي مفاتيح أول صف
    $headers = array_keys($data[0]);

    // طباعة الجدول
    $pdf->FancyTable($headers, $data);
} else {
    // في حال لا توجد بيانات
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'لا توجد بيانات.', 1, 1, 'C');
}

// إخراج ملف PDF للعرض في المتصفح
$pdf->Output($mapping_filenames[$type] . '.pdf', 'I');
