<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];

// الحصول على صلاحيات المستخدم (تأكد من أن الحقل يحمل صلاحيات المستخدم)
$permissions = isset($user['permissions']) ? $user['permissions'] : [];

// إذا كانت الصلاحيات محفوظة كسلسلة JSON، نقوم بفك الترميز
if (is_string($permissions)) {
    $permissions = json_decode($permissions, true);
}
if (!is_array($permissions)) {
    $permissions = [];
}

// التحقق من وجود صلاحية عرض لوحة التحكم (dashboard_view)
if (!in_array('dashboard_view', $permissions)) {
    // عرض رسالة للمستخدم في حال عدم وجود الصلاحية وعدم تحميل باقي الصفحة
    ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>غير مصرح لك</title>
        <style>
            body {
                background-color: #f2f2f2;
                font-family: Arial, sans-serif;
                text-align: center;
                padding: 50px;
            }
            .message {
                background-color: #fff;
                border: 1px solid #ddd;
                padding: 20px;
                border-radius: 5px;
                display: inline-block;
            }
        </style>
    </head>
    <body>
        <div class="message">
            <h1>غير مصرح لك</h1>
            <p>عذراً، ليس لديك صلاحية عرض لوحة التحكم.</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// إذا كان لديه الصلاحية، نكمل تحميل باقي الصفحة

// جلب بيانات المخططات
include('database/po_status_pie_graph.php');
include('database/supplier_prodct_bar_graph.php');
include('database/delivery_history.php');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="نظام إدارة مخزون متكامل بإمكانيات متقدمة">
    <title>نظام إدارة المخزون - لوحة التحكم</title>
    
    <!-- CSS الرئيسي للوحة التحكم -->
    <link rel="stylesheet" type="text/css" href="CSS/dashboard.css">
    <!-- أيقونات Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- خط Tajawal -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        /* تنسيقات إضافية للمخططات بوضع داكن */
        .highcharts-figure {
            margin: 1rem 0;
            padding: 1rem;
            background: #000; /* خلفية سوداء */
            border: 1px solid #444;
            border-radius: 4px;
            color: #fff;
        }
        .highcharts-description {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #fff;
        }
    </style>
</head>
<body>
    <div id="dashboardMainContainer">
        <!-- الشريط الجانبي -->
        <?php include('partials/app-sidebar.php'); ?>

        <div class="dashboard_content_container">
            <!-- الشريط العلوي -->
            <?php include('partials/app-topnav.php'); ?>

            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <h1>مرحباً بك في النظام</h1>

                    <div class="stats-container">
                        <!-- المخطط الأول (Pie Chart) لعرض حالات الطلب -->
                        <figure class="highcharts-figure">
                            <div id="pieContainer"></div>
                            <p class="highcharts-description">
                                هذا المخطط يوضح توزيع حالات طلبات الشراء في النظام:
                                <strong>incomplete</strong>, <strong>pending</strong>, <strong>complete</strong>.
                            </p>
                        </figure>

                        <!-- المخطط الثاني (Column Chart) لعرض عدد المنتجات لكل مورّد -->
                        <figure class="highcharts-figure">
                            <div id="barContainer"></div>
                            <p class="highcharts-description">
                                هذا المخطط يوضح عدد المنتجات المرتبطة بكل مورّد في النظام.
                            </p>
                        </figure>
                        
                        <!-- المخطط الثالث (Spline Chart) لعرض بيانات سجل التوصيل -->
                        <figure class="highcharts-figure">
                            <div id="lineContainer"></div>
                            <p class="highcharts-description">
                                مخطط spline يعرض بيانات سجل التوصيل مع تسميات السلاسل لتحسين القراءة.
                            </p>
                        </figure>
                    </div><!-- نهاية stats-container -->
                </div><!-- نهاية dashboard_content_main -->
            </div><!-- نهاية dashboard_content -->
        </div><!-- نهاية dashboard_content_container -->
    </div><!-- نهاية dashboardMainContainer -->

    <div class="floating-notification"></div>

    <!-- سكربتات خاصة بالمشروع -->
    <script src="JS/script.js"></script>

    <!-- مكتبات Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <!-- إضافة وحدة series-label لتحسين قراءة السلاسل -->
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
    // =============== المخطط الدائري (حالات الطلب) ===============
    Highcharts.chart('pieContainer', {
        chart: {
            type: 'pie',
            backgroundColor: '#171716'
        },
        title: {
            text: 'حالة طلبات الشراء',
            style: { color: '#fff' }
        },
        tooltip: {
            valueSuffix: ' طلب',
            style: { color: '#fff' }
        },
        plotOptions: {
            series: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: [{
                    enabled: true,
                    distance: 20,
                    style: { color: '#fff' }
                }, {
                    enabled: true,
                    distance: -40,
                    format: '{point.percentage:.1f}%',
                    style: {
                        fontSize: '1.2em',
                        textOutline: 'none',
                        opacity: 0.7,
                        color: '#fff'
                    },
                    filter: {
                        operator: '>',
                        property: 'percentage',
                        value: 10
                    }
                }]
            }
        },
        series: [{
            name: 'عدد الطلبات',
            colorByPoint: true,
            data: [
                {
                    name: 'incomplete',
                    y: <?php echo (int)$incompleteCount; ?>,
                    color: '#ff6a00'
                },
                {
                    name: 'pending',
                    y: <?php echo (int)$pendingCount; ?>,
                    color: '#dc3545'
                },
                {
                    name: 'complete',
                    y: <?php echo (int)$completeCount; ?>,
                    color: '#28a745',
                    sliced: true,
                    selected: true
                }
            ]
        }]
    });

    // =============== المخطط العمودي (المورّد والمنتجات) ===============
    Highcharts.chart('barContainer', {
        chart: {
            type: 'column',
            backgroundColor: '#171716'
        },
        title: {
            text: 'عدد المنتجات لكل مورّد',
            style: { color: '#fff' }
        },
        xAxis: {
            categories: <?php echo json_encode($supplierNames, JSON_UNESCAPED_UNICODE); ?>,
            crosshair: true,
            labels: { style: { color: '#fff' } },
            accessibility: {
                description: 'أسماء المورّدين'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'عدد المنتجات',
                style: { color: '#fff' }
            },
            labels: { style: { color: '#fff' } }
        },
        tooltip: {
            valueSuffix: ' منتج',
            style: { color: '#fff' }
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Products',
            data: <?php echo json_encode($productCounts, JSON_NUMERIC_CHECK); ?>,
            color: '#ff6a00'
        }]
    });

    // =============== المخطط السلس (Spline Chart) لبيانات سجل التوصيل ===============
    Highcharts.chart('lineContainer', {
        chart: {
            type: 'spline',
            backgroundColor: '#171716'
        },
        title: {
            text: 'بيانات سجل التوصيل',
            align: 'left',
            style: { color: '#fff' }
        },
        subtitle: {
            text: 'عرض للبيانات المستخرجة من قاعدة البيانات',
            align: 'left',
            style: { color: '#fff' }
        },
        xAxis: {
            categories: <?php echo json_encode($deliveryDates, JSON_UNESCAPED_UNICODE); ?>,
            accessibility: {
                rangeDescription: 'تواريخ سجل التوصيل'
            },
            labels: { style: { color: '#fff' } }
        },
        yAxis: {
            title: {
                text: 'القيمة',
                style: { color: '#fff' }
            },
            labels: { style: { color: '#fff' } }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            itemStyle: { color: '#fff' }
        },
        plotOptions: {
            series: {
                label: {
                    connectorAllowed: false
                }
            }
        },
        series: [{
            name: 'سجل التوصيل',
            data: <?php echo json_encode($deliveryTotals, JSON_NUMERIC_CHECK); ?>
        }],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }
    });
    </script>
</body>
</html>
