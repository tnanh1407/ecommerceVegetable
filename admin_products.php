<?php
session_start();
include('connect.php');

// Check quyền admin
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// --- 1. XỬ LÝ THÊM SẢN PHẨM ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $type = $_POST['type'];
    $nation = $_POST['nation'];
    $status = $_POST['status'];
    $desc = $_POST['desc'];
    
    // Upload ảnh
    $image = $_FILES['image']['name'];
    $target = "./assets/images/" . basename($image);
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $sql = "INSERT INTO Products (name, price, type, nation, status, productDesc, image) 
                VALUES ('$name', '$price', '$type', '$nation', '$status', '$desc', '$image')";
        mysqli_query($conn, $sql);
        echo "<script>alert('Thêm sản phẩm thành công!');</script>";
    } else {
        echo "<script>alert('Lỗi upload ảnh!');</script>";
    }
}

// --- 2. [MỚI] XỬ LÝ SỬA SẢN PHẨM ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $id = $_POST['edit_id'];
    $name = $_POST['edit_name'];
    $price = $_POST['edit_price'];
    $type = $_POST['edit_type'];
    $nation = $_POST['edit_nation'];
    $status = $_POST['edit_status'];
    $desc = $_POST['edit_desc'];
    $current_image = $_POST['current_image']; // Tên ảnh cũ

    $image = $_FILES['edit_image']['name']; // Tên ảnh mới (nếu có)
    
    // Logic: Nếu có chọn ảnh mới -> Upload và cập nhật tên ảnh
    //        Nếu không chọn ảnh mới -> Giữ nguyên tên ảnh cũ
    if ($image != "") {
        $target = "./assets/images/" . basename($image);
        move_uploaded_file($_FILES['edit_image']['tmp_name'], $target);
        $sql_image = ", image='$image'";
    } else {
        $sql_image = ""; // Không thay đổi cột image
    }

    $sql = "UPDATE Products SET 
            name='$name', 
            price='$price', 
            type='$type', 
            nation='$nation', 
            status='$status', 
            productDesc='$desc' 
            $sql_image 
            WHERE id=$id";

    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Cập nhật sản phẩm thành công!'); window.location.href='admin_products.php';</script>";
    } else {
        echo "<script>alert('Lỗi: " . mysqli_error($conn) . "');</script>";
    }
}

// --- 3. XỬ LÝ XÓA SẢN PHẨM ---
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM Products WHERE id = $id");
    header("Location: admin_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý sản phẩm</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
    /* --- CSS CHO POPUP (MODAL) --- */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        overflow-y: auto;
        /* Cho phép cuộn nếu popup dài */
    }

    .modal-content {
        background-color: #fff;
        padding: 25px;
        border-radius: 8px;
        width: 600px;
        max-height: 90vh;
        /* Giới hạn chiều cao */
        overflow-y: auto;
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

    /* Button style */
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
        <h2 class="page-title">Quản lý sản phẩm</h2>

        <div class="form-add">
            <h3>Thêm sản phẩm mới</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Tên sản phẩm:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Giá (VNĐ):</label>
                    <input type="number" name="price" required>
                </div>
                <div class="form-group" style="display: flex; gap: 20px;">
                    <div style="flex:1">
                        <label>Loại:</label>
                        <select name="type">
                            <option value="rau_xanh">Rau xanh</option>
                            <option value="rau_cu">Rau củ</option>
                            <option value="trai_cay">Trái cây</option>
                        </select>
                    </div>
                    <div style="flex:1">
                        <label>Xuất xứ:</label>
                        <select name="nation">
                            <option value="VietNam">Việt Nam</option>
                            <option value="TrungQuoc">Trung Quốc</option>
                            <option value="ThaiLan">Thái Lan</option>
                        </select>
                    </div>
                    <div style="flex:1">
                        <label>Trạng thái:</label>
                        <select name="status">
                            <option value="con_hang">Còn hàng</option>
                            <option value="het_hang">Hết hàng</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Ảnh sản phẩm:</label>
                    <input type="file" name="image" required>
                </div>
                <div class="form-group">
                    <label>Mô tả:</label>
                    <textarea name="desc" rows="3"></textarea>
                </div>
                <button type="submit" name="add_product" class="btn-submit">Thêm sản phẩm</button>
            </form>
        </div>

        <div class="table-container">
            <h3>Danh sách sản phẩm hiện có</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá</th>
                        <th>Loại</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM Products ORDER BY id DESC");
                    while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><img src="./assets/images/<?= $row['image'] ?>" width="50"
                                style="object-fit: cover; height: 50px;"></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= number_format($row['price']) ?>đ</td>
                        <td>
                            <?php 
                                if ($row['type'] == 'rau_cu') echo 'Rau củ';
                                elseif ($row['type'] == 'rau_qua') echo 'Rau quả';
                                else echo 'Trái cây';
                            ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'con_hang'): ?>
                            <span style="color:green; font-weight:bold">Còn hàng</span>
                            <?php else: ?>
                            <span style="color:red; font-weight:bold">Hết hàng</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn-edit" onclick="openEditModal(
                                '<?= $row['id'] ?>',
                                '<?= htmlspecialchars($row['name']) ?>',
                                '<?= $row['price'] ?>',
                                '<?= $row['type'] ?>',
                                '<?= $row['nation'] ?>',
                                '<?= $row['status'] ?>',
                                '<?= $row['image'] ?>',
                                `<?= htmlspecialchars($row['productDesc']) ?>` 
                            )">Sửa</button>

                            <a href="admin_products.php?delete_id=<?= $row['id'] ?>" class="btn-delete"
                                onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">Xóa</a>
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
            <h3 style="margin-bottom: 20px; color: #2e7d32;">Chỉnh sửa sản phẩm</h3>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="edit_id">
                <input type="hidden" name="current_image" id="current_image">

                <div class="form-group">
                    <label>Tên sản phẩm:</label>
                    <input type="text" name="edit_name" id="edit_name" required>
                </div>

                <div class="form-group">
                    <label>Giá (VNĐ):</label>
                    <input type="number" name="edit_price" id="edit_price" required>
                </div>

                <div class="form-group" style="display: flex; gap: 20px;">
                    <div style="flex:1">
                        <label>Loại:</label>
                        <select name="edit_type" id="edit_type">
                            <option value="rau_xanh">Rau xanh</option>
                            <option value="rau_cu">Rau củ</option>
                            <option value="trai_cay">Trái cây</option>
                        </select>
                    </div>
                    <div style="flex:1">
                        <label>Xuất xứ:</label>
                        <select name="edit_nation" id="edit_nation">
                            <option value="VietNam">Việt Nam</option>
                            <option value="TrungQuoc">Trung Quốc</option>
                            <option value="ThaiLan">Thái Lan</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Trạng thái:</label>
                    <select name="edit_status" id="edit_status">
                        <option value="con_hang">Còn hàng</option>
                        <option value="het_hang">Hết hàng</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Ảnh hiện tại:</label>
                    <div id="preview_img_container">
                        <img id="preview_img" src="" width="80" style="border: 1px solid #ddd; padding: 2px;">
                    </div>
                    <label style="margin-top: 10px;">Chọn ảnh mới (nếu muốn thay đổi):</label>
                    <input type="file" name="edit_image">
                </div>

                <div class="form-group">
                    <label>Mô tả:</label>
                    <textarea name="edit_desc" id="edit_desc" rows="4"></textarea>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" onclick="closeEditModal()"
                        style="padding: 8px 15px; margin-right: 10px; cursor: pointer;">Hủy</button>
                    <button type="submit" name="edit_product" class="btn-submit">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openEditModal(id, name, price, type, nation, status, image, desc) {
        document.getElementById('editModal').style.display = 'flex';

        // Điền dữ liệu cũ vào form
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_type').value = type;
        document.getElementById('edit_nation').value = nation;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_desc').value = desc;

        // Xử lý ảnh
        document.getElementById('current_image').value = image;
        document.getElementById('preview_img').src = './assets/images/' + image;
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    window.onclick = function(event) {
        var modal = document.getElementById('editModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>
</body>

</html>