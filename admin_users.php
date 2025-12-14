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
    $address = trim($_POST['address']);
    $role = $_POST['role'];

    // Kiểm tra trùng username
    $checkSql = "SELECT * FROM Users WHERE username = '$username'";
    if (mysqli_num_rows(mysqli_query($conn, $checkSql)) > 0) {
        echo "<script>alert('Tên tài khoản này đã tồn tại!');</script>";
    } else {
        // Mặc định avatar là user_default.png
        $sql = "INSERT INTO Users (username, password, fullname, address, role, avatar) 
                VALUES ('$username', '$password', '$fullname', '$address', '$role', 'user_default.png')";
        
        if(mysqli_query($conn, $sql)){
            echo "<script>alert('Thêm thành công!'); window.location.href='admin_users.php';</script>";
        } else {
            echo "<script>alert('Lỗi: " . mysqli_error($conn) . "');</script>";
        }
    }
}

// --- XỬ LÝ CẬP NHẬT (EDIT) NGƯỜI DÙNG ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $id = $_POST['edit_id'];
    $fullname = trim($_POST['edit_fullname']);
    $address = trim($_POST['edit_address']);
    $role = $_POST['edit_role'];
    $password = $_POST['edit_password'];

    $sql = "UPDATE Users SET fullname='$fullname', address='$address', role='$role'";

    // Nếu có nhập pass mới thì cập nhật luôn
    if (!empty($password)) {
        $sql .= ", password='$password'";
    }
    
    $sql .= " WHERE id=$id";

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
    <link rel="stylesheet" href="css/admin_users.css">
</head>

<body>
    <?php include 'src/components/admin_sidebar.php'; ?>

    <div class="main-content">
        <h2 class="page-title">Quản lý người dùng</h2>

        <div class="form-add-container">
            <div class="form-add-header">
                <i class="fa-solid fa-user-plus"></i> Thêm tài khoản mới
            </div>

            <form method="POST">
                <div class="form-row">
                    <div class="form-col">
                        <label><i class="fa-solid fa-user"></i> Tài khoản</label>
                        <input type="text" name="username" placeholder="Nhập tên đăng nhập..." required>
                    </div>
                    <div class="form-col">
                        <label><i class="fa-solid fa-lock"></i> Mật khẩu</label>
                        <input type="text" name="password" placeholder="Nhập mật khẩu..." required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label><i class="fa-solid fa-signature"></i> Họ và tên</label>
                        <input type="text" name="fullname" placeholder="Nhập họ tên đầy đủ..." required>
                    </div>
                    <div class="form-col">
                        <label><i class="fa-solid fa-shield-halved"></i> Phân quyền</label>
                        <select name="role">
                            <option value="user">Người dùng (User)</option>
                            <option value="admin">Quản trị viên (Admin)</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label><i class="fa-solid fa-location-dot"></i> Địa chỉ</label>
                        <input type="text" name="address" placeholder="Nhập địa chỉ (Tùy chọn)...">
                    </div>
                </div>

                <div style="text-align: right;">
                    <button type="submit" name="add_user" class="btn-add-new">
                        <i class="fa-solid fa-plus"></i> Thêm người dùng
                    </button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h3>Danh sách người dùng</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Avatar</th>
                        <th>Tài khoản</th>
                        <th>Họ tên</th>
                        <th>Địa chỉ</th>
                        <th>Quyền</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM Users ORDER BY id DESC");
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Xử lý hiển thị Avatar
                        $avatarFile = !empty($row['avatar']) ? $row['avatar'] : 'user_default.png';
                        $avatarPath = "./assets/images/avatar/" . $avatarFile;
                        
                        // Nếu file không tồn tại thì dùng ảnh mặc định online
                        if (!file_exists($avatarPath)) {
                            $avatarPath = "https://cdn-icons-png.flaticon.com/512/149/149071.png";
                        }
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>
                            <img src="<?= $avatarPath ?>" alt="avt" class="user-avatar-thumb">
                        </td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['address'] ?? 'Chưa cập nhật') ?></td>
                        <td>
                            <?php if($row['role'] == 'admin'): ?>
                            <span
                                style="color:red; font-weight:bold; background: #ffebee; padding: 4px 8px; border-radius: 4px;">Admin</span>
                            <?php else: ?>
                            <span
                                style="color:green; font-weight:bold; background: #e8f5e9; padding: 4px 8px; border-radius: 4px;">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn-edit" onclick="openEditModal(
                                '<?= $row['id'] ?>', 
                                '<?= htmlspecialchars($row['username']) ?>', 
                                '<?= htmlspecialchars($row['fullname']) ?>', 
                                '<?= htmlspecialchars($row['address'] ?? '') ?>',
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
            <h3 style="margin-bottom: 20px; color: #2e7d32; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                Chỉnh sửa thông tin
            </h3>

            <form method="POST">
                <input type="hidden" name="edit_id" id="edit_id">

                <div class="form-group">
                    <label>Tài khoản (Không thể đổi):</label>
                    <input type="text" id="edit_username" disabled style="background: #eee; cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label>Họ và tên:</label>
                    <input type="text" name="edit_fullname" id="edit_fullname" required>
                </div>

                <div class="form-group">
                    <label>Địa chỉ:</label>
                    <input type="text" name="edit_address" id="edit_address">
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
                        style="padding: 8px 15px; margin-right: 10px; cursor: pointer; background: #ddd; border: none; border-radius: 4px;">Hủy</button>
                    <button type="submit" name="edit_user" class="btn-submit">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Hàm mở popup và điền dữ liệu
    function openEditModal(id, username, fullname, address, role) {
        document.getElementById('editModal').style.display = 'flex';

        // Điền dữ liệu vào form
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_fullname').value = fullname;
        document.getElementById('edit_address').value = address;
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