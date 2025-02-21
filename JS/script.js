        // التهيئة الأساسية
        let sideBarIsOpen = true;
        let lastScroll = 0;
        const toggle = document.getElementById('toggleBtn');
        const navbar = document.querySelector('.dashboard.topNav');
        const dashboard_sidebar = document.querySelector('.dashboard.sidebar');
        const notification = document.querySelector('.floating-notification');

        // دالة عرض الإشعارات
        function showNotification(message, type = 'success') {
            notification.textContent = message;
            notification.className = `floating-notification ${type} show`;
            setTimeout(() => notification.classList.remove('show'), 3000);
        }

        // تفعيل القوائم
        document.querySelectorAll('.dashboard.sidebar_menus ul li').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelector('.menuActive').classList.remove('menuActive');
                this.classList.add('menuActive');
                showNotification(`تم التبديل إلى ${this.querySelector('.menuText').textContent}`);
            });
        });

        // إدارة التمرير
        window.addEventListener('scroll', () => {
            const SCROLL_THRESHOLD = 100;
            const currentScroll = window.scrollY || document.documentElement.scrollTop;
            
            navbar.style.transform = currentScroll > lastScroll && currentScroll > SCROLL_THRESHOLD
                ? 'translateY(-100%)'
                : 'translateY(0)';
            
            lastScroll = currentScroll <= 0 ? 0 : currentScroll;
        });

        // تبديل الشريط الجانبي
        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            dashboard_sidebar.style.transition = 'width 0.3s ease-in-out';
            
            if (sideBarIsOpen) {
                dashboard_sidebar.style.width = '10%';
                document.querySelectorAll('.menuText').forEach(text => {
                    text.style.display = 'none';
                });
            } else {
                dashboard_sidebar.style.width = '20%';
                document.querySelectorAll('.menuText').forEach(text => {
                    text.style.display = 'block';
                });
            }
            
            sideBarIsOpen = !sideBarIsOpen;
            showNotification(sideBarIsOpen ? 'تم توسيع القائمة' : 'تم تصغير القائمة');
        });

        // تهيئة أولية عند التحميل
        window.addEventListener('DOMContentLoaded', () => {
            showNotification('مرحباً! النظام جاهز للاستخدام', 'success');
        });