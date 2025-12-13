<?php
// Kiểm tra session đã được start chưa, nếu chưa thì start để lấy được thông tin user
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = false;
$username = '';
if (isset($_SESSION['user'])) {
    $isLoggedIn = true;
    $username = $_SESSION['user'];
}
?>
<header class="header container-full">
    <div class="header__left">
        <div class="header__logo">
            <img src="./assets/images/logo.png" alt="img_logo " />
        </div>
        <a href="index.php">Trang chủ</a>
        <a href="product.php">Sản phẩm</a>
    </div>
    <div class=" header__right">
        <form action="product.php" method="GET" class="header__search-form"
            style="display: flex; align-items: center; gap: 10px;">
            <div class="header__search">
                <input type="text" name="keyword" placeholder="Tìm kiếm..."
                    value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>" />
            </div>
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
        <?php if ($isLoggedIn): ?>
        <div class="header__user">
            <div class="header__user-name">
                <span>Xin chào, <?php echo htmlspecialchars($username); ?></span>
                <i class="fa-solid fa-caret-down"></i>
            </div>
            <div class="header__dropdown">
                <a href="profile.php"><i class="fa-solid fa-user"></i> Thông tin cá nhân</a>
                <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Giỏ hàng</a>
                <hr style="margin: 0; border: 0; border-top: 1px solid #eee" />
                <a href="logout.php" style="color: red"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
            </div>
        </div>
        <?php else: ?>
        <a href="login.php">Đăng nhập</a>
        <a href="register.php">Đăng kí</a>
        <?php endif; ?>
    </div>
</header>