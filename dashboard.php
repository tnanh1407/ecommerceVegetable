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
$productResult = mysqli_query($conn, "SELECT * FROM Products ORDER BY id DESC LIMIT 5");
$userResult = mysqli_query($conn, "SELECT * FROM Users ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/admin_dashboard.css">
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
                                <img src="./assets/images/product/<?php echo $row['image']; ?>" alt="img">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                            </td>
                            <td style="color: #d32f2f; font-weight: bold;">
                                <?php echo number_format($row['price'], 0, ',', '.'); ?>đ
                            </td>
                        </tr>
                        <?php } 
                        } else { echo "<tr><td colspan='3'>Chưa có sản phẩm nào.</td></tr>"; } ?>
                    </tbody>
                </table>
                <div class="view-all-link">
                    <a href="admin_products.php">Xem tất cả &rarr;</a>
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
                                <span class="status-badge danger">Admin</span>
                                <?php else: ?>
                                <span class="status-badge success">User</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } 
                        } else { echo "<tr><td colspan='3'>Chưa có thành viên nào.</td></tr>"; } ?>
                    </tbody>
                </table>
                <div class="view-all-link">
                    <a href="admin_users.php">Xem tất cả &rarr;</a>
                </div>
            </div>

        </div>
    </div>

</body>

</html>