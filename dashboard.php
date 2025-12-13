<?php
session_start();
include('connect.php');

// 1. KIỂM TRA QUYỀN ADMIN
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Nếu không phải admin, đá về trang đăng nhập
    exit();
}

// 2. THỐNG KÊ DỮ LIỆU
$countUser = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM Users"))['total'];
$countProduct = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM Products"))['total'];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
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
    </div>

</body>

</html>