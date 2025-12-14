<?php
  session_start();
  include('connect.php'); 

  $isLoggedIn = false ;
  $username = '' ;
  if(isset($_SESSION['user'])){
    $isLoggedIn = true ;
    $username = $_SESSION['user'];
  }
  
  // 2. TRUY VẤN SẢN PHẨM BÁN CHẠY (Lấy ví dụ 8 sản phẩm mới nhất)
  $sql = "SELECT * FROM Products LIMIT 8";
  $result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Trang chủ - Quản lí rau củ</title>
    <link rel="stylesheet" href="css/homePage.css" />
    <link rel="stylesheet" href="css/main.css" />
    <style>
    /* Thêm một chút CSS để thẻ a trong danh mục hiển thị đẹp */
    .category__item a {
        text-decoration: none;
        color: inherit;
        display: block;
        width: 100%;
        height: 100%;
    }
    </style>
</head>

<body>
    <?php include './src/components/header.php' ?>

    <section class="banner container-full">
        <img src="./assets/images/banner.webp" alt="banner" />
    </section>

    <section class="intro container">
        <h3 class="intro__title">Giới thiệu</h3>
        <p class="intro__main">
            Thực phẩm là nguồn cung cấp chất dinh dưỡng cho sự phát triển của cơ
            thể, tác động trực tiếp và gây ảnh hưởng lâu dài đến sức khỏe. Làm sao
            để lựa chọn được thực phẩm sạch, vừa an toàn về chất lượng, vừa tiết
            kiệm thời gian để đảm bảo tốt cho sức khỏe của gia đình mình? Hiểu được
            sự trăn trở chung của các chị em nội trợ và cũng chính là sự lo lắng của
            bản thân Organic Mart đối với tổ ấm của mình...
        </p>
        <p class="intro__note">
            Mart đồng hành cùng bạn trong việc lựa chọn bữa ăn hàng ngày cho gia
            đình bạn nhé! Sống vui. Sống khoẻ. Nhẹ gánh âu lo!
        </p>
    </section>

    <section class="category container">
        <div class="category__header">
            <i class="fa-solid fa-star"></i>
            <p>Danh mục sản phẩm nổi bật</p>
        </div>
        <div class="category__list">
            <div class="category__item">
                <a href="product.php?type=rau_xanh">
                    <img src="./assets/images/rau_cu.jpg" alt="img_rauXanh" />
                    <p>Rau xanh</p>
                </a>
            </div>
            <div class="category__item">
                <a href="product.php?type=rau_cu">
                    <img src="./assets/images/nam_dui_ga.jpg" alt="img_rauCu" />
                    <p>Rau Củ</p>
                </a>
            </div>
            <div class="category__item">
                <a href="product.php?type=trai_cay">
                    <img src="./assets/images/nam_dui_ga.jpg" alt="img_traiCay" />
                    <p>Trái Cây</p>
                </a>
            </div>
        </div>
    </section>

    <section class="bestSaller container">
        <div class="bestSaller__header">
            <i class="fa-solid fa-fire"></i>
            <p>Sản phẩm bán chạy</p>
        </div>
        <div class="bestSaller__list">

            <?php
        // Kiểm tra và lặp qua dữ liệu lấy từ DB
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $typeText = '';
                if ($row['type'] == 'rau_cu') $typeText = 'Rau củ';
                elseif ($row['type'] == 'rau_qua') $typeText = 'Rau quả';
                elseif ($row['type'] == 'trai_cay') $typeText = 'Trái cây';
                else $typeText = 'Khác';

                $statusText = ($row['status'] == 'con_hang') ? 'Còn hàng' : 'Hết hàng';
        ?>
            <div class="bestSaller__item">
                <a href="productDetail.php?id=<?php echo $row['id']; ?>"
                    style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center;">

                    <img src="./assets/images/product/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" />

                    <h3 class="bestSaller__name"><?php echo $row['name']; ?></h3>

                    <div class="bestSaller__statusAndType">
                        <span class="bestSaller__type"><?php echo $typeText; ?></span>
                        <span class="bestSaller__status"><?php echo $statusText; ?></span>
                    </div>

                    <p class="bestSaller__priceAndUnit">
                        <span class="bestSaller__price"><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</span>
                        <span>/</span>
                        <span class="bestSaller__unit">kg</span>
                    </p>

                    <button class="bestSaller__detail"
                        onclick="location.href='productDetail.php?id=<?php echo $row['id']; ?>'">
                        Xem chi tiết
                    </button>
                </a>
            </div>
            <?php 
            } 
        } else {
            echo "<p>Chưa có sản phẩm nào.</p>";
        }
        ?>

        </div>
    </section>

    <?php include "./src/components/footer.php" ?>
</body>

</html>