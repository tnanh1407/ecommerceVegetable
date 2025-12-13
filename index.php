<?php
  session_start();
  // include('connect.php');
  $isLoggedIn = false ;
  $username = '' ;
  if(isset($_SESSION['user'])){
    $isLoggedIn = true ;
    $username = $_SESSION['user'];
  }
    echo "isLoggedIn: " . ($isLoggedIn ? 'true' : 'false') . "<br>";
    echo "username: " . $username;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Trang chủ - Quản lí rau củ</title>
    <link rel="stylesheet" href="css/homePage.css" />
    <style>
      .header__user {
        position: relative;
        display: inline-block;
        cursor: pointer;
        margin-left: 15px;
        font-weight: bold;
        color: #2e7d32;
      }

      .header__user-name {
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .header__dropdown {
        display: none; /* Ẩn mặc định */
        position: absolute;
        right: 0;
        top: 100%;
        background-color: white;
        min-width: 200px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 100;
        border-radius: 4px;
        overflow: hidden;
      }

      /* Hiện dropdown khi hover vào header__user */
      .header__user:hover .header__dropdown {
        display: block;
      }

      .header__dropdown a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        font-size: 14px;
        font-weight: normal;
        text-align: left;
        transition: 0.2s;
      }

      .header__dropdown a:hover {
        background-color: #f1f1f1;
        color: #2e7d32;
      }

      /* Chỉnh lại header right một chút để căn giữa */
      .header__right {
        display: flex;
        align-items: center;
        gap: 15px;
      }
    </style>
  </head>
  <body>
    <header class="header container-full">
      <div class="header__left">
        <div class="header__logo">
          <img src="./assets/images/logo.png" alt="img_logo " />
        </div>
        <a href="#">Trang chủ</a>
        <a href="#">Sản phẩm</a>
      </div>
      <div class="header__right">
        <div class="header__search">
          <input type="text" placeholder="Tìm kiếm..." />
        </div>
        <button><i class="fa-solid fa-magnifying-glass"></i></button>
        <?php if ($isLoggedIn): ?>
        <div class="header__user">
          <div class="header__user-name">
            <span
              >Xin chào,
              <?php echo htmlspecialchars($username); ?></span
            >
            <i class="fa-solid fa-caret-down"></i>
          </div>
          <div class="header__dropdown">
            <a href="#"><i class="fa-solid fa-user"></i> Thông tin cá nhân</a>
            <a href="#"><i class="fa-solid fa-cart-shopping"></i> Giỏ hàng</a>
            <a href="#"><i class="fa-solid fa-heart"></i> Sản phẩm yêu thích</a>
            <hr style="margin: 0; border: 0; border-top: 1px solid #eee" />
            <a href="logout.php" style="color: red"
              ><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a
            >
          </div>
        </div>
        <?php else: ?>
        <a href="login.php">Đăng nhập</a>
        <a href="register.php">Đăng kí</a>
        <?php endif; ?>
      </div>
    </header>

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
        bản thân Organic Mart đối với tổ ấm của mình. Chính vì vậy, Organic Mart
        – chuỗi cửa hàng bán lẻ thực phẩm hữu cơ ra đời, để cùng đồng hành, chia
        sẻ với các chị em nội trợ trong việc lựa chọn thực phẩm sạch, nguồn gốc
        rõ ràng để bảo vệ sức khỏe, làm nền tảng cho sự phát triển khoẻ mạnh của
        bạn và gia đình ở thời điểm hiện tại và cả tương lai sau này. Bên cạnh
        việc đồng hành cùng bạn trong bữa cơm gia đình hằng ngày, chúng tôi còn
        mong muốn làm cầu nối đưa các loại rau củ quả từ các vùng miền được các
        cô chú, các bạn trẻ yêu nông nghiệp canh tác tự nhiên đến gần hơn với
        người tiêu dùng, gắn kết những người nông dân cùng chung chí hướng để
        nông nghiệp hữu cơ ngày càng được mở rộng. "From Farm to Table" là kim
        chỉ nam trong suốt quá trình kinh doanh của chúng tôi. Hãy để Organic
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
          <img src="./assets/images/nam_dui_ga.jpg" alt="img_rauXanh" />
          <p>Rau xanh</p>
        </div>
        <div class="category__item">
          <img src="./assets/images/nam_dui_ga.jpg" alt="img_rauXanh" />
          <p>Rau Củ</p>
        </div>
        <div class="category__item">
          <img src="./assets/images/nam_dui_ga.jpg" alt="img_rauXanh" />
          <p>Trái Cây</p>
        </div>
      </div>
    </section>

    <section class="bestSaller container">
      <div class="bestSaller__header">
        <i class="fa-solid fa-fire"></i>
        <p>Sản phẩm bán bán chạy</p>
      </div>
      <div class="bestSaller__list">
        <div class="bestSaller__item">
          <img src="./assets/images/nam_dui_ga.jpg" alt="img_rauXanh" />
          <h3 class="bestSaller__name">Rau muống</h3>
          <div class="bestSaller__statusAndType">
            <span class="bestSaller__type">Rau Xanh</span>

            <span class="bestSaller__status">Còn hàng</span>
          </div>
          <p class="bestSaller__priceAndUnit">
            <span class="bestSaller__price"> 15.000đ</span>
            <span>/</span>
            <span class="bestSaller__unit">Bó</span>
          </p>
          <button class="bestSaller__detail">Xem chi tiết</button>
        </div>
        <div class="bestSaller__item">
          <img src="./assets/images/nam_dui_ga.jpg" alt="img_rauXanh" />
          <h3 class="bestSaller__name">Rau muống</h3>
          <div class="bestSaller__statusAndType">
            <span class="bestSaller__type">Rau Xanh</span>

            <span class="bestSaller__status">Còn hàng</span>
          </div>
          <p class="bestSaller__priceAndUnit">
            <span class="bestSaller__price"> 15.000đ</span>
            <span>/</span>
            <span class="bestSaller__unit">Bó</span>
          </p>
          <button class="bestSaller__detail">Xem chi tiết</button>
        </div>

        <div class="bestSaller__item">
          <img src="./assets/images/nam_dui_ga.jpg" alt="img_rauXanh" />
          <h3 class="bestSaller__name">Rau muống</h3>
          <div class="bestSaller__statusAndType">
            <span class="bestSaller__type">Rau Xanh</span>

            <span class="bestSaller__status">Còn hàng</span>
          </div>
          <p class="bestSaller__priceAndUnit">
            <span class="bestSaller__price"> 15.000đ</span>
            <span>/</span>
            <span class="bestSaller__unit">Bó</span>
          </p>
          <button class="bestSaller__detail">Xem chi tiết</button>
        </div>

        <div class="bestSaller__item">
          <img src="./assets/images/nam_dui_ga.jpg" alt="img_rauXanh" />
          <h3 class="bestSaller__name">Rau muống</h3>
          <div class="bestSaller__statusAndType">
            <span class="bestSaller__type">Rau Xanh</span>

            <span class="bestSaller__status">Còn hàng</span>
          </div>
          <p class="bestSaller__priceAndUnit">
            <span class="bestSaller__price"> 15.000đ</span>
            <span>/</span>
            <span class="bestSaller__unit">Bó</span>
          </p>
          <button class="bestSaller__detail">Xem chi tiết</button>
        </div>

        <div class="bestSaller__item">
          <img src="./assets/images/nam_dui_ga.jpg" alt="img_rauXanh" />
          <h3 class="bestSaller__name">Rau muống</h3>
          <div class="bestSaller__statusAndType">
            <span class="bestSaller__type">Rau Xanh</span>

            <span class="bestSaller__status">Còn hàng</span>
          </div>
          <p class="bestSaller__priceAndUnit">
            <span class="bestSaller__price"> 15.000đ</span>
            <span>/</span>
            <span class="bestSaller__unit">Bó</span>
          </p>
          <button class="bestSaller__detail">Xem chi tiết</button>
        </div>
      </div>
    </section>
  </body>
</html>
