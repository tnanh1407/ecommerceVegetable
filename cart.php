<?php
session_start();
include('connect.php');

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user'];
// Lấy ID người dùng từ username
$userQuery = mysqli_query($conn, "SELECT id FROM Users WHERE username = '$username'");
$userRow = mysqli_fetch_assoc($userQuery);
$userId = $userRow['id'];

// --- XỬ LÝ: THÊM VÀO GIỎ HÀNG (Từ trang chi tiết) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $productId = intval($_POST['id']);
    $quantity = intval($_POST['quantity']);

    // Lấy giá sản phẩm hiện tại
    $productQuery = mysqli_query($conn, "SELECT price FROM Products WHERE id = $productId");
    $productRow = mysqli_fetch_assoc($productQuery);
    $price = $productRow['price'];

    // Kiểm tra sản phẩm đã có trong giỏ chưa
    $checkCart = mysqli_query($conn, "SELECT * FROM Cart WHERE idUser = $userId AND idProduct = $productId");

    if (mysqli_num_rows($checkCart) > 0) {
        // Nếu có rồi -> Cộng dồn số lượng
        $cartItem = mysqli_fetch_assoc($checkCart);
        $newQuantity = $cartItem['quantity'] + $quantity;
        mysqli_query($conn, "UPDATE Cart SET quantity = $newQuantity WHERE idUser = $userId AND idProduct = $productId");
    } else {
        // Nếu chưa có -> Thêm mới
        mysqli_query($conn, "INSERT INTO Cart (idUser, idProduct, quantity, price) VALUES ($userId, $productId, $quantity, $price)");
    }
    
    // Refresh để tránh gửi lại form khi F5
    header("Location: cart.php");
    exit();
}

// --- XỬ LÝ: XÓA SẢN PHẨM ---
if (isset($_GET['delete_id'])) {
    $cartId = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM Cart WHERE id = $cartId");
    header("Location: cart.php");
    exit();
}

// --- XỬ LÝ: CẬP NHẬT SỐ LƯỢNG (Tăng/Giảm) ---
if (isset($_GET['update_id']) && isset($_GET['type'])) {
    $cartId = intval($_GET['update_id']);
    $type = $_GET['type'];
    
    // Lấy số lượng hiện tại
    $currQuery = mysqli_query($conn, "SELECT quantity FROM Cart WHERE id = $cartId");
    $currRow = mysqli_fetch_assoc($currQuery);
    $currentQty = $currRow['quantity'];

    if ($type == 'increase') {
        $newQty = $currentQty + 1;
        mysqli_query($conn, "UPDATE Cart SET quantity = $newQty WHERE id = $cartId");
    } elseif ($type == 'decrease') {
        if ($currentQty > 1) {
            $newQty = $currentQty - 1;
            mysqli_query($conn, "UPDATE Cart SET quantity = $newQty WHERE id = $cartId");
        } else {
            // Nếu giảm về 0 thì xóa luôn (hoặc giữ là 1 tùy bạn)
            // Ở đây mình giữ là 1
        }
    }
    header("Location: cart.php");
    exit();
}

// --- LẤY DANH SÁCH GIỎ HÀNG ---
// JOIN bảng Cart với Products để lấy tên và ảnh sản phẩm
$sqlCart = "SELECT Cart.id as cart_id, Cart.quantity, Products.name, Products.image, Products.price 
            FROM Cart 
            JOIN Products ON Cart.idProduct = Products.id 
            WHERE Cart.idUser = $userId";
$resultCart = mysqli_query($conn, $sqlCart);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng - Organic Mart</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/cart.css">
</head>

<body>

    <?php include 'src/components/header.php'; ?>

    <div class="container cart-page">
        <h2 class="page-title">Giỏ hàng của bạn</h2>

        <?php if (mysqli_num_rows($resultCart) > 0) { ?>
        <div class="cart-container">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $totalMoney = 0;
                        while ($row = mysqli_fetch_assoc($resultCart)) { 
                            $lineTotal = $row['price'] * $row['quantity'];
                            $totalMoney += $lineTotal;
                        ?>
                    <tr>
                        <td class="product-col">
                            <img src="./assets/images/<?php echo $row['image']; ?>" alt="img">
                            <span><?php echo $row['name']; ?></span>
                        </td>
                        <td><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</td>
                        <td>
                            <div class="quantity-box">
                                <a href="cart.php?update_id=<?php echo $row['cart_id']; ?>&type=decrease"
                                    class="qty-btn">-</a>
                                <input type="text" value="<?php echo $row['quantity']; ?>" readonly>
                                <a href="cart.php?update_id=<?php echo $row['cart_id']; ?>&type=increase"
                                    class="qty-btn">+</a>
                            </div>
                        </td>
                        <td class="price-col"><?php echo number_format($lineTotal, 0, ',', '.'); ?>đ</td>
                        <td>
                            <a href="cart.php?delete_id=<?php echo $row['cart_id']; ?>" class="delete-btn"
                                onclick="return confirm('Xóa sản phẩm này?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="summary-info">
                    <p>Tổng tiền:</p>
                    <span class="total-price"><?php echo number_format($totalMoney, 0, ',', '.'); ?>đ</span>
                </div>
                <div class="cart-actions">
                    <a href="product.php" class="btn-continue">Tiếp tục mua sắm</a>
                    <button class="btn-checkout">Thanh toán ngay</button>
                </div>
            </div>
        </div>
        <?php } else { ?>
        <div class="empty-cart">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-cart-2130356-1800917.png"
                alt="Empty Cart">
            <p>Giỏ hàng của bạn đang trống!</p>
            <a href="product.php" class="btn-shop-now">Mua sắm ngay</a>
        </div>
        <?php } ?>
    </div>

    <?php include 'src/components/footer.php'; ?>
</body>

</html>