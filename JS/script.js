document.addEventListener('DOMContentLoaded', () => {
    let sideBarIsOpen = true;
    let lastScroll = 0;

    const toggle = document.getElementById('toggleBtn');
    const navbar = document.querySelector('.dashboard.topNav');
    const dashboard_sidebar = document.getElementById('dashboard_Sidebar');
    const notification = document.querySelector('.floating-notification');

    function showNotification(message, type = 'success') {
        notification.textContent = message;
        notification.className = `floating-notification ${type} show`;
        setTimeout(() => notification.classList.remove('show'), 3000);
    }

    // تفعيل القوائم الجانبية
    document.querySelectorAll('.dashboard.sidebar_menus ul li a').forEach(item => {
        item.addEventListener('click', function() {
            let activeItem = document.querySelector('.menuActive');
            if (activeItem) activeItem.classList.remove('menuActive');
            this.closest('li').classList.add('menuActive');
            showNotification(`تم التبديل إلى ${this.querySelector('.menuText').textContent}`);
        });
    });

    // تبديل الشريط الجانبي
    if (toggle) {
        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            dashboard_sidebar.style.transition = 'width 0.3s ease-in-out';
            if (sideBarIsOpen) {
                dashboard_sidebar.style.width = '10%';
                document.querySelectorAll('.menuText').forEach(text => text.style.display = 'none');
            } else {
                dashboard_sidebar.style.width = '20%';
                document.querySelectorAll('.menuText').forEach(text => text.style.display = 'block');
            }
            sideBarIsOpen = !sideBarIsOpen;
            showNotification(sideBarIsOpen ? 'تم توسيع القائمة' : 'تم تصغير القائمة');
        });
    }

    // إظهار / إخفاء القوائم الفرعية
    document.querySelectorAll('.showHideSubMenu').forEach(menu => {
        menu.addEventListener('click', function(e) {
            e.preventDefault();
            let mainMenu = this.closest('li');
            let subMenu = mainMenu.querySelector('.submenu');
            let mainMenuIcon = mainMenu.querySelector('.mainMenuIconArrow');

            // إغلاق جميع القوائم الفرعية الأخرى
            document.querySelectorAll('.submenu').forEach(sub => {
                if (sub !== subMenu) {
                    sub.style.display = 'none';
                    let icon = sub.closest('li').querySelector('.mainMenuIconArrow');
                    if (icon) {
                        icon.classList.remove('fa-angle-up');
                        icon.classList.add('fa-angle-down');
                    }
                }
            });

            // تبديل القائمة الفرعية المحددة
            if (subMenu.style.display === 'block') {
                subMenu.style.display = 'none';
                mainMenuIcon.classList.remove('fa-angle-up');
                mainMenuIcon.classList.add('fa-angle-down');
            } else {
                subMenu.style.display = 'block';
                mainMenuIcon.classList.remove('fa-angle-down');
                mainMenuIcon.classList.add('fa-angle-up');
            }
        });
    });

    // تمييز العنصر النشط عند تحميل الصفحة
    let path = window.location.pathname.split('/').pop();
    let curNavItem = document.querySelector(`.dashboard.sidebar_menus ul li a[href="${path}"]`);
    if (curNavItem) {
        let mainNav = curNavItem.closest('li');
        if (mainNav) {
            mainNav.classList.add('menuActive');
            let subMenu = mainNav.querySelector('.submenu');
            let mainMenuIcon = mainNav.querySelector('.mainMenuIconArrow');
            if (subMenu) subMenu.style.display = 'block';
            if (mainMenuIcon) {
                mainMenuIcon.classList.remove('fa-angle-down');
                mainMenuIcon.classList.add('fa-angle-up');
            }
        }
    }

    // إشعار ترحيبي
    showNotification('مرحباً! النظام جاهز للاستخدام', 'success');
});
