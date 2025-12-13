<?php
session_start();
include('connect.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // 1. Kiểm tra rỗng
    if (empty($fullname) || empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } 
    // 2. Kiểm tra mật khẩu nhập lại
    elseif ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp!";
    } 
    else {
        // 3. Kiểm tra tài khoản đã tồn tại chưa
        // Lưu ý: Tên bảng là 'Users', cột là 'username' (theo database.sql)
        $sql_check = "SELECT * FROM Users WHERE username = '$username'";
        $result_check = mysqli_query($conn, $sql_check);

        if (mysqli_num_rows($result_check) > 0) {
            $error = "Tài khoản này đã tồn tại, vui lòng chọn tên khác!";
        } else {
            // 4. Thêm mới người dùng vào DB
            // Cột: username, password, fullname, role
            $sql = "INSERT INTO Users (username, password, fullname, role) 
                    VALUES ('$username', '$password', '$fullname', 'user')";
            
            if (mysqli_query($conn, $sql)) {
                $success = "Đăng ký thành công! <a href='login.php' style='color: #2e7d32; text-decoration: underline'>Đăng nhập ngay</a>";
            } else {
                $error = "Có lỗi xảy ra: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng ký - Organic Mart</title>
    <link rel="stylesheet" href="css/login.css" />
    <style>
    .login-container {
        margin-top: 20px;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <img src="./assets/images/logo.png" alt="Organic Mart" />
        </div>

        <h2>Đăng ký thành viên</h2>

        <?php if (!empty($error)) { ?>
        <p class="error" style="color:red; margin-bottom: 10px;"><?= $error ?></p>
        <?php } ?>

        <?php if (!empty($success)) { ?>
        <p class="success" style="color:green; font-weight:bold; margin-bottom: 10px;"><?= $success ?></p>
        <?php } ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="fullname">Họ và tên</label>
                <input type="text" name="fullname" id="fullname" placeholder="Nhập họ và tên..."
                    value="<?php echo isset($fullname) ? htmlspecialchars($fullname) : '' ?>" />
            </div>

            <div class="form-group">
                <label for="username">Tài khoản</label>
                <input type="text" name="username" id="username" placeholder="Nhập tài khoản..."
                    value="<?php echo isset($username) ? htmlspecialchars($username) : '' ?>" />
            </div>

            <div class="form-group password-box">
                <label for="password">Mật khẩu</label>
                <input type="password" name="password" id="password" placeholder="Nhập mật khẩu..." />
                <span class="toggle-eye" onclick="togglePassword('password', this)">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>

            <div class="form-group password-box">
                <label for="confirm_password">Nhập lại mật khẩu</label>
                <input type="password" name="confirm_password" id="confirm_password"
                    placeholder="Xác nhận mật khẩu..." />
                <span class="toggle-eye" onclick="togglePassword('confirm_password', this)">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>

            <button type="submit" class="btn-login">Đăng ký</button>
        </form>

        <div style="margin-top: 15px">
            <span>Bạn đã có tài khoản? </span>
            <a href="login.php" class="register-btn">Đăng nhập ngay</a>
        </div>
    </div>

    <script>
    function togglePassword(inputId, iconSpan) {
        let input = document.getElementById(inputId);
        let icon = iconSpan.querySelector("i");

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
    </script>
</body>

</html>