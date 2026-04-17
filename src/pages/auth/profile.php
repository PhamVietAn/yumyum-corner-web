<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thông tin cá nhân | YumYum Corners</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="/public/logo.jpg" />
  <link rel="stylesheet" href="../../css/home.css">
  <link rel="stylesheet" href="../../css/profile.css">
</head>
<body class="profile-body">
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

  <main class="profile-page">
    <div class="profile-container">
      <h1 class="profile-title">Thông tin cá nhân</h1>

      <div class="profile-grid">
        <section class="profile-sidebar-card">
          <div class="profile-avatar" id="avatarLetter">U</div>
          <div class="profile-user-head">
            <h2 id="sidebarName">Người dùng</h2>
            <p id="sidebarEmail">email@example.com</p>
          </div>

          <div class="profile-stat-list">
            <div class="profile-stat-item">
              <span>Tổng đơn hàng</span>
              <strong id="totalOrders">0</strong>
            </div>
            <div class="profile-stat-item">
              <span>Tổng chi tiêu</span>
              <strong id="totalSpent">0đ</strong>
            </div>
          </div>
        </section>

        <section class="profile-main-col">
          <article class="profile-card">
            <div class="profile-card-head">
              <h3>Thông tin cá nhân</h3>
              <p>Cập nhật thông tin tài khoản của bạn</p>
            </div>

            <form id="profileForm" class="profile-form">
              <label for="fullName">Họ và tên</label>
              <input id="fullName" type="text" placeholder="Nhập họ và tên">

              <label for="email">Email</label>
              <input id="email" type="email" placeholder="Nhập email">

              <label for="phone">Số điện thoại</label>
              <input id="phone" type="tel" placeholder="Nhập số điện thoại">

              <label for="address">Địa chỉ</label>
              <textarea id="address" placeholder="Nhập địa chỉ"></textarea>

              <button type="submit" class="profile-btn primary">Lưu thay đổi</button>
            </form>
          </article>

          <article class="profile-card">
            <div class="profile-card-head">
              <h3>Đổi mật khẩu</h3>
              <p>Đảm bảo tài khoản của bạn dùng mật khẩu mạnh</p>
            </div>

            <form id="passwordForm" class="profile-form">
              <label for="currentPassword">Mật khẩu hiện tại</label>
              <input id="currentPassword" type="password" placeholder="••••••••">

              <label for="newPassword">Mật khẩu mới</label>
              <input id="newPassword" type="password" placeholder="••••••••">

              <label for="confirmPassword">Xác nhận mật khẩu mới</label>
              <input id="confirmPassword" type="password" placeholder="••••••••">

              <button type="submit" class="profile-btn primary">Đổi mật khẩu</button>
            </form>
          </article>

          <article class="profile-card danger-zone">
            <div class="profile-card-head">
              <h3>Vùng nguy hiểm</h3>
              <p>Xóa tài khoản sẽ xóa toàn bộ dữ liệu của bạn và không thể hoàn tác.</p>
            </div>

            <button id="deleteAccountBtn" type="button" class="profile-btn danger">Xóa tài khoản</button>
          </article>
        </section>
      </div>
    </div>
  </main>

  <div id="deleteModal" class="profile-modal hidden" aria-hidden="true">
    <div class="profile-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
      <h3 id="deleteModalTitle">Bạn có chắc chắn không?</h3>
      <p>Hành động này không thể hoàn tác. Tài khoản của bạn sẽ bị xóa vĩnh viễn.</p>
      <div class="profile-modal-actions">
        <button id="cancelDeleteBtn" type="button" class="profile-btn secondary">Hủy</button>
        <button id="confirmDeleteBtn" type="button" class="profile-btn danger">Xóa tài khoản</button>
      </div>
    </div>
  </div>

  <div id="profileToast" class="profile-toast" role="status" aria-live="polite"></div>

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
  <script src="../../js/auth/profile.js"></script>
</body>
</html>
