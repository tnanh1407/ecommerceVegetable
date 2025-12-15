<div class="sidebar">
    <div class="sidebar__logo">
        ADMIN
    </div>
    <ul class="sidebar__menu">
        <?php $page = basename($_SERVER['PHP_SELF']); ?>

        <li><a href="dashboard.php" class="<?= $page == 'dashboard.php' ? 'active' : '' ?>">Tổng quan</a></li>
        <li><a href="admin_products.php" class="<?= $page == 'admin_products.php' ? 'active' : '' ?>">Quản lý Sản
                phẩm</a></li>
        <li><a href="admin_users.php" class="<?= $page == 'admin_users.php' ? 'active' : '' ?>">Quản lý Người dùng</a>
        </li>
        <li><a href="logout.php" style="color: #ff6b6b;">Đăng xuất</a></li>
    </ul>
</div>