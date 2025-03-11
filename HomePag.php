<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS Homepage - نظام إدارة المخزون</title>
    <link rel="stylesheet" type="text/css" href="CSS/login.css">
    <meta name="description" content="نظام إدارة المخزون يساعدك على تتبع وإدارة المخزون بكفاءة وسهولة.">
    <meta name="keywords" content="نظام إدارة المخزون, IMS, إدارة المخزون, تتبع المنتجات">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body id="homepage">

    <!-- ✅ رأس الصفحة -->
    <header class="header">
        <div class="homepageContiner">
            <a href="login.php">تسجيل الدخول</a>
        </div>
    </header>

    <!-- ✅ البانر الرئيسي -->
    <section class="banner">
        <div class="banner-content">
            <h1>IMS</h1>
            <h2>INVENTORY MANAGEMENT SYSTEM</h2>
            <p>تتبع منتجاتك عبر سلسلة التوريد من الشراء إلى المبيعات النهائية.</p>
            <div class="banner-icons">
                <a href="#"><i class="fab fa-apple"></i></a>
                <a href="#"><i class="fab fa-android"></i></a>
                <a href="#"><i class="fab fa-windows"></i></a>
                <a href="#"><i class="fab fa-linux"></i></a>
            </div>
        </div>
    </section>

    <!-- ✅ قسم الميزات -->
    <section class="features">
        <div class="feature-box">
            <span class="feature-icon"><i class="fas fa-cogs"></i></span>
            <h3>تخصيص مرن</h3>
            <p>قم بتعديل النظام ليناسب احتياجاتك بسهولة وبسرعة.</p>
        </div>
        <div class="feature-box">
            <span class="feature-icon"><i class="fas fa-star"></i></span>
            <h3>تصميم حديث</h3>
            <p>واجهة سهلة الاستخدام تعتمد على أحدث معايير التصميم.</p>
        </div>
        <div class="feature-box">
            <span class="feature-icon"><i class="fas fa-globe"></i></span>
            <h3>الوصول العالمي</h3>
            <p>إدارة المخزون من أي مكان باستخدام الأنظمة السحابية الحديثة.</p>
        </div>
    </section>

    <!-- ✅ قسم الإشعارات والفيديو -->
    <section class="homepageNotified">
        <div class="emailform">
            <h3>احصل على إشعارات التحديثات!</h3>
            <p>ابقَ على اطلاع دائم بالتحديثات والتعديلات الجديدة.</p>
            <form action="index.php" method="post">
                <input type="email" placeholder="أدخل بريدك الإلكتروني" required>
                <button type="submit">إشترك</button>
            </form>
        </div>
        <div class="video-container">
            <iframe src="https://www.youtube.com/embed/VIDEO_ID" frameborder="0" allowfullscreen></iframe>
        </div>
    </section>

    <!-- ✅ قسم التواصل الاجتماعي -->
    <section class="socials">
        <div class="homepageContainer">
            <h3 class="socialHeader">تواصل معنا</h3>
            <p class="socialText">ابقَ على تواصل عبر مواقع التواصل الاجتماعي.</p>
            <div class="homepage-socials">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-google-plus"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
    </section>

    <!-- ✅ تذييل الصفحة -->
    <footer class="footer">
        <div class="homepageContainer">
            <a href="">اتصل بنا</a>
            <a href="">تحميل</a>
            <a href="">الصحافة</a>
            <a href="">البريد الإلكتروني</a>
            <a href="">الدعم</a>
            <a href="">سياسة الخصوصية</a>
        </div>
    </footer>

</body>
</html>
