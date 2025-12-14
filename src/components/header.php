<?php
// Kiểm tra session đã được start chưa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = false;
$username = '';
$role = ''; // Khởi tạo biến role

if (isset($_SESSION['user'])) {
    $isLoggedIn = true;
    $username = $_SESSION['user'];
    // Lấy role từ session nếu có (được set lúc login)
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
}
?>
<header class="header container-full">
    <div class="header__left">
        <div class="header__logo">
            <img src="./assets/images/logo.png" alt="img_logo " />
        </div>
        <a href="index.php">Trang chủ</a>
        <a href="products.php">Sản phẩm</a>
    </div>
    <div class=" header__right">
        <form action="products.php" method="GET" class="header__search-form"
            style="display: flex; align-items: center; gap: 10px;">
            <div class="header__search">
                <input type="text" name="keyword" placeholder="Tìm kiếm..."
                    value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>" />
            </div>
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>

        <?php if ($isLoggedIn): ?>
        <span style="color: white; font-weight: bold; margin-right: 5px;">Xin chào,
            <?php echo htmlspecialchars($username); ?></span>

        <a href="profile.php" title="Thông tin cá nhân">
            <i class="fa-solid fa-user"></i> Hồ sơ
        </a>

        <?php if ($role !== 'admin'): ?>
        <a href="cart.php" title="Giỏ hàng">
            <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng
        </a>
        <?php endif; ?>

        <a href="logout.php" title="Đăng xuất" style="margin-left: 5px;">
            <i class="fa-solid fa-right-from-bracket"></i> Thoát
        </a>

        <?php else: ?>
        <a href="login.php">Đăng nhập</a>
        <a href="register.php">Đăng kí</a>
        <?php endif; ?>
    </div>
</header>