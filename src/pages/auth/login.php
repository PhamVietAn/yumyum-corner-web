<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="/public/logo.jpg" />
    <title>YumYum Corners - Đăng nhập</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    />
    <link rel="stylesheet" href="../../css/login.css" />
  </head>
  <body>
    <header>
      <div class="header-content">
        <div class="logo">
          <img
            src="../../../public/logo.jpg"
            alt="YumYum Logo"
            style="
              width: 35px;
              height: 35px;
              border-radius: 50%;
              object-fit: cover;
            "
          />
          <strong>YumYum</strong> <span class="brand-sub">Corner</span>
        </div>
        <div class="header-right">
          <a class="help-link" href="#">Cần giúp đỡ?</a>
        </div>
      </div>
    </header>

    <main>
      <div class="login-card">
        <div
          class="main-logo"
          style="background: none; overflow: hidden; border: 2px solid #ff7e47"
        >
          <img
            src="../../../public/logo.jpg"
            alt="Logo Center"
            style="width: 100%; height: 100%; object-fit: cover"
          />
        </div>
        <h1>Chào mừng đến YumYum!</h1>
        <p class="subtitle">Đăng nhập hoặc tạo tài khoản mới</p>

        <div class="tab-control">
          <button class="tab active" data-tab="login" type="button">Đăng nhập</button>
          <a class="tab tab-link" href="/src/pages/auth/register.php">Đăng ký</a>
        </div>

        <form id="loginForm" novalidate>
          <div class="input-group">
            <label>Email</label>
            <div class="input-field" id="emailFieldGroup">
              <i class="fa-regular fa-envelope"></i>
              <input
                type="email"
                id="emailInput"
                placeholder="your@email.com"
              />
            </div>
            <span id="emailError" class="error-text"></span>
          </div>

          <div class="input-group">
            <label>Mật khẩu</label>
            <div class="input-field" id="passwordFieldGroup">
              <i class="fa-solid fa-lock"></i>
              <input
                type="password"
                id="passwordField"
                placeholder="••••••••"
              />
              <i class="fa-regular fa-eye toggle-pass" id="togglePassword"></i>
            </div>
            <span id="passwordError" class="error-text"></span>
          </div>

          <div class="form-actions">
            <label class="remember-me">
              <input type="checkbox" /> Ghi nhớ đăng nhập
            </label>
            <a href="#" class="forgot-pass">Quên mật khẩu?</a>
          </div>

          <button type="submit" class="btn-submit">Đăng nhập</button>
        </form>

        <div class="divider">Hoặc đăng nhập với</div>

        <div class="social-login">
          <button type="button" id="btnGoogle" class="btn-social">
            <i class="fa-brands fa-google" style="color: #db4437"></i> Google
          </button>
          <button type="button" id="btnFacebook" class="btn-social">
            <i class="fa-brands fa-facebook-f" style="color: #4267b2"></i>
            Facebook
          </button>
        </div>
      </div>
      <p class="bottom-slogan">
        Chào bạn! Hãy cùng khám phá thế giới đồ ăn nhé!
      </p>
    </main>

    <footer>
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
                    <li><a href="#contact">Liên hệ</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Chính sách</h3>
                <ul>
                    <li><a href="chinh-sach.php#doi-tra">Chính sách đổi trả</a></li>
                    <li><a href="chinh-sach.php#van-chuyen">Chính sách vận chuyển</a></li>
                    <li><a href="chinh-sach.php#bao-mat">Chính sách bảo mật</a></li>
                </ul>
            </div>
        <div class="footer-col">
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

    <script src="../../js/auth/login.js"></script>
  </body>
</html>
