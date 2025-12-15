<?php
session_start();
include('connect.php');

// Biến dùng để kích hoạt Popup
$popupType = ''; 
$popupMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu và làm sạch
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // --- CÁC TRƯỜNG HỢP KIỂM TRA ---
    
    // 1. Kiểm tra rỗng
    if (empty($fullname) || empty($username) || empty($password) || empty($confirm_password)) {
        $popupType = 'error';
        $popupMessage = "Vui lòng nhập đầy đủ tất cả thông tin!";
    } 
    // 2. Kiểm tra độ dài mật khẩu (Ví dụ: tối thiểu 6 ký tự)
    elseif (strlen($password) < 6) {
        $popupType = 'error';
        $popupMessage = "Mật khẩu phải có ít nhất 6 ký tự!";
    }
    // 3. Kiểm tra mật khẩu xác nhận
    elseif ($password !== $confirm_password) {
        $popupType = 'error';
        $popupMessage = "Mật khẩu xác nhận không khớp!";
    } 
    else {
        // 4. Kiểm tra tài khoản đã tồn tại chưa
        $usernameSafe = mysqli_real_escape_string($conn, $username);
        $sql_check = "SELECT * FROM Users WHERE username = '$usernameSafe'";
        $result_check = mysqli_query($conn, $sql_check);

        if (mysqli_num_rows($result_check) > 0) {
            $popupType = 'error';
            $popupMessage = "Tài khoản '$username' đã tồn tại. Vui lòng chọn tên khác!";
        } else {
            // 5. Thêm mới người dùng vào DB
            $passwordSafe = mysqli_real_escape_string($conn, $password);
            $fullnameSafe = mysqli_real_escape_string($conn, $fullname);
            
            // Mặc định avatar là user_default.png
            $sql = "INSERT INTO Users (username, password, fullname, role, avatar) 
                    VALUES ('$usernameSafe', '$passwordSafe', '$fullnameSafe', 'user', null)";
            
            if (mysqli_query($conn, $sql)) {
                $popupType = 'success';
                $popupMessage = "Đăng ký thành công! Bạn có thể đăng nhập ngay.";
            } else {
                $popupType = 'error';
                $popupMessage = "Lỗi hệ thống: " . mysqli_error($conn);
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
    <link rel="stylesheet" href="css/register.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <img src="./assets/images/logo.png" alt="Organic Mart" />
        </div>

        <h2>Đăng ký thành viên</h2>

        <form method="post" action="">
            <div class="form-group">
                <label for="fullname">Họ và tên</label>
                <input type="text" name="fullname" id="fullname" placeholder="Ví dụ: Nguyễn Văn A"
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

        <div class="register-link-box">
            <span>Bạn đã có tài khoản? </span>
            <a href="login.php" class="register-btn">Đăng nhập ngay</a>
        </div>
    </div>

    <div id="notificationModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>

            <div class="modal-icon" id="modalIcon">
            </div>

            <p id="modalMessage" class="modal-message"></p>

            <button class="btn-confirm" id="modalBtn" onclick="closeModal()">Đã hiểu</button>
        </div>
    </div>

    <script>
    // 1. Hàm ẩn hiện mật khẩu (Xử lý cho cả 2 ô pass)
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

    // 2. Hàm đóng Modal
    function closeModal() {
        document.getElementById("notificationModal").style.display = "none";
        // Nếu là success (đăng ký thành công) thì chuyển hướng về login khi đóng
        <?php if ($popupType == 'success') { ?>
        window.location.href = 'login.php';
        <?php } ?>
    }

    // 3. Đóng khi click ngoài vùng modal
    window.onclick = function(event) {
        var modal = document.getElementById('notificationModal');
        if (event.target == modal) {
            closeModal();
        }
    }

    // 4. LOGIC HIỂN THỊ POPUP TỪ PHP
    <?php if (!empty($popupType)) { ?>
    var modal = document.getElementById("notificationModal");
    var iconDiv = document.getElementById("modalIcon");
    var msgP = document.getElementById("modalMessage");
    var btn = document.getElementById("modalBtn");

    modal.style.display = "flex";
    msgP.innerText = "<?php echo $popupMessage; ?>";

    // Xử lý giao diện theo loại (Thành công / Lỗi)
    <?php if ($popupType == 'error') { ?>
    iconDiv.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i>';
    iconDiv.className = "modal-icon error";
    btn.innerText = "Thử lại";
    <?php } else { ?>
    iconDiv.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
    iconDiv.className = "modal-icon success";
    btn.innerText = "Đến trang đăng nhập";
    <?php } ?>

    <?php } ?>
    </script>
</body>

</html>