<?php
session_start();
include('connect.php');

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// --- XỬ LÝ THÊM NGƯỜI DÙNG ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password']; 
    $fullname = trim($_POST['fullname']);
    $role = $_POST['role'];

    $checkSql = "SELECT * FROM Users WHERE username = '$username'";
    if (mysqli_num_rows(mysqli_query($conn, $checkSql)) > 0) {
        echo "<script>alert('Tên tài khoản này đã tồn tại!');</script>";
    } else {
        $sql = "INSERT INTO Users (username, password, fullname, role) VALUES ('$username', '$password', '$fullname', '$role')";
        mysqli_query($conn, $sql);
        echo "<script>alert('Thêm thành công!');</script>";
    }
}

// --- [MỚI] XỬ LÝ CẬP NHẬT (EDIT) NGƯỜI DÙNG ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $id = $_POST['edit_id'];
    $fullname = trim($_POST['edit_fullname']);
    $role = $_POST['edit_role'];
    $password = $_POST['edit_password'];

    // Nếu người dùng nhập mật khẩu mới thì cập nhật, không thì giữ nguyên
    if (!empty($password)) {
        $sql = "UPDATE Users SET fullname='$fullname', role='$role', password='$password' WHERE id=$id";
    } else {
        $sql = "UPDATE Users SET fullname='$fullname', role='$role' WHERE id=$id";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Cập nhật thông tin thành công!'); window.location.href='admin_users.php';</script>";
    } else {
        echo "<script>alert('Lỗi cập nhật: " . mysqli_error($conn) . "');</script>";
    }
}

// --- XỬ LÝ XÓA ---
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $currentUser = $_SESSION['user'];
    
    // Kiểm tra không cho xóa chính mình
    $checkSelf = "SELECT * FROM Users WHERE id = $id AND username = '$currentUser'";
    if(mysqli_num_rows(mysqli_query($conn, $checkSelf)) > 0) {
        echo "<script>alert('Không thể xóa tài khoản đang đăng nhập!'); window.location.href='admin_users.php';</script>";
    } else {
        mysqli_query($conn, "DELETE FROM Users WHERE id = $id");
        header("Location: admin_users.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
    /* --- CSS CHO POPUP (MODAL) --- */
    .modal {
        display: none;
        /* Ẩn mặc định */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* Màu nền tối mờ */
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: #fff;
        padding: 25px;
        border-radius: 8px;
        width: 500px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        position: relative;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .close-btn {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 24px;
        cursor: pointer;
        color: #aaa;
    }

    .close-btn:hover {
        color: #000;
    }

    /* Nút Sửa màu vàng cam */
    .btn-edit {
        background-color: #f39c12;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
        margin-right: 5px;
    }

    .btn-edit:hover {
        background-color: #e67e22;
    }
    </style>
</head>

<body>
    <?php include 'src/components/admin_sidebar.php'; ?>

    <div class="main-content">
        <h2 class="page-title">Quản lý người dùng</h2>

        <div class="form-add">
            <h3>Thêm tài khoản mới</h3>
            <form method="POST">
                <div class="form-group" style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <label>Tài khoản:</label>
                        <input type="text" name="username" required>
                    </div>
                    <div style="flex: 1;">
                        <label>Mật khẩu:</label>
                        <input type="text" name="password" required>
                    </div>
                </div>
                <div class="form-group" style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <label>Họ tên:</label>
                        <input type="text" name="fullname" required>
                    </div>
                    <div style="flex: 1;">
                        <label>Quyền:</label>
                        <select name="role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="add_user" class="btn-submit">Thêm mới</button>
            </form>
        </div>

        <div class="table-container">
            <h3>Danh sách người dùng</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tài khoản</th>
                        <th>Họ tên</th>
                        <th>Quyền</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM Users ORDER BY id DESC");
                    while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td>
                            <?php if($row['role'] == 'admin'): ?>
                            <span style="color:red; font-weight:bold">Admin</span>
                            <?php else: ?>
                            <span style="color:green; font-weight:bold">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn-edit" onclick="openEditModal(
                                '<?= $row['id'] ?>', 
                                '<?= htmlspecialchars($row['username']) ?>', 
                                '<?= htmlspecialchars($row['fullname']) ?>', 
                                '<?= $row['role'] ?>'
                            )">Sửa</button>

                            <?php if($row['role'] !== 'admin' || $row['username'] !== $_SESSION['user']): ?>
                            <a href="admin_users.php?delete_id=<?= $row['id'] ?>" class="btn-delete"
                                onclick="return confirm('Xóa người dùng này?')">Xóa</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <h3 style="margin-bottom: 20px; color: #2e7d32;">Chỉnh sửa thông tin</h3>

            <form method="POST">
                <input type="hidden" name="edit_id" id="edit_id">

                <div class="form-group">
                    <label>Tài khoản (Không thể đổi):</label>
                    <input type="text" id="edit_username" disabled style="background: #eee;">
                </div>

                <div class="form-group">
                    <label>Họ và tên:</label>
                    <input type="text" name="edit_fullname" id="edit_fullname" required>
                </div>

                <div class="form-group">
                    <label>Mật khẩu mới (Để trống nếu không đổi):</label>
                    <input type="text" name="edit_password" placeholder="Nhập mật khẩu mới...">
                </div>

                <div class="form-group">
                    <label>Quyền hạn:</label>
                    <select name="edit_role" id="edit_role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" onclick="closeEditModal()"
                        style="padding: 8px 15px; margin-right: 10px; cursor: pointer;">Hủy</button>
                    <button type="submit" name="edit_user" class="btn-submit">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Hàm mở popup và điền dữ liệu
    function openEditModal(id, username, fullname, role) {
        document.getElementById('editModal').style.display = 'flex';

        // Điền dữ liệu vào form
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_fullname').value = fullname;
        document.getElementById('edit_role').value = role;
    }

    // Hàm đóng popup
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Đóng khi click ra ngoài popup
    window.onclick = function(event) {
        var modal = document.getElementById('editModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>
</body>

</html>