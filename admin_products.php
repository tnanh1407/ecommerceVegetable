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
    $target = "./assets/images/product/" . basename($image); 
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $sql = "INSERT INTO Products (name, price, type, nation, status, productDesc, image) 
                VALUES ('$name', '$price', '$type', '$nation', '$status', '$desc', '$image')";
        if(mysqli_query($conn, $sql)){
            echo "<script>alert('Thêm sản phẩm thành công!'); window.location.href='admin_products.php';</script>";
        } else {
             echo "<script>alert('Lỗi Database: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Lỗi upload ảnh!');</script>";
    }
}

// --- 2. XỬ LÝ SỬA SẢN PHẨM ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $id = $_POST['edit_id'];
    $name = $_POST['edit_name'];
    $price = $_POST['edit_price'];
    $type = $_POST['edit_type'];
    $nation = $_POST['edit_nation'];
    $status = $_POST['edit_status'];
    $desc = $_POST['edit_desc'];
    $current_image = $_POST['current_image'];

    $image = $_FILES['edit_image']['name']; 
    
    if ($image != "") {
        $target = "./assets/images/product/" . basename($image);
        move_uploaded_file($_FILES['edit_image']['tmp_name'], $target);
        $sql_image = ", image='$image'";
    } else {
        $sql_image = ""; 
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
    <link rel="stylesheet" href="css/admin_products.css">
</head>

<body>
    <?php include 'src/components/admin_sidebar.php'; ?>

    <div class="main-content">
        <h2 class="page-title">Quản lý sản phẩm</h2>

        <div class="form-add-container">
            <div class="form-add-header">
                <i class="fa-solid fa-box-open"></i> Thêm sản phẩm mới
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-col" style="flex: 2;">
                        <label>Tên sản phẩm</label>
                        <input type="text" name="name" placeholder="Nhập tên sản phẩm..." required>
                    </div>
                    <div class="form-col">
                        <label>Giá (VNĐ)</label>
                        <input type="number" name="price" placeholder="Ví dụ: 50000" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label>Loại sản phẩm</label>
                        <select name="type">
                            <option value="rau_xanh">Rau xanh</option>
                            <option value="trai_cay">Trái cây</option>
                            <option value="rau_qua">Rau quả</option>
                        </select>
                    </div>
                    <div class="form-col">
                        <label>Xuất xứ</label>
                        <select name="nation">
                            <option value="VietNam">Việt Nam</option>
                            <option value="TrungQuoc">Trung Quốc</option>
                            <option value="ThaiLan">Thái Lan</option>
                            <option value="HanQuoc">Hàn Quốc</option>
                            <option value="My">Mỹ</option>
                            <option value="NhatBan">Nhật Bản</option>
                            <option value="NewZealand">New Zealand</option>
                        </select>
                    </div>
                    <div class="form-col">
                        <label>Trạng thái</label>
                        <select name="status">
                            <option value="con_hang">Còn hàng</option>
                            <option value="het_hang">Hết hàng</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label>Ảnh sản phẩm</label>
                        <input type="file" name="image" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label>Mô tả chi tiết</label>
                        <textarea name="desc" rows="3" placeholder="Nhập mô tả sản phẩm..."></textarea>
                    </div>
                </div>

                <div style="text-align: right;">
                    <button type="submit" name="add_product" class="btn-add-new">
                        <i class="fa-solid fa-plus"></i> Thêm sản phẩm
                    </button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h3>Danh sách sản phẩm hiện có</h3>
            <table>
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th width="80">Hình ảnh</th>
                        <th width="150">Tên sản phẩm</th>
                        <th>Giá</th>
                        <th>Xuất xứ</th>
                        <th>Loại</th>
                        <th>Trạng thái</th>
                        <th>Mô tả</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM Products ORDER BY id DESC");
                    while ($row = mysqli_fetch_assoc($result)) {
                        $imgUrl = "./assets/images/product/" . $row['image'];
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>
                            <img src="<?= $imgUrl ?>" alt="img" class="product-thumb">
                        </td>
                        <td>
                            <div style="font-weight: bold;"><?= htmlspecialchars($row['name']) ?></div>
                        </td>
                        <td style="color: #d32f2f; font-weight: bold;"><?= number_format($row['price']) ?>đ</td>

                        <td><?= $row['nation'] ?></td>

                        <td>
                            <?php 
                                if ($row['type'] == 'rau_xanh') echo 'Rau xanh';
                                elseif ($row['type'] == 'rau_qua') echo 'Rau quả';
                                else echo 'Trái cây';
                            ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'con_hang'): ?>
                            <span
                                style="color:green; font-weight:bold; background: #e8f5e9; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Còn
                                hàng</span>
                            <?php else: ?>
                            <span
                                style="color:red; font-weight:bold; background: #ffebee; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Hết
                                hàng</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="desc-cell" title="<?= htmlspecialchars($row['productDesc']) ?>">
                                <?= htmlspecialchars($row['productDesc']) ?>
                            </div>
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
            <h3 style="margin-bottom: 20px; color: #2e7d32; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                Chỉnh sửa sản phẩm
            </h3>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="edit_id">
                <input type="hidden" name="current_image" id="current_image">

                <div class="form-row">
                    <div class="form-col">
                        <label>Tên sản phẩm:</label>
                        <input type="text" name="edit_name" id="edit_name" required>
                    </div>
                    <div class="form-col">
                        <label>Giá (VNĐ):</label>
                        <input type="number" name="edit_price" id="edit_price" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label>Loại:</label>
                        <select name="edit_type" id="edit_type">
                            <option value="rau_xanh">Rau xanh</option>
                            <option value="trai_cay">Trái cây</option>
                            <option value="rau_qua">Rau quả</option>
                        </select>
                    </div>
                    <div class="form-col">
                        <label>Xuất xứ:</label>
                        <select name="edit_nation" id="edit_nation">
                            <option value="VietNam">Việt Nam</option>
                            <option value="TrungQuoc">Trung Quốc</option>
                            <option value="ThaiLan">Thái Lan</option>
                            <option value="HanQuoc">Hàn Quốc</option>
                            <option value="My">Mỹ</option>
                            <option value="NhatBan">Nhật Bản</option>
                            <option value="NewZealand">New Zealand</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label>Trạng thái:</label>
                        <select name="edit_status" id="edit_status">
                            <option value="con_hang">Còn hàng</option>
                            <option value="het_hang">Hết hàng</option>
                        </select>
                    </div>
                </div>

                <div class="form-col">
                    <label>Ảnh hiện tại:</label>
                    <div id="preview_img_container">
                        <img id="preview_img" src="" width="80">
                    </div>
                    <label style="margin-top: 10px;">Chọn ảnh mới (nếu muốn thay đổi):</label>
                    <input type="file" name="edit_image">
                </div>

                <div class="form-col" style="margin-top: 15px;">
                    <label>Mô tả:</label>
                    <textarea name="edit_desc" id="edit_desc" rows="4"></textarea>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" onclick="closeEditModal()"
                        style="padding: 8px 15px; margin-right: 10px; cursor: pointer; background: #ddd; border: none; border-radius: 4px;">Hủy</button>
                    <button type="submit" name="edit_product" class="btn-add-new">Lưu thay đổi</button>
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
        document.getElementById('preview_img').src = './assets/images/product/' + image;
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