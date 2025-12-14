<?php
session_start();
include('connect.php');

// --- 1. HÀM TẠO URL ---
function createUrl($key, $value) {
    $params = $_GET; 
    if ($value === '') {
        unset($params[$key]); 
    } else {
        $params[$key] = $value; 
    }
    return '?' . http_build_query($params);
}

// --- 2. LẤY GIÁ TRỊ HIỆN TẠI ---
$currentType = isset($_GET['type']) ? $_GET['type'] : '';
$currentNation = isset($_GET['nation']) ? $_GET['nation'] : '';
$currentPriceRange = isset($_GET['price_range']) ? $_GET['price_range'] : '';
$sortOption = isset($_GET['sort']) ? $_GET['sort'] : '';
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : ''; // Lấy keyword

// --- 3. XỬ LÝ SQL ---
$whereClauses = [];

// [MỚI] Logic Tìm kiếm
if ($keyword) {
    $safeKeyword = mysqli_real_escape_string($conn, $keyword);
    $whereClauses[] = "name LIKE '%$safeKeyword%'";
}

// Lọc Type
if ($currentType) {
    $safeType = mysqli_real_escape_string($conn, $currentType);
    $whereClauses[] = "type = '$safeType'";
}
// Lọc Nation
if ($currentNation) {
    $safeNation = mysqli_real_escape_string($conn, $currentNation);
    $whereClauses[] = "nation = '$safeNation'";
}
// Lọc Price
if ($currentPriceRange) {
    switch ($currentPriceRange) {
        case 'under100': $whereClauses[] = "price < 100000"; break;
        case '100-200': $whereClauses[] = "price BETWEEN 100000 AND 200000"; break;
        case '200-300': $whereClauses[] = "price BETWEEN 200000 AND 300000"; break;
        case 'over300': $whereClauses[] = "price > 300000"; break;
    }
}

// Sắp xếp
$orderSql = "ORDER BY id DESC"; 
switch ($sortOption) {
    case 'price-asc': $orderSql = "ORDER BY price ASC"; break;
    case 'price-desc': $orderSql = "ORDER BY price DESC"; break;
    case 'name-asc': $orderSql = "ORDER BY name ASC"; break;
    case 'name-desc': $orderSql = "ORDER BY name DESC"; break;
}

// Ghép SQL
$whereSql = "";
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(' AND ', $whereClauses);
}
$sql = "SELECT * FROM Products $whereSql $orderSql";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sản phẩm - Organic Mart</title>
    <link rel="stylesheet" href="./css/product.css" />
    <style>
    .content__funtion {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 10px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }

    .side-bar__categorys li a {
        text-decoration: none;
        color: #333;
        transition: all 0.2s;
        display: block;
        padding: 5px 0;
        border-left: 3px solid transparent;
        padding-left: 5px;
    }

    .side-bar__categorys li a:hover {
        color: #43a047;
        padding-left: 10px;
    }

    .side-bar__categorys li a.active {
        color: #2e7d32;
        font-weight: bold;
        border-left: 3px solid #2e7d32;
        padding-left: 10px;
        background-color: #e8f5e9;
    }

    .btn-reset-filter {
        display: block;
        text-align: center;
        margin-top: 15px;
        padding: 8px;
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #c62828;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
    }

    .btn-reset-filter:hover {
        background-color: #c62828;
        color: white;
    }
    </style>
</head>

<body>
    <?php include 'src/components/header.php'; ?>

    <div>
        <div class="product-main container">
            <div class="side-bar">

                <div class="side-bar__components">
                    <h3>Danh mục</h3>
                    <ul class="side-bar__categorys">
                        <li><a href="<?php echo createUrl('type', ''); ?>"
                                class="<?= $currentType == '' ? 'active' : '' ?>">Tất cả</a></li>
                        <li><a href="<?php echo createUrl('type', 'rau_xanh'); ?>"
                                class="<?= $currentType == 'rau_xanh' ? 'active' : '' ?>">Rau xanh</a></li>
                        <li><a href="<?php echo createUrl('type', 'rau_cu'); ?>"
                                class="<?= $currentType == 'rau_cu' ? 'active' : '' ?>">Rau củ</a></li>
                        <li><a href="<?php echo createUrl('type', 'trai_cay'); ?>"
                                class="<?= $currentType == 'trai_cay' ? 'active' : '' ?>">Trái cây</a></li>
                    </ul>
                </div>

                <div class="side-bar__components">
                    <h3>Giá sản phẩm</h3>
                    <ul class="side-bar__categorys">
                        <li><a href="<?php echo createUrl('price_range', 'under100'); ?>"
                                class="<?= $currentPriceRange == 'under100' ? 'active' : '' ?>">Dưới 100.000đ</a></li>
                        <li><a href="<?php echo createUrl('price_range', '100-200'); ?>"
                                class="<?= $currentPriceRange == '100-200' ? 'active' : '' ?>">100.000đ - 200.000đ</a>
                        </li>
                        <li><a href="<?php echo createUrl('price_range', '200-300'); ?>"
                                class="<?= $currentPriceRange == '200-300' ? 'active' : '' ?>">200.000đ - 300.000đ</a>
                        </li>
                        <li><a href="<?php echo createUrl('price_range', 'over300'); ?>"
                                class="<?= $currentPriceRange == 'over300' ? 'active' : '' ?>">Trên 300.000đ</a></li>
                    </ul>
                </div>

                <div class="side-bar__components">
                    <h3>Nguồn gốc</h3>
                    <ul class="side-bar__categorys">
                        <li><a href="<?php echo createUrl('nation', ''); ?>"
                                class="<?= $currentNation == '' ? 'active' : '' ?>">Tất cả</a></li>
                        <li><a href="<?php echo createUrl('nation', 'VietNam'); ?>"
                                class="<?= $currentNation == 'VietNam' ? 'active' : '' ?>">Việt Nam</a></li>
                        <li><a href="<?php echo createUrl('nation', 'TrungQuoc'); ?>"
                                class="<?= $currentNation == 'TrungQuoc' ? 'active' : '' ?>">Trung Quốc</a></li>
                        <li><a href="<?php echo createUrl('nation', 'ThaiLan'); ?>"
                                class="<?= $currentNation == 'ThaiLan' ? 'active' : '' ?>">Thái Lan</a></li>
                    </ul>
                </div>

                <?php if($currentType || $currentNation || $currentPriceRange || $keyword): ?>
                <a href="products.php" class="btn-reset-filter">
                    <i class="fa-solid fa-filter-circle-xmark"></i> Xóa tất cả bộ lọc
                </a>
                <?php endif; ?>
            </div>

            <div class="content">
                <div class="content__funtion">
                    <h2 style="margin: 0; color: #2e7d32; font-size: 20px;">
                        <?php 
                            // [MỚI] Hiển thị tiêu đề khi tìm kiếm
                            if($keyword) {
                                echo "Tìm thấy: \"$keyword\"";
                            } elseif($currentType == 'rau_xanh') {
                                echo 'Rau Xanh';
                            } elseif($currentType == 'rau_cu') {
                                echo 'Rau Củ';
                            } elseif($currentType == 'trai_cay') {
                                echo 'Trái Cây';
                            } else {
                                echo 'Tất cả sản phẩm';
                            }

                            if($currentNation) echo " - $currentNation";
                        ?>
                    </h2>

                    <form method="GET" class="sort-box">
                        <?php 
                            foreach($_GET as $key => $value) {
                                if($key != 'sort') echo "<input type='hidden' name='$key' value='$value'>";
                            }
                        ?>
                        <label for="sort">Sắp xếp: </label>
                        <select name="sort" id="sort" onchange="this.form.submit()">
                            <option value="default" <?php if($sortOption == 'default') echo 'selected'; ?>>Mặc định
                            </option>
                            <option value="price-asc" <?php if($sortOption == 'price-asc') echo 'selected'; ?>>Giá tăng
                                dần</option>
                            <option value="price-desc" <?php if($sortOption == 'price-desc') echo 'selected'; ?>>Giá
                                giảm dần</option>
                            <option value="name-asc" <?php if($sortOption == 'name-asc') echo 'selected'; ?>>Tên A-Z
                            </option>
                            <option value="name-desc" <?php if($sortOption == 'name-desc') echo 'selected'; ?>>Tên Z-A
                            </option>
                        </select>
                    </form>
                </div>

                <div class="content__product">
                    <div class="content__product-list">
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $typeText = '';
                                if ($row['type'] == 'rau_cu') $typeText = 'Rau củ';
                                elseif ($row['type'] == 'rau_qua') $typeText = 'Rau quả';
                                elseif ($row['type'] == 'trai_cay') $typeText = 'Trái cây';
                                else $typeText = 'Khác';
                                $statusText = ($row['status'] == 'con_hang') ? 'Còn hàng' : 'Hết hàng';
                        ?>
                        <div class="content__product-item">
                            <img src="./assets/images/product/<?php echo $row['image']; ?>"
                                alt="<?php echo $row['name']; ?>" />
                            <h3 class="content__product-name"><?php echo $row['name']; ?></h3>
                            <div class="content__product-status-type">
                                <span class="content__product-type"><?php echo $typeText; ?></span>
                                <span class="content__product-status"><?php echo $statusText; ?></span>
                            </div>
                            <p class="content__product-price-unit">
                                <span
                                    class="content__product-price"><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</span>
                                <span>/</span>
                                <span class="content__product-unit">kg</span>
                            </p>
                            <a href="productDetail.php?id=<?php echo $row['id']; ?>">
                                <button class="content__product-detail">Xem chi tiết</button>
                            </a>
                        </div>
                        <?php
                            }
                        } else {
                            echo "<div style='width:100%; text-align:center; padding: 40px; color: gray;'>
                                    <i class='fa-solid fa-magnifying-glass' style='font-size: 40px; margin-bottom: 10px;'></i>
                                    <p>Không tìm thấy sản phẩm nào!</p>
                                    <a href='products.php' style='color:#2e7d32; font-weight:bold'>Xem tất cả sản phẩm</a>
                                  </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'src/components/footer.php'; ?>
</body>

</html>