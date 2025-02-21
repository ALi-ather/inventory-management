        <div class="dashboard sidebar" id="dashboard_Sidebar">
            <h3 class="dashboard.Logo">IMS</h3>
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
                    <!-- class="menuActive" -->
                    <li>
                        <a href="./index.php">
                            <i class="fa fa-dashboard"></i>
                            <span class="menuText">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="./users-add.php">
                            <i class="fa fa-user-plus"></i>
                            <span class="menuText">Add User</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>