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

// --- XỬ LÝ: XÓA AVATAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_avatar'])) {
    $sql = "UPDATE Users SET avatar = 'user_default.png' WHERE username = '$username'";
    if (mysqli_query($conn, $sql)) {
        $message = "<p class='success'>Đã xóa ảnh đại diện, quay về mặc định!</p>";
    } else {
        $message = "<p class='error'>Lỗi: " . mysqli_error($conn) . "</p>";
    }
}

// --- XỬ LÝ: CẬP NHẬT THÔNG TIN (Gồm cả upload avatar) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_profile'])) {
    $fullname = trim($_POST['fullname']);
    $address = trim($_POST['address']);
    $new_password = $_POST['new_password'];
    
    // 1. Cập nhật thông tin cơ bản
    $sql = "UPDATE Users SET fullname = '$fullname', address = '$address'";

    // 2. Cập nhật mật khẩu nếu có nhập
    if (!empty($new_password)) {
        $sql .= ", password = '$new_password'";
    }

    // 3. Xử lý Upload Avatar
    if (isset($_FILES['avatar']) && $_FILES['avatar']['name'] != "") {
        $target_dir = "./assets/images/avatar/";
        
        // Tạo thư mục nếu chưa có
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Tạo tên file mới: time_tenfilegoc.jpg (để tránh trùng)
        $fileName = time() . "_" . basename($_FILES["avatar"]["name"]);
        $target_file = $target_dir . $fileName;
        $allowUpload = true;

        // Kiểm tra định dạng ảnh
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if(!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            $message = "<p class='error'>Chỉ chấp nhận file ảnh (JPG, JPEG, PNG, GIF)!</p>";
            $allowUpload = false;
        }

        if ($allowUpload) {
            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                // Thêm vào câu lệnh SQL
                $sql .= ", avatar = '$fileName'";
            } else {
                $message = "<p class='error'>Lỗi khi tải ảnh lên server.</p>";
            }
        }
    }

    $sql .= " WHERE username = '$username'";

    if (mysqli_query($conn, $sql)) {
        // Ghi đè thông báo thành công nếu chưa có lỗi upload
        if (strpos($message, 'error') === false) {
            $message = "<p class='success'>Cập nhật hồ sơ thành công!</p>";
        }
    } else {
        $message = "<p class='error'>Lỗi Database: " . mysqli_error($conn) . "</p>";
    }
}

// 4. LẤY THÔNG TIN NGƯỜI DÙNG HIỆN TẠI
$sql_info = "SELECT * FROM Users WHERE username = '$username'";
$result_info = mysqli_query($conn, $sql_info);
$user = mysqli_fetch_assoc($result_info);

// Xử lý hiển thị ảnh: Nếu db trống hoặc file không tồn tại -> dùng ảnh mặc định
$avatarName = !empty($user['avatar']) ? $user['avatar'] : 'user_default.png';
$avatarPath = "./assets/images/avatar/" . $avatarName;

// Nếu file không tồn tại thực tế (do xóa tay hoặc path sai), fallback về ảnh gốc hoặc icon online
if (!file_exists($avatarPath) && $avatarName != 'user_default.png') {
    $displayAvatar = "https://cdn-icons-png.flaticon.com/512/149/149071.png"; 
} elseif (file_exists($avatarPath)) {
    $displayAvatar = $avatarPath;
} else {
    // Trường hợp là user_default.png nhưng chưa có file đó trong folder avatar
    $displayAvatar = "https://cdn-icons-png.flaticon.com/512/149/149071.png";
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
    <style>
    /* CSS bổ sung cho phần avatar */
    .avatar-preview {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #2e7d32;
        margin-bottom: 15px;
    }

    .btn-delete-img {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #c62828;
        padding: 4px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        margin-top: 5px;
    }

    .btn-delete-img:hover {
        background: #ef9a9a;
    }
    </style>
</head>

<body>

    <?php include 'src/components/header.php'; ?>

    <div class="container profile-page">
        <div class="profile-sidebar">
            <div class="avatar-box">
                <img src="<?php echo $displayAvatar; ?>" alt="Avatar" class="avatar-preview">
                <h3><?php echo htmlspecialchars($user['fullname']); ?></h3>
                <p>Thành viên</p>
            </div>
            <div class="menu-list">
                <a href="profile.php" class="active"><i class="fa-solid fa-user"></i> Tài khoản của tôi</a>
            </div>
        </div>

        <div class="profile-content">
            <h2 class="section-title">Hồ sơ của tôi</h2>
            <p class="section-subtitle">Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
            <hr>

            <?php echo $message; ?>

            <form method="POST" class="profile-form" enctype="multipart/form-data">

                <div class="form-group">
                    <label>Ảnh đại diện:</label>
                    <div style="flex: 1;">
                        <input type="file" name="avatar" accept="image/*">

                        <?php if ($avatarName != 'user_default.png'): ?>
                        <div style="margin-top: 5px;">
                            <button type="submit" name="delete_avatar" class="btn-delete-img"
                                onclick="return confirm('Bạn chắc chắn muốn xóa ảnh này?')">
                                <i class="fa-solid fa-trash"></i> Xóa ảnh hiện tại
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

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
                    <label></label>
                    <button type="submit" name="save_profile" class="btn-save">Lưu thay đổi</button>
                </div>

            </form>
        </div>
    </div>

    <?php include 'src/components/footer.php'; ?>
</body>

</html>