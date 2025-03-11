<?php
// إذا لم يكن متوفرًا متغير الصلاحيات للمستخدم (في حالة الإضافة)
if (!isset($userPermissions)) {
    $userPermissions = [];
}
?>
<style>
/* الحاوية الرئيسية لجميع البطاقات */
.permissions-container {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin: 1rem 0;
}

/* كل بطاقة تمثل قسماً من الصلاحيات */
.permission-card {
  background-color: #2b2b2b; /* خلفية داكنة */
  border-radius: 8px;
  padding: 1rem;
  flex: 1 1 calc(50% - 1rem); /* يعرض بطاقتين في كل صف تقريباً */
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  min-width: 250px; /* الحد الأدنى لعرض البطاقة */
}

/* عنوان القسم داخل البطاقة */
.permission-card h4 {
  margin: 0 0 0.5rem;
  color: #ff6600; /* لون برتقالي لعناوين الأقسام */
  font-size: 1.1rem;
}

/* حاوية مربعات الاختيار */
.permission-options {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

/* تنسيق النصوص داخل مربعات الاختيار */
.permission-options label {
  color: #fff;
  cursor: pointer;
  font-size: 0.95rem;
}

/* تنسيق لمربع الاختيار نفسه */
.permission-options input[type="checkbox"] {
  margin-right: 5px;
}
</style>

<div class="permissions-container">
    <!-- Dashboard -->
    <div class="permission-card">
        <h4>Dashboard</h4>
        <div class="permission-options">
            <label>
                <input type="checkbox" name="permissions[]" value="dashboard_view" <?= in_array('dashboard_view', $userPermissions) ? 'checked' : '' ?>>
                View
            </label>
        </div>
    </div>

    <!-- Reports -->
    <div class="permission-card">
        <h4>Reports</h4>
        <div class="permission-options">
            <label>
                <input type="checkbox" name="permissions[]" value="reports_view" <?= in_array('reports_view', $userPermissions) ? 'checked' : '' ?>>
                View
            </label>
        </div>
    </div>

    <!-- Product -->
    <div class="permission-card">
        <h4>Product</h4>
        <div class="permission-options">
            <label>
                <input type="checkbox" name="permissions[]" value="product_view" <?= in_array('product_view', $userPermissions) ? 'checked' : '' ?>>
                View
            </label>
            <label>
                <input type="checkbox" name="permissions[]" value="product_create_edit" <?= in_array('product_create_edit', $userPermissions) ? 'checked' : '' ?>>
                Create-Edit
            </label>
            <label>
                <input type="checkbox" name="permissions[]" value="product_delete" <?= in_array('product_delete', $userPermissions) ? 'checked' : '' ?>>
                Delete
            </label>
        </div>
    </div>

    <!-- Purchase Order -->
    <div class="permission-card">
        <h4>Purchase Order</h4>
        <div class="permission-options">
            <label>
                <input type="checkbox" name="permissions[]" value="purchase_order_view" <?= in_array('purchase_order_view', $userPermissions) ? 'checked' : '' ?>>
                View
            </label>
            <label>
                <input type="checkbox" name="permissions[]" value="purchase_order_create" <?= in_array('purchase_order_create', $userPermissions) ? 'checked' : '' ?>>
                Create
            </label>
            <label>
                <input type="checkbox" name="permissions[]" value="purchase_order_edit" <?= in_array('purchase_order_edit', $userPermissions) ? 'checked' : '' ?>>
                Edit
            </label>
        </div>
    </div>

    <!-- Users -->
    <div class="permission-card">
        <h4>Users</h4>
        <div class="permission-options">
            <label>
                <input type="checkbox" name="permissions[]" value="users_view" <?= in_array('users_view', $userPermissions) ? 'checked' : '' ?>>
                View
            </label>
            <label>
                <input type="checkbox" name="permissions[]" value="users_create" <?= in_array('users_create', $userPermissions) ? 'checked' : '' ?>>
                Create
            </label>
            <label>
                <input type="checkbox" name="permissions[]" value="users_edit" <?= in_array('users_edit', $userPermissions) ? 'checked' : '' ?>>
                Edit
            </label>
            <label>
                <input type="checkbox" name="permissions[]" value="users_delete" <?= in_array('users_delete', $userPermissions) ? 'checked' : '' ?>>
                Delete
            </label>
        </div>
    </div>

    <!-- Supplier -->
    <div class="permission-card">
        <h4>Supplier</h4>
        <div class="permission-options">
            <label>
                <input type="checkbox" name="permissions[]" value="supplier_view" <?= in_array('supplier_view', $userPermissions) ? 'checked' : '' ?>>
                View
            </label>
            <label>
                <input type="checkbox" name="permissions[]" value="supplier_create" <?= in_array('supplier_create', $userPermissions) ? 'checked' : '' ?>>
                Create
            </label>
            <label>
                <input type="checkbox" name="permissions[]" value="supplier_edit" <?= in_array('supplier_edit', $userPermissions) ? 'checked' : '' ?>>
                Edit
            </label>
            <label>
                <input type="checkbox" name="permissions[]" value="supplier_delete" <?= in_array('supplier_delete', $userPermissions) ? 'checked' : '' ?>>
                Delete
            </label>
        </div>
    </div>
</div>
