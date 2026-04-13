<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thanh toán | YumYum Corners</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="/logo.jpg" />
  <link rel="stylesheet" href="../../css/home.css">
  <link rel="stylesheet" href="../../css/checkout.css">
</head>

<body class="checkout-body">
  <header class="header">
    <div class="logo" style="display: flex; align-items: center; gap: 8px; width: 185px;">
      <img title="logo" src="../../../public/logo.jpg" style="width: 40px; height: 40px; object-fit: cover;">
      <p style="color: #f97316;"><strong>YumYum</strong><span class="brand-sub"> Corners</span></p>
    </div>
    <div class="navbar">
      <a href="dashboard.php" class="nav-item">Trang chủ</a>
      <a href="products.php" class="nav-item">Sản phẩm</a>
      <a href="introduction.php" class="nav-item">Giới thiệu</a>
      <a href="#contact" class="nav-item">Liên hệ</a>
    </div>
    <div class="header-right">
      <div class="cart-icon">
        <a href="cart.php" title="Giỏ hàng" aria-label="Giỏ hàng"><i class="fa-solid fa-cart-shopping"></i></a>
        <span id="cart-count">0</span>
      </div>
      <a href="login.php" title="Tài khoản" aria-label="Tài khoản"><i class="fa-regular fa-user"></i></a>
    </div>
  </header>

  <main class="checkout-page">
    <div class="checkout-container">
      <section class="checkout-left">
        <h1>Thanh toán</h1>

        <article class="checkout-card">
          <h3>Thông tin giao hàng</h3>

          <input id="name" placeholder="Họ và tên *">

          <div class="checkout-row">
            <input id="phone" placeholder="SĐT *">
            <input id="email" placeholder="Email *">
          </div>

          <input id="address" placeholder="Địa chỉ *">

          <div class="checkout-row">
            <select id="city" title="Tỉnh/Thành phố"><option value="">Tỉnh/Thành phố *</option></select>
            <select id="district" title="Quận/Huyện"><option value="">Quận/Huyện *</option></select>
          </div>
        </article>

        <article class="checkout-card" id="shippingBox">
          <h3>Phương thức vận chuyển</h3>

          <div class="option active" data-ship="25000">
            <div class="opt-left">
              <span class="dot"></span>
              <div class="text">
                <span class="title">Tiết kiệm</span>
                <span class="sub">Giao hàng trong 3-5 ngày</span>
              </div>
            </div>
            <div class="option-price">25.000đ</div>
          </div>

          <div class="option" data-ship="40000">
            <div class="opt-left">
              <span class="dot"></span>
              <div class="text">
                <span class="title">Nhanh</span>
                <span class="sub">Giao hàng trong 1-2 ngày</span>
              </div>
            </div>
            <div class="option-price">40.000đ</div>
          </div>

          <div class="option" data-ship="60000">
            <div class="opt-left">
              <span class="dot"></span>
              <div class="text">
                <span class="title">Hỏa tốc</span>
                <span class="sub">Giao hàng trong 4-6 giờ</span>
              </div>
            </div>
            <div class="option-price">60.000đ</div>
          </div>
        </article>

        <article class="checkout-card" id="paymentBox">
          <h3>Phương thức thanh toán</h3>

          <div class="option active" data-pay="cod">
            <div class="opt-left">
              <span class="dot"></span>
              <div class="text">
                <span class="title">Thanh toán khi nhận hàng (COD)</span>
                <span class="sub">Thanh toán bằng tiền mặt</span>
              </div>
            </div>
          </div>

          <div class="option" data-pay="vnpay">
            <div class="opt-left">
              <span class="dot"></span>
              <div class="text">
                <span class="title">VNPAY</span>
                <span class="sub">Thanh toán qua VNPAY</span>
              </div>
            </div>
          </div>

          <div id="qrBox" class="qr hidden">
            <img id="qrImg" alt="Mã QR thanh toán">
          </div>
        </article>
      </section>

      <aside class="checkout-right">
        <h3>Đơn hàng của bạn</h3>

        <div id="cartItems"></div>
        <p id="emptyMsg" class="empty hidden">Giỏ hàng đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.</p>

        <hr>

        <p class="row-price">Tạm tính <span id="subtotal">0đ</span></p>
        <p class="row-price">Phí vận chuyển <span id="ship">25.000đ</span></p>

        <h2 class="total">Tổng <span id="total">0đ</span></h2>

        <button id="placeOrderBtn" class="order-btn" type="button">Xác nhận & Đặt hàng</button>
      </aside>
    </div>
  </main>

  <div id="checkoutToast" class="checkout-toast" role="status" aria-live="polite"></div>

  <footer class="footer">
    <div class="footer-container">
      <div class="footer-col brand-info">
        <div class="logo-footer">
          <strong>YumYum Corners</strong>
        </div>
        <p>
          Điểm đến lý tưởng cho những món ăn vặt ngon và chất lượng. Chúng tôi
          mang đến niềm vui cho mỗi bữa ăn nhẹ của bạn.
        </p>
        <div class="social-icons">
          <i class="fa-brands fa-facebook"></i>
          <i class="fa-brands fa-instagram"></i>
          <i class="fa-brands fa-youtube"></i>
        </div>
      </div>
      <div class="footer-col">
        <h3>Liên kết nhanh</h3>
        <ul>
          <li><a href="../auth/dashboard.php">Trang chủ</a></li>
          <li><a href="../auth/products.php">Sản phẩm</a></li>
          <li><a href="../auth/introduction.php">Về chúng tôi</a></li>
          <li><a href="../auth/introduction.php">Liên hệ</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h3>Chính sách</h3>
        <ul>
          <li><a href="#">Chính sách đổi trả</a></li>
          <li><a href="#">Chính sách vận chuyển</a></li>
          <li><a href="#">Chính sách bảo mật</a></li>
        </ul>
      </div>
      <div class="footer-col" id="contact">
        <h3>Liên hệ</h3>
        <p>
          <i class="fa-solid fa-location-dot"></i> 79, Hồ Tùng Mậu, Từ Liêm,
          Hà Nội
        </p>
        <p><i class="fa-solid fa-phone"></i> 1900 1234</p>
        <p><i class="fa-solid fa-envelope"></i> hello@yumyum.vn</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2026 YumYum Corners</p>
    </div>
  </footer>

  <script src="https://kit.fontawesome.com/e9591eab39.js" crossorigin="anonymous"></script>
  <script src="../../js/auth/userMenu.js"></script>
  <script src="../../js/auth/checkout.js"></script>
</body>
</html>