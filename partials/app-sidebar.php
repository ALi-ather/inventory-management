<div class="dashboard sidebar" id="dashboard_Sidebar">
    <h3 class="dashboard-logo">IMS</h3>
    <div class="dashboard sidebar_user">
        <img src="CSS/IMG/th.jpg"
            alt="صورة المستخدم"
            id="userImage"
            loading="lazy"
            decoding="async">
        <span><?= $user['first_name'] . ' ' . $user['last_name'] ?></span>
    </div>

    <div class="dashboard sidebar_menus">
        <ul class="dashboard menu lists">
            <!-- Dashboard -->
            <li class="liMainMenu">
                <a href="./index.php">
                    <i class="fa fa-dashboard"></i>
                    <span class="menuText">Dashboard</span>
                </a>
            </li>
            <li class="liMainMenu">
                <a href="./report.php">
                    <i class="fa fa-file"></i>
                    <span class="menuText">Reports</span>
                </a>
            </li>
            <!-- Products Management -->
            <li class="liMainMenu">
                <a href="javascript:void(0)" class="showHideSubMenu">
                    <i class="fa fa-plus"></i>
                    <span class="menuText">Products Management</span>
                    <i class="fa fa-angle-left mainMenuIconArrow"></i>
                </a>
                <ul class="fa fa-hoose submenu">
                    <li><a href="./product-view.php"><i class="fa fa-circle-o"></i>View Product</a></li>
                    <li><a href="./product-add.php"><i class="fa fa-circle-o"></i>Add Product</a></li>
                </ul>
            </li>
            <!-- Supplier Management -->
            <li class="liMainMenu">
                <a href="javascript:void(0)" class="showHideSubMenu">
                    <i class="fa fa-truck"></i>
                    <span class="menuText">Supplier Management</span>
                    <i class="fa fa-angle-left mainMenuIconArrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="./suppliers-view.php"><i class="fa fa-circle-o"></i>View Supplier</a></li>
                    <li><a href="./supplier-add.php"><i class="fa fa-circle-o"></i>Add Supplier</a></li>
                </ul>
            </li>
                        <!-- Supplier Management -->
                        <li class="liMainMenu">
                <a href="javascript:void(0)" class="showHideSubMenu">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="menuText">purchase Order</span>
                    <i class="fa fa-angle-left mainMenuIconArrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="./product-order.php"><i class="fa fa-circle-o"></i>Creat Order</a></li>
                    <li><a href="./view-order.php"><i class="fa fa-circle-o"></i>View Order</a></li>                </ul>
            </li>
            <!-- Users Management -->
            <li class="liMainMenu">
                <a href="javascript:void(0)" class="showHideSubMenu">
                    <i class="fa fa-user"></i>
                    <span class="menuText">Users Management</span>
                    <i class="fa fa-angle-left mainMenuIconArrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="./users-view.php"><i class="fa fa-circle-o"></i>View Users</a></li>
                    <li><a href="./users-add.php"><i class="fa fa-circle-o"></i>Add Users</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
