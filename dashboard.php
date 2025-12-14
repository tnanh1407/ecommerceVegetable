<?php
session_start();
include('connect.php');

// 1. KIỂM TRA QUYỀN ADMIN
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. THỐNG KÊ DỮ LIỆU CƠ BẢN
$countUser = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM Users"))['total'];
$countProduct = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM Products"))['total'];

// 3. LẤY DỮ LIỆU MỚI NHẤT
// Lấy 5 sản phẩm mới nhất
$productResult = mysqli_query($conn, "SELECT * FROM Products ORDER BY id DESC LIMIT 5");
// Lấy 5 người dùng mới nhất
$userResult = mysqli_query($conn, "SELECT * FROM Users ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
    /* CSS nội bộ bổ sung cho dashboard bố cục đẹp hơn */
    .dashboard-sections {
        display: flex;
        gap: 20px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .section-box {
        flex: 1;
        min-width: 300px;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .section-box h3 {
        margin-bottom: 15px;
        color: #2e7d32;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    /* Tinh chỉnh bảng nhỏ gọn hơn cho dashboard */
    .mini-table {
        width: 100%;
        font-size: 14px;
    }

    .mini-table th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: bold;
    }

    .mini-table td,
    .mini-table th {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .mini-table img {
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    </style>
</head>

<body>

    <?php include 'src/components/admin_sidebar.php'; ?>

    <div class="main-content">
        <h2 class="page-title">Tổng quan hệ thống</h2>

        <div class="dashboard-cards">
            <div class="card">
                <h3>Tổng sản phẩm</h3>
                <p><?php echo $countProduct; ?></p>
            </div>
            <div class="card">
                <h3>Tổng thành viên</h3>
                <p><?php echo $countUser; ?></p>
            </div>
            <div class="card">
                <h3>Đơn hàng mới</h3>
                <p>0</p>
            </div>
        </div>

        <div class="dashboard-sections">

            <div class="section-box">
                <h3><i class="fa-solid fa-carrot"></i> Sản phẩm vừa thêm</h3>
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($productResult) > 0) {
                            while ($row = mysqli_fetch_assoc($productResult)) { ?>
                        <tr>
                            <td>
                                <img src="./assets/images/product/<?php echo $row['image']; ?>" width="40" height="40"
                                    style="object-fit: cover;">
                            </td>
                            <td>
                                <?php echo $row['name']; ?>
                                <br>
                                <span style="font-size: 12px; color: #888;">
                                    <?php echo ($row['status'] == 'con_hang') ? '<span style="color:green">Còn hàng</span>' : '<span style="color:red">Hết hàng</span>'; ?>
                                </span>
                            </td>
                            <td style="color: #d32f2f; font-weight: bold;">
                                <?php echo number_format($row['price'], 0, ',', '.'); ?>đ
                            </td>
                        </tr>
                        <?php } 
                        } else { echo "<tr><td colspan='3'>Chưa có sản phẩm nào.</td></tr>"; } ?>
                    </tbody>
                </table>
                <div style="text-align: right; margin-top: 10px;">
                    <a href="admin_products.php" style="color: #2e7d32; text-decoration: none; font-size: 13px;">Xem tất
                        cả &rarr;</a>
                </div>
            </div>

            <div class="section-box">
                <h3><i class="fa-solid fa-users"></i> Thành viên mới</h3>
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Tài khoản</th>
                            <th>Họ tên</th>
                            <th>Quyền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($userResult) > 0) {
                            while ($row = mysqli_fetch_assoc($userResult)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                            <td>
                                <?php if($row['role'] == 'admin'): ?>
                                <span
                                    style="background: #ffebee; color: #c62828; padding: 2px 6px; border-radius: 4px; font-size: 12px;">Admin</span>
                                <?php else: ?>
                                <span
                                    style="background: #e8f5e9; color: #2e7d32; padding: 2px 6px; border-radius: 4px; font-size: 12px;">User</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } 
                        } else { echo "<tr><td colspan='3'>Chưa có thành viên nào.</td></tr>"; } ?>
                    </tbody>
                </table>
                <div style="text-align: right; margin-top: 10px;">
                    <a href="admin_users.php" style="color: #2e7d32; text-decoration: none; font-size: 13px;">Xem tất cả
                        &rarr;</a>
                </div>
            </div>

        </div>
    </div>

</body>

</html>