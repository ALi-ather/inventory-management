<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = 'localhost';
$username   = 'root';
$password   = '';


try {
    // استخدم المتغير $database بدلًا من كتابة 'inventory' مباشرة
    $con = new PDO("mysql:host=$servername;dbname=inventory", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // تمت إزالة echo 'connected successfully' لمنع إرسال أي مخرجات قبل استخدام header()
} catch (\Exception $e) {
    error_log('Connection failed: ' . $e->getMessage());
    die('حدث خطأ في الاتصال بقاعدة البيانات، الرجاء المحاولة لاحقًا.');
}
?>
