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
    <title>YumYum Corners</title>
</head>
<body>
    <header class="header">
        <div class="logo" style="display: flex; align-items: center; gap: 8px; width: 185px;">
            <img title="logo" src="../../../public/logo.jpg" style="width: 40px; height: 40px; object-fit: cover;">
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
                <a href="cart.php"> <i class="fa-solid fa-cart-shopping"></i></a>
                <span id="cart-count">0</span>
            </div>
            <a href="login.php"> <i class="fa-regular fa-user"></i></a>
        </div>
    </header>
    <section id="banner">
        <div class="box-left">
            <div class="bleft">
                <h2>
                    <b><span>MÓN NGON MỖI NGÀY</span></b>
                    <br>
                    <span> niềm vui tràn đầy</span>
                </h2>
                <p> Khám phá hàng trăm món ăn ngon miệng, chất lượng cao từ khắp nơi trên thế giới. Giao hàng nhanh chóng, ưu đãi hấp dẫn mỗi ngày! </p>
            </div>
                <button> <a href="products.php"> Mua ngay </a> </button>
            <div class="stats">
                <div class="stat">
                    <h3> 50+</h3>
                    <p>Sản phẩm</p>
                </div>
                <div class="stat">
                    <h3>1K+</h3>
                    <p>Khách hàng</p>
                </div>
                <div class="stat">
                    <h3>4.9 <i class="fa-solid fa-star"></i></h3>
                    <p>Đánh giá</p>
                </div>
            </div>
        </div>
        <div class="box-right">
            <img src="../../assets/home/1.jpg" alt="banner 1">
            <img src="../../assets/home/1 (2).jpg" alt="banner 2">
            <img src="../../assets/home/1 (3).jpg" alt="banner 3">
        </div>
    </section>
    <section class="featured-cate">
        <div class="containers">
            <h2 class="section-title center">Danh mục nổi bật</h2>
            <p class="section-sub">Khám phá các loại đồ ăn vặt yêu thích của bạn</p>
            <div class="cate-grid">
                <div class="cate-card">
                    <span><img src="../../assets/home/snacks_4689118.png" alt="Snack"></span>
                    <p>Snack</p>
                </div>
                <div class="cate-card">
                    <span><img src="../../assets/home/soda_1967382.png" alt="Đồ uống"></span>
                    <p>Đồ uống</p>
                </div>
                <div class="cate-card">
                    <span><img src="../../assets/home/fruits_11827865.png" alt="Trái cây sấy"></span>
                    <p>Trái cây sấy</p>
                </div>
                <div class="cate-card">
                    <span><img src="../../assets/home/sweet-stuff_16467373.png" alt="Kẹo và bánh"></span>
                    <p>Kẹo & Bánh</p>
                </div>
                <div class="cate-card">
                    <span><img src="../../assets/home/mortar_18385012.png" alt="Hạt dinh dưỡng"></span>
                    <p>Hạt dinh dưỡng</p>
                </div>
                <div class="cate-card">
                    <span><img src="../../assets/home/ice-cream_11747642.png" alt="Kem và đông lạnh"></span>
                    <p>Kem & Đông lạnh</p>
                </div>
            </div>
        </div>
    </section>
    <section class="home-products">
        <div class="containerss">
            <h2 class="section-title">Sản phẩm nổi bật</h2>
            <h3 class="section-subtitle"> Những món ăn vặt được yêu thích nhất </h3>
            <div id="list-products" class="home-products-grid"></div>
            <div class="view-all-wrap">
                <a href="products.php" class="view-all-btn"> <b>Xem tất cả sản phẩm</b></a>
            </div>
        </div>
    </section>
    <div id="saleoff">
        <div class="box-left">
            <h1>
                <span>GIẢM GIÁ LÊN ĐẾN</span>
                <span>30%</span>
            </h1>
        </div>
        <div class="box-right"></div>
    </div>
    <section id="subscribe">
        <div class="subscribe-content">
            <h2>Đăng ký nhận ưu đãi đặc biệt</h2>
            <p>Nhận ngay mã giảm giá 15% cho đơn hàng đầu tiên khi đăng ký!</p>
            <div class="subscribe-form">
                <input type="email" id="emailInput" placeholder="Nhập email của bạn">
                <button onclick="handleSubscribe()">Đăng ký ngay</button>
            </div>
            <p id="message" class="message"></p>
        </div>
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
    <script src="../../js/auth/home.js"></script>
</body>
</html>