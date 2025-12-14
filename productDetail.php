<?php
session_start();
include('connect.php');

// 1. Kiểm tra xem có ID sản phẩm trên URL không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = intval($_GET['id']); // Bảo mật: Ép kiểu số

// 2. Truy vấn thông tin sản phẩm
$sql = "SELECT * FROM Products WHERE id = $id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

// Nếu không tìm thấy sản phẩm
if (!$product) {
    echo "<h1>Sản phẩm không tồn tại!</h1>";
    exit();
}

// 3. Truy vấn sản phẩm liên quan (Cùng loại, trừ sản phẩm đang xem)
$type = $product['type'];
// Lấy 4 sản phẩm cùng loại khác
$sqlRelated = "SELECT * FROM Products WHERE type = '$type' AND id != $id LIMIT 4";
$resultRelated = mysqli_query($conn, $sqlRelated);

$typeText = '';
if ($product['type'] == 'rau_cu') $typeText = 'Rau củ';
elseif ($product['type'] == 'rau_qua') $typeText = 'Rau quả';
elseif ($product['type'] == 'trai_cay') $typeText = 'Trái cây';
else $typeText = 'Khác';

$statusText = ($product['status'] == 'con_hang') ? 'Còn hàng' : 'Hết hàng';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - Organic Mart</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/productDetail.css">
</head>

<body>

    <?php include 'src/components/header.php'; ?>

    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">Trang chủ</a> /
            <a href="products.php">Sản phẩm</a> /
            <span><?php echo $product['name']; ?></span>
        </div>

        <div class="product-detail-wrapper">
            <div class="detail-left">
                <div class="main-image">
                    <img src="./assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                </div>
            </div>

            <div class="detail-right">
                <h1 class="product-title"><?php echo $product['name']; ?></h1>

                <div class="product-meta">
                    <span class="meta-item">Mã SP: <strong><?php echo $product['id']; ?></strong></span>
                    <span class="meta-item">Tình trạng:
                        <span class="status-tag <?php echo $product['status']; ?>">
                            <?php echo $statusText; ?>
                        </span>
                    </span>
                </div>

                <div class="product-price">
                    <?php echo number_format($product['price'], 0, ',', '.'); ?>₫
                    <span class="unit">/ kg</span>
                </div>

                <div class="product-desc">
                    <p><strong>Xuất xứ:</strong> <?php echo $product['nation']; ?></p>
                    <p><strong>Loại:</strong> <?php echo $typeText; ?></p>
                    <p class="desc-content">
                        <?php echo $product['productDesc'] ? $product['productDesc'] : 'Đang cập nhật mô tả...'; ?>
                    </p>
                </div>

                <form action="cart.php" method="POST" class="add-cart-form">
                    <div class="quantity-control">
                        <label>Số lượng:</label>
                        <input type="number" name="quantity" value="1" min="1" max="100">
                    </div>
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                    <div class="action-buttons">
                        <button type="submit" name="add_to_cart" class="btn-add-cart">
                            <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ
                        </button>

                        <button type="submit" name="add_to_cart" class="btn-buy-now"
                            onclick="this.form.action='cart.php?redirect=checkout'">
                            Mua ngay
                        </button>
                    </div>
                </form>

                <div class="policy-box">
                    <div class="policy-item">
                        <i class="fa-solid fa-truck-fast"></i>
                        <span>Giao hàng nhanh 2h</span>
                    </div>
                    <div class="policy-item">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Đảm bảo tươi ngon</span>
                    </div>
                    <div class="policy-item">
                        <i class="fa-solid fa-rotate"></i>
                        <span>Đổi trả trong 24h</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="related-products">
            <h2 class="section-title">Sản phẩm liên quan</h2>
            <div class="related-list">
                <?php while($row = mysqli_fetch_assoc($resultRelated)) { ?>
                <div class="related-item">
                    <a href="productDetail.php?id=<?php echo $row['id']; ?>" class="related-link">
                        <img src="./assets/images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                        <h3><?php echo $row['name']; ?></h3>
                        <p class="price"><?php echo number_format($row['price'], 0, ',', '.'); ?>₫</p>
                    </a>

                    <form action="cart.php" method="POST" class="quick-add-form">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" name="add_to_cart" class="btn-quick-add">
                            <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </form>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php include 'src/components/footer.php'; ?>
</body>

</html>