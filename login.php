<?php
// Bắt đầu session ngay đầu file
session_start();
include('connect.php');

$error = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);

        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            $_SESSION['user'] = $username;
            $_SESSION['role'] = $row['role']; 

            if ($_SESSION['role'] == 'admin') {
                header('Location: dashboard.php');
            } else {
                header('Location: index.php');;
            }
            exit();
        } else {
            $error = "Sai tài khoản hoặc mật khẩu!";
        }
    } else {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng nhập - Organic Mart</title>
    <link rel="stylesheet" href="css/login.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
    /* CSS CHO POPUP MODAL */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* Nền đen mờ */
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .modal-content {
        background-color: #fff;
        padding: 30px 20px;
        border-radius: 12px;
        width: 350px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        position: relative;
        animation: slideDown 0.3s ease-in-out;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-icon {
        font-size: 50px;
        color: #d32f2f;
        /* Màu đỏ cảnh báo */
        margin-bottom: 15px;
    }

    .modal-message {
        font-size: 18px;
        color: #333;
        margin-bottom: 25px;
        font-weight: 500;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
        color: #aaa;
        transition: 0.2s;
    }

    .close-btn:hover {
        color: #333;
    }

    .btn-confirm {
        background-color: #2e7d32;
        color: white;
        padding: 10px 30px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: background 0.3s;
    }

    .btn-confirm:hover {
        background-color: #1b5e20;
    }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <img src="./assets/images/logo.png" alt="FreshFarm" />
        </div>

        <h2>Đăng nhập Organic Mart</h2>

        <form method="post">
            <div class="form-group">
                <label for="username">Tài khoản</label>
                <input type="text" name="username" id="username" placeholder="Nhập tài khoản..."
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" />
            </div>

            <div class="form-group password-box">
                <label for="password">Mật khẩu</label>
                <input type="password" name="password" id="password" placeholder="Nhập mật khẩu..." />
                <span class="toggle-eye" onclick="togglePassword()"><i class="fa-solid fa-eye"></i></span>
            </div>

            <button type="submit" class="btn-login">Đăng nhập</button>
        </form>

        <a href="register.php" class="register-btn">Tạo tài khoản mới</a>
    </div>

    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <div class="modal-icon">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <p id="modalErrorMessage" class="modal-message"></p>
            <button class="btn-confirm" onclick="closeModal()">Đã hiểu</button>
        </div>
    </div>

    <script>
    // Hàm ẩn/hiện mật khẩu
    function togglePassword() {
        let input = document.getElementById("password");
        let icon = document.querySelector(".toggle-eye i");

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

    // Hàm đóng Modal
    function closeModal() {
        document.getElementById("errorModal").style.display = "none";
    }

    // Đóng modal khi click ra vùng tối bên ngoài
    window.onclick = function(event) {
        var modal = document.getElementById('errorModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // LOGIC KIỂM TRA LỖI TỪ PHP ĐỂ HIỆN MODAL
    <?php if (!empty($error)) { ?>
    document.getElementById("modalErrorMessage").innerText = "<?php echo $error; ?>";
    document.getElementById("errorModal").style.display = "flex";
    <?php } ?>
    </script>
</body>

</html>