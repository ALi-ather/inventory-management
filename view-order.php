<?php
session_start();
require_once('database/connection.php');

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// جلب بيانات الطلبات مع معلومات المنتج والمورّد والموظف
$stmt = $con->prepare("
    SELECT 
        op.id,
        op.batch,
        op.product,
        p.product_name,
        op.supplier,
        s.supplier_name,
        op.quantity_ordered,
        op.quantity_received,
        op.status,
        op.created_by,
        CONCAT(u.first_name, ' ', u.last_name) AS ordered_by,
        op.created_at
    FROM order_product op
    LEFT JOIN products p   ON op.product  = p.id
    LEFT JOIN suppliers s  ON op.supplier = s.id
    LEFT JOIN users u      ON op.created_by = u.id
    ORDER BY op.batch, op.created_at DESC
");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// تجميع الطلبات حسب الدفعة (batch)
$ordersByBatch = [];
foreach ($results as $row) {
    $batchNumber = $row['batch'] ?? 0;
    if (!isset($ordersByBatch[$batchNumber])) {
        $ordersByBatch[$batchNumber] = [];
    }
    $ordersByBatch[$batchNumber][] = $row;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عرض طلبات الشراء - نظام إدارة المخزون</title>
    <?php include('partials/app-header-scripts.php'); ?>
    <style>
        /* تنسيقات أساسية للجدول */
        .ordersTable {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        .ordersTable th, .ordersTable td {
            border: 1px solid #ddd;
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
        }
        /* شارات الحالة */
        .status-badge {
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .status-pending {
            background-color: #dc3545;
        }
        .status-complete {
            background-color: #28a745;
        }
        .status-incomplete {
            background-color: rgb(226, 233, 0);
        }
        .batch-title {
            margin-top: 2rem;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #666;
        }
        /* تنسيق صف نموذج التعديل المخفي */
        .editRow {
            display: none;
            background: #f9f9f9;
        }
        .editForm {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
        }
        .editForm label {
            margin: 0 5px;
        }
        .editForm input, .editForm select {
            margin: 5px;
            padding: 5px;
            min-width: 100px;
        }
        .editForm button {
            margin: 5px;
            padding: 5px 10px;
        }
        /* صندوق عرض الرسائل */
        #messageContainer {
            margin: 1rem 0;
        }
        .alert {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        /* زر Delivery History */
        .deliveryHistoryBtn {
            display: inline-block;
            background: #ff6a00;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
            text-decoration: none;
            margin-left: 5px;
            cursor: pointer;
        }
        .deliveryHistoryBtn:hover {
            background: #ff6a00;
        }
        /* نافذة (Modal) لعرض سجل التسليم */
        #deliveryHistoryContainer {
            display: none;
            position: fixed;
            top: 10%;
            left: 10%;
            width: 80%;
            height: 80%;
            background: #1e1e1e;
            color: #fff;
            overflow: auto;
            border: 2px solid #ff6a00;
            border-radius: 8px;
            z-index: 1000;
            padding: 20px;
        }
        #closeHistoryBtn {
            background: #ff4444;
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            float: right;
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
                <h1 class="section_title"><i class="fa fa-list"></i> قائمة طلبات الشراء</h1>
                <!-- صندوق عرض الرسائل (نجاح/خطأ) -->
                <div id="messageContainer"></div>

                <?php if (count($ordersByBatch) > 0): ?>
                    <?php foreach ($ordersByBatch as $batchNumber => $orders): ?>
                        <h3 class="batch-title">Batch #: <?= htmlspecialchars($batchNumber ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
                        <table class="ordersTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>PRODUCT</th>
                                    <th>QTY ORDERED</th>
                                    <th>QTY RECEIVED</th>
                                    <th>SUPPLIER</th>
                                    <th>STATUS</th>
                                    <th>ORDERED BY</th>
                                    <th>CREATED DATE</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $count = 1;
                                foreach ($orders as $order): 
                                    if ($order['status'] === 'complete') {
                                        $statusBadge = '<span class="status-badge status-complete">complete</span>';
                                    } elseif ($order['status'] === 'pending') {
                                        $statusBadge = '<span class="status-badge status-pending">pending</span>';
                                    } elseif ($order['status'] === 'incomplete') {
                                        $statusBadge = '<span class="status-badge status-incomplete">incomplete</span>';
                                    } else {
                                        $statusBadge = '<span class="status-badge">'.htmlspecialchars($order['status'] ?? '', ENT_QUOTES, 'UTF-8').'</span>';
                                    }
                                    $createdDate = $order['created_at'] ? date('Y-m-d H:i:s', strtotime($order['created_at'])) : '-';
                                ?>
                                <tr id="dataRow-<?= $order['id']; ?>">
                                    <td><?= $count; ?></td>
                                    <td><?= htmlspecialchars($order['product_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($order['quantity_ordered'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($order['quantity_received'] ?? 0, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($order['supplier_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= $statusBadge; ?></td>
                                    <td><?= htmlspecialchars($order['ordered_by'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= $createdDate; ?></td>
                                    <td>
                                        <!-- زر التعديل -->
                                        <a href="#" class="editOrder" 
                                           data-orderid="<?= $order['id']; ?>"
                                           data-qty_received="<?= htmlspecialchars($order['quantity_received'] ?? 0, ENT_QUOTES, 'UTF-8'); ?>"
                                           data-status="<?= htmlspecialchars($order['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                            <i class="fa fa-edit custom-edit-icon"></i>
                                        </a>
                                        <!-- زر عرض Delivery History مع معرّف الطلب في data attribute -->
                                        <a href="#" class="deliveryHistoryBtn" data-orderid="<?= $order['id']; ?>">
                                            Delivery History
                                        </a>
                                    </td>
                                </tr>
                                <!-- صف نموذج التعديل المخفي -->
                                <tr class="editRow" id="editRow-<?= $order['id']; ?>">
                                    <td colspan="9">
                                        <form class="editForm" data-orderid="<?= $order['id']; ?>">
                                            <label>QTY Delivered:</label>
                                            <input type="number" name="qty_delivered" min="0" value="0" required>
                                            <label>Status:</label>
                                            <select name="status" required>
                                                <option value="pending" <?= ($order['status'] === 'pending' ? 'selected' : ''); ?>>pending</option>
                                                <option value="complete" <?= ($order['status'] === 'complete' ? 'selected' : ''); ?>>complete</option>
                                                <option value="incomplete" <?= ($order['status'] === 'incomplete' ? 'selected' : ''); ?>>incomplete</option>
                                            </select>
                                            <button type="submit"><i class="fa fa-save"></i> حفظ</button>
                                            <button type="button" class="cancelEdit" data-orderid="<?= $order['id']; ?>">إلغاء</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php 
                                    $count++;
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>لا توجد طلبات شراء حالياً.</p>
                <?php endif; ?>
            </div><!-- نهاية dashboard_content_main -->
        </div><!-- نهاية dashboard_content -->
    </div><!-- نهاية dashboard_content_container -->
</div><!-- نهاية dashboardMainContainer -->

<!-- نافذة عرض سجل التسليم (Delivery History Modal) -->
<div id="deliveryHistoryContainer">
    <button id="closeHistoryBtn">إغلاق</button>
    <div id="historyContent"></div>
</div>

<?php include('partials/app-script.php'); ?>

<script>
    // دالة عرض الرسائل بنمط جمالي
    function showMessage(message, type = 'success') {
        const msgContainer = document.getElementById('messageContainer');
        msgContainer.innerHTML = '';
        const alertDiv = document.createElement('div');
        alertDiv.classList.add('alert');
        if (type === 'success') {
            alertDiv.classList.add('alert-success');
        } else {
            alertDiv.classList.add('alert-danger');
        }
        alertDiv.textContent = message;
        msgContainer.appendChild(alertDiv);
        setTimeout(() => {
            if (msgContainer.contains(alertDiv)) {
                alertDiv.remove();
            }
        }, 3000);
    }

    // إظهار نموذج التعديل عند الضغط على أيقونة التعديل
    document.querySelectorAll('.editOrder').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var orderId = this.getAttribute('data-orderid');
            // إخفاء كل نماذج التعديل المفتوحة
            document.querySelectorAll('.editRow').forEach(function(row) {
                row.style.display = 'none';
            });
            document.getElementById('editRow-' + orderId).style.display = 'table-row';
        });
    });

    // إخفاء نموذج التعديل عند الضغط على زر الإلغاء
    document.querySelectorAll('.cancelEdit').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var orderId = this.getAttribute('data-orderid');
            document.getElementById('editRow-' + orderId).style.display = 'none';
        });
    });

    // التعامل مع زر Delivery History باستخدام AJAX
    document.querySelectorAll('.deliveryHistoryBtn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var orderId = this.getAttribute('data-orderid');
            var url = 'database/view-delivery-history.php?orderId=' + orderId;
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    document.getElementById('historyContent').innerHTML = html;
                    document.getElementById('deliveryHistoryContainer').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching delivery history:', error);
                    showMessage('حدث خطأ أثناء تحميل سجل التسليم.', 'error');
                });
        });
    });

    // زر إغلاق النافذة (Modal)
    document.getElementById('closeHistoryBtn').addEventListener('click', function() {
        document.getElementById('deliveryHistoryContainer').style.display = 'none';
    });

    // إرسال بيانات نموذج التعديل عبر AJAX إلى update-order.php
    document.querySelectorAll('.editForm').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var orderId = this.getAttribute('data-orderid');
            var formData = new FormData(this);
            formData.append('orderId', orderId);

            fetch('database/update-order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    showMessage('تم تحديث الطلب بنجاح!', 'success');
                    var dataRow = document.getElementById('dataRow-' + orderId);
                    if (data.newQtyReceived) {
                        dataRow.querySelector('td:nth-child(4)').textContent = data.newQtyReceived;
                    }
                    var statusBadge = dataRow.querySelector('td:nth-child(6) span');
                    statusBadge.textContent = formData.get('status');
                    if (formData.get('status') === 'complete') {
                        statusBadge.className = 'status-badge status-complete';
                    } else if (formData.get('status') === 'incomplete') {
                        statusBadge.className = 'status-badge status-incomplete';
                    } else {
                        statusBadge.className = 'status-badge status-pending';
                    }
                    document.getElementById('editRow-' + orderId).style.display = 'none';
                } else {
                    showMessage(data.message || 'حدث خطأ أثناء تحديث الطلب.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showMessage('حدث خطأ أثناء تحديث الطلب.', 'error');
            });
        });
    });
</script>
</body>
</html>
