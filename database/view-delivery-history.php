<?php
session_start();
// بما أن هذا الملف داخل مجلد database، نستخدم __DIR__ للوصول لملف الاتصال في المجلد الرئيسي
require_once(__DIR__ . '/connection.php');

$orderId = $_GET['orderId'] ?? null;
if (!$orderId) {
    echo "لا يوجد معرف طلب محدد.";
    exit;
}

// استعلام لجلب سجل التسليم لهذا الطلب
$stmt = $con->prepare("
    SELECT qty_received, date_received, date_updated
    FROM order_product_history
    WHERE order_product_id = :orderId
    ORDER BY date_received DESC
");
$stmt->execute([':orderId' => $orderId]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سجل التسليم - طلب رقم <?= htmlspecialchars($orderId, ENT_QUOTES, 'UTF-8'); ?></title>
    <?php include(__DIR__ . '/../partials/app-header-scripts.php'); ?>
    <style>
        body {
            background-color: #1e1e1e;
            color: #fff;
            font-family: 'Tajawal', sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .historyTable {
            width: 100%;
            border-collapse: collapse;
            background-color: #2c2c2c;
            margin-bottom: 1rem;
        }
        .historyTable th, 
        .historyTable td {
            border: 1px solid #444;
            padding: 10px;
            text-align: center;
        }
        .historyTable thead {
            background: #ff6a00;
        }
        a {
            color: #ff6a00;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>سجل التسليم للطلب رقم <?= htmlspecialchars($orderId, ENT_QUOTES, 'UTF-8'); ?></h1>
    <?php if (count($history) > 0): ?>
        <table class="historyTable">
            <thead>
                <tr>
                    <th>الكمية المسلمة</th>
                    <th>تاريخ الاستلام</th>
                    <th>تاريخ التحديث</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['qty_received'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($record['date_received'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($record['date_updated'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center;">لا يوجد سجل تسليم لهذا الطلب.</p>
    <?php endif; ?>
</div>
<?php include(__DIR__ . '/../partials/app-script.php'); ?>
</body>
</html>
