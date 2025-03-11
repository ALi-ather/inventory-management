<?php
$table_columns_mapping = [
    'users' => [
        'id'         => 'رقم المستخدم',
        'first_name' => 'الاسم الأول',
        'last_name'  => 'اسم العائلة',
        'email'      => 'البريد الإلكتروني',
        'password'   => 'كلمة المرور',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
        'permissions'=>'صلاحيات'
    ],
    'products' => [
        'id'          => 'رقم المنتج',
        'product_name' => 'اسم المنتج',
        'description' => 'الوصف',
        'img'         => 'الصورة',
        'created_by'  => 'المستخدم',
        'created_at'  => 'تاريخ الإنشاء',
        'updated_at'  => 'تاريخ التحديث',
        'suppliers'   => 'supplier'
    ],
    // أضف مصفوفة جدول الموردين:
    'suppliers' => [
        'id'               => 'رقم المورد',
        'supplier_name'    => 'اسم المورد',
        'supplier_location'=> 'موقع المورد',
        'email'            => 'البريد الإلكتروني',
        'created_by'       => 'تمت الإضافة بواسطة',
        'created_at'       => 'تاريخ الإنشاء',
        'updated_at'       => 'تاريخ التحديث'
    ]
];
