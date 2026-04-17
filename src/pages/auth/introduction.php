<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/public/logo.jpg" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/home.css">
    <title>YumYum Corners</title>
</head>
<body>
    <header class="header">
        <div class="logo" style="display: flex; align-items: center; gap: 8px; width: 185px;">
            <img title="logo" src="../../../public/logo.jpg " style="width: 40px; height: 40px; object-fit: cover;">
            <p style="color: #f97316;"><strong>YumYum</strong><span class="brand-sub"> Corners</span></p> 
        </div>
        <div class="navbar">
           <a href="../../pages/auth/dashboard.php" class="nav-item">Trang chủ</a>
           <a href="../../pages/auth/products.php" class="nav-item">Sản phẩm</a>
           <a href="../../pages/auth/introduction.php" class="nav-item active">Giới thiệu</a>
           <a href="#contact" class="nav-item">Liên hệ</a>
        </div>
        <div class="header-right" style="width: 185px; display: flex; justify-content: flex-end; align-items: center; gap: 16px;">
            <div class="cart-icon">
                <a href="../../pages/auth/cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
                <span id="cart-count">0</span>
            </div>
            <a href="../../pages/auth/login.php"><i class="fa-regular fa-user"></i></a>
        </div>
    </header>
    <section class="introbanner">
        <div>
            <h1>YumYum Corners</h1>
            <p>Thiên đường đồ ăn vặt dành cho giới trẻ</p>
        </div>
    </section>
    <section class="about-section">
        <img title="home" src="../../assets/home/pexels-nunzdy-30588247.jpg">
        <div class="about-text">
            <h2>Câu chuyện của chúng tôi</h2>
            <p> 
                YumYum Corners được tạo ra với mong muốn mang đến những món ăn vặt ngon nhất,
                chất lượng nhất với giá cả hợp lý cho giới trẻ.
            </p>
            <p>
                Chúng tôi tin rằng mỗi món ăn không chỉ là hương vị mà còn là trải nghiệm,
                là niềm vui và sự kết nối giữa mọi người.
            </p>
        </div>
    </section>
    <section class="features">
        <h2>Tại sao chọn chúng tôi?</h2>
        <div class="feature-list">
            <div class="feature-box">
                <i class="fa-solid fa-burger"></i>
                <h3>Đa dạng sản phẩm</h3>
                <p>Hàng trăm món ăn vặt từ khắp nơi</p>
            </div>
            <div class="feature-box">
                <i class="fa-solid fa-truck"></i>
                <h3>Giao hàng nhanh</h3>
                <p>Giao hàng chỉ trong 1-2 giờ</p>
            </div>
            <div class="feature-box">
                <i class="fa-solid fa-star"></i>
                <h3>Chất lượng đảm bảo</h3>
                <p>Cam kết sản phẩm chính hãng</p>
            </div>
        </div>
    </section>
    <section class="introstats">
        <div>
            <h3>1.000+</h3>
            <p>Khách hàng</p>
        </div>
        <div>
            <h3>50+</h3>
            <p>Sản phẩm</p>
        </div>
        <div>
            <h3>4.9 <i class="fa-solid fa-star"></i></h3>
            <p>Đánh giá</p>
        </div>
    </section>
    <section class="team">
        <h2>Đội ngũ của chúng tôi</h2>
        <div class="team-list">
            <div class="member">
                <h4>Đặng Thị Phương Anh</h4>
            </div>
            <div class="member">
                <h4>Đoàn Vân Anh</h4>
            </div>
            <div class="member">
                <h4>Trần Lê Diệp Chi</h4>
            </div>
            <div class="member">
                <h4>Nguyễn Mai Phương</h4>
            </div>
            <div class="member">
                <h4>Nguyễn Thanh Phương</h4>
            </div>
            <div class="member">
                <h4>Nguyễn Thị Quế Thương</h4>
            </div>
            <div class="member">
                <h4>Nguyễn Hà Trang</h4>
            </div>
        </div>
    </section>
    <section class="cta">
        <h2>Khám phá ngay thế giới đồ ăn vặt</h2>
        <p>Đừng bỏ lỡ những ưu đãi hấp dẫn mỗi ngày!</p>
        <a href="products.php">Mua ngay</a>
    </section>
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
    <script src="../../js/auth/home.js"></script>
</body>
</html>