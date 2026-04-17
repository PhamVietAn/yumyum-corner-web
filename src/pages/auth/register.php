<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/jpeg" href="/logo.jpg" />
    <title>YumYum Corners - Đăng ký</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="/public/logo.jpg" />
    <link rel="stylesheet" href="../../css/login.css" />
    <link rel="stylesheet" href="../../css/register.css" />
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
      <div class="login-card register-card">
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
        <h1>Tạo tài khoản YumYum</h1>
        <p class="subtitle">Điền thông tin để bắt đầu mua sắm</p>

        <div class="tab-control">
          <a class="tab tab-link" href="/src/pages/auth/login.php">Đăng nhập</a>
          <button class="tab active" type="button">Đăng ký</button>
        </div>

        <form id="registerForm" novalidate>
          <div class="input-group">
            <label>Họ và tên</label>
            <div class="input-field" id="nameFieldGroup">
              <i class="fa-regular fa-user"></i>
              <input type="text" id="name" placeholder="Nguyễn Văn A" />
            </div>
            <span class="error-text" id="nameError"></span>
          </div>

          <div class="input-group">
            <label>Email</label>
            <div class="input-field" id="emailFieldGroup">
              <i class="fa-regular fa-envelope"></i>
              <input type="email" id="email" placeholder="your@email.com" />
            </div>
            <span class="error-text" id="emailError"></span>
          </div>

          <div class="input-group">
            <label>Mật khẩu</label>
            <div class="input-field" id="passwordFieldGroup">
              <i class="fa-solid fa-lock"></i>
              <input type="password" id="password" placeholder="••••••••" />
            </div>
            <span class="error-text" id="passwordError"></span>
          </div>

          <div class="input-group">
            <label>Nhập lại mật khẩu</label>
            <div class="input-field" id="confirmFieldGroup">
              <i class="fa-solid fa-shield-heart"></i>
              <input
                type="password"
                id="confirmPassword"
                placeholder="••••••••"
              />
            </div>
            <span class="error-text" id="confirmError"></span>
          </div>

          <button type="submit" class="btn-submit">Đăng ký tài khoản</button>
          <p class="success-text" id="successMsg"></p>
        </form>

        <div class="divider">Hoặc đăng ký với</div>

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

      <p class="bottom-slogan">Đăng ký nhanh để nhận ưu đãi thành viên mới!</p>
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
                    <li><a href="../auth/chinh-sach.php#doi-tra">Chính sách đổi trả</a></li>
                    <li><a href="../auth/chinh-sach.php#van-chuyen">Chính sách vận chuyển</a></li>
                    <li><a href="../auth/chinh-sach.php#bao-mat">Chính sách bảo mật</a></li>
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

    <script src="../../js/auth/register.js"></script>
  </body>
</html>