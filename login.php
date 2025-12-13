<?php
// Bắt đầu session ngay đầu file
session_start();
include('connect.php');

$error = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            $_SESSION['user'] = $username;
            $_SESSION['role'] = $row['role']; // Lưu quyền (admin/user)

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
    <title>Đăng nhập - FreshFarm</title>
    <link rel="stylesheet" href="css/login.css" />

    <style></style>
  </head>

  <body>
    <div class="login-container">
      <div class="logo">
        <img src="./assets/images/logo.png" alt="FreshFarm" />
      </div>

      <h2>Đăng nhập Organic Mart</h2>

      <?php if (!empty($error)) { ?>
      <p class="error" style="color: red; margin-bottom: 10px;"><?= $error ?></p>
      <?php } ?>

      <form method="post">
        <div class="form-group">
          <label for="username">Tài khoản</label>
          <input
            type="text"
            name="username"
            id="username"
            placeholder="Nhập tài khoản..."
          />
        </div>

        <div class="form-group password-box">
          <label for="password">Mật khẩu</label>
          <input
            type="password"
            name="password"
            id="password"
            placeholder="Nhập mật khẩu..."
          />
          <span class="toggle-eye" onclick="togglePassword()"
            ><i class="fa-solid fa-eye"></i
          ></span>
        </div>

        <button type="submit" class="btn-login">Đăng nhập</button>
      </form>

      <a href="register.php" class="register-btn">Tạo tài khoản mới</a>
    </div>

    <script>
      function togglePassword() {
        let input = document.getElementById("password");
        input.type = input.type === "password" ? "text" : "password";
      }
    </script>
  </body>
</html>
