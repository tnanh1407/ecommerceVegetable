<?php
session_start();
include('connect.php');

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user'];
$message = '';

// 2. XỬ LÝ CẬP NHẬT THÔNG TIN
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $address = trim($_POST['address']);
    $new_password = $_POST['new_password'];
    
    // SQL cơ bản cập nhật thông tin chung
    $sql = "UPDATE Users SET fullname = '$fullname', address = '$address'";

    // Nếu người dùng nhập mật khẩu mới thì cập nhật thêm mật khẩu
    if (!empty($new_password)) {
        $sql .= ", password = '$new_password'";
    }

    $sql .= " WHERE username = '$username'";

    if (mysqli_query($conn, $sql)) {
        $message = "<p class='success'>Cập nhật thông tin thành công!</p>";
    } else {
        $message = "<p class='error'>Lỗi: " . mysqli_error($conn) . "</p>";
    }
}

// 3. LẤY THÔNG TIN NGƯỜI DÙNG HIỆN TẠI ĐỂ HIỂN THỊ
$sql_info = "SELECT * FROM Users WHERE username = '$username'";
$result_info = mysqli_query($conn, $sql_info);
$user = mysqli_fetch_assoc($result_info);

// Nếu không tìm thấy user (trường hợp lỗi hy hữu)
if (!$user) {
    echo "Không tìm thấy thông tin người dùng.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - Organic Mart</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/profile.css">
</head>

<body>

    <?php include 'src/components/header.php'; ?>

    <div class="container profile-page">
        <div class="profile-sidebar">
            <div class="avatar-box">
                <img src="./assets/images/user_default.png" alt="Avatar"
                    onerror="this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png'">
                <h3><?php echo htmlspecialchars($user['fullname']); ?></h3>
                <p>Thành viên</p>
            </div>
            <div class="menu-list">
                <a href="profile.php" class="active"><i class="fa-solid fa-user"></i> Tài khoản của tôi</a>
                <a href="#"><i class="fa-solid fa-clipboard-list"></i> Đơn mua</a>
                <a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
            </div>
        </div>

        <div class="profile-content">
            <h2 class="section-title">Hồ sơ của tôi</h2>
            <p class="section-subtitle">Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
            <hr>

            <?php echo $message; ?>

            <form method="POST" class="profile-form">

                <div class="form-group">
                    <label>Tên đăng nhập:</label>
                    <input type="text" value="<?php echo $user['username']; ?>" disabled class="input-disabled">
                    <span class="note">(Không thể thay đổi)</span>
                </div>

                <div class="form-group">
                    <label>Họ và tên:</label>
                    <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Địa chỉ:</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>"
                        placeholder="Thêm địa chỉ giao hàng...">
                </div>

                <div class="form-group change-pass-group">
                    <label>Mật khẩu mới:</label>
                    <input type="password" name="new_password" placeholder="Để trống nếu không muốn đổi mật khẩu">
                </div>

                <div class="form-group">
                    <label></label> <button type="submit" class="btn-save">Lưu thay đổi</button>
                </div>

            </form>
        </div>
    </div>

    <?php include 'src/components/footer.php'; ?>
</body>

</html>