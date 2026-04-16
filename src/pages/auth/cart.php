<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="/public/logo.jpg" />
    <link rel="stylesheet" href="../../css/home.css">
  <link rel="stylesheet" href="../../css/cart.css">
    <title>YumYum Corners</title>
</head>
<body>
    <header class="header">
        <div class="logo" style="display: flex; align-items: center; gap: 8px; width: 185px;">
            <img title="logo" src="../../../public/logo.jpg " style="width: 40px; height: 40px; object-fit: cover;">
            <p style="color: #f97316;"><strong>YumYum</strong><span class="brand-sub"> Corners</span></p> 
        </div>
        <div class="navbar">
           <a href="../../pages/auth/dashboard.php" class="nav-item active">Trang chủ</a>
           <a href="../../pages/auth/products.php" class="nav-item">Sản phẩm</a>
           <a href="../../pages/auth/introduction.php" class="nav-item ">Giới thiệu</a>
           <a href="#contact" class="nav-item">Liên hệ</a>
        </div>
        <div class="header-right">
            <div class="cart-icon">
            <a href="cart.php" title="Giỏ hàng" aria-label="Giỏ hàng"> <i class="fa-solid fa-cart-shopping"></i></a>
                <span id="cart-count">0</span>
            </div>
          <a href="login.php" title="Tài khoản" aria-label="Tài khoản"> <i class="fa-regular fa-user"></i></a>
        </div>
    </header>

  <main class="cart-page">
    <div class="cart-wrapper">
      <section id="emptyCart" class="cart-empty hidden" aria-live="polite">
        <div class="cart-empty-icon">🛒</div>
        <h2>Giỏ hàng trống</h2>
        <p>Bạn chưa có sản phẩm nào trong giỏ hàng. Hãy khám phá các món ăn vặt ngon của chúng tôi nhé!</p>
        <a class="continue-btn" href="products.php">
          <i class="fa-solid fa-bag-shopping"></i>
          <span>Tiếp tục mua sắm</span>
        </a>
      </section>

      <section id="cartContent" class="cart-content hidden">
        <div class="cart-header-block">
          <a class="back-shopping" href="products.php">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Tiếp tục mua sắm</span>
          </a>
          <h1>Giỏ hàng của bạn</h1>
          <p id="cart-info">Bạn có 0 sản phẩm trong giỏ hàng</p>
        </div>

        <div class="cart-layout">
          <div class="cart-items-col">
            <div id="cartItems"></div>
          </div>

          <aside class="cart-summary">
            <h3>Tóm tắt đơn hàng</h3>

            <div class="summary-row">
              <span>Tạm tính</span>
              <span id="subtotal">0đ</span>
            </div>

            <div class="summary-row">
              <span>Phí vận chuyển</span>
              <span id="shippingFee">25.000đ</span>
            </div>

            <hr>

            <div class="summary-row total">
              <span>Tổng cộng</span>
              <span id="total">0đ</span>
            </div>

            <button id="checkoutBtn" class="checkout-btn" type="button">Tiến hành thanh toán</button>

            <ul class="policy">
              <li>✔ Miễn phí vận chuyển cho đơn hàng trên 500.000đ</li>
              <li>✔ Đổi trả trong vòng 7 ngày</li>
              <li>✔ Thanh toán an toàn & bảo mật</li>
            </ul>
          </aside>
        </div>
      </section>
    </div>
  </main>

  <div id="removeModal" class="cart-modal hidden" aria-hidden="true">
    <div class="cart-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="removeModalTitle">
      <h3 id="removeModalTitle">Xác nhận xoá sản phẩm</h3>
      <p id="removeModalText">Bạn có chắc chắn muốn xoá sản phẩm này khỏi giỏ hàng?</p>
      <div class="cart-modal-actions">
        <button id="modalCancelBtn" class="modal-btn modal-cancel" type="button">Huỷ</button>
        <button id="modalConfirmBtn" class="modal-btn modal-confirm" type="button">Xoá sản phẩm</button>
      </div>
    </div>
  </div>

  <div id="cartToast" class="cart-toast" role="status" aria-live="polite"></div>
<!-- FOOTER -->
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
<script src="../../js/auth/cart.js"></script>

</body>
</html>