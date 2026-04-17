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
           <a href="../../pages/auth/dashboard.php" class="nav-item">Trang chủ</a>
           <a href="../../pages/auth/products.php" class="nav-item active">Sản phẩm</a>
           <a href="../../pages/auth/introduction.php" class="nav-item">Giới thiệu</a>
           <a href="#contact" class="nav-item">Liên hệ</a>
        </div>
        <div class="header-right">
            <div class="cart-icon">
                <a href="../../pages/auth/cart.php"> <i class="fa-solid fa-cart-shopping"></i></a>
                <span id="cart-count">0</span>
            </div>
            <a href="../../pages/auth/login.php"> <i class="fa-regular fa-user"></i></a>
        </div>
    </header>
    <section class="shop-page">
        <div class="shop-container">
            <div class="shop-sidebar">
                <h3>Bộ lọc</h3>
                <p class="filter-title">Danh mục</p>
                <div id="categoryFilters">
                    <div class="filter-item active" onclick="filterCate('all',this)">Tất cả</div>
                </div>
                <p class="filter-title mt">Khoảng giá</p>
                <input type="range" min="0" max="100000" value="100000" id="priceRange" title="Khoảng giá" >
                <div class="price-text">0đ — <span id="priceValue">100000</span>đ</div>
            </div>
            <div class="shop-content">
                <div class="shop-top">
                    <select title="sapxep" onchange="sortPrice(this.value)">
                        <option value="">Sắp xếp</option>
                        <option value="asc">Giá tăng dần</option>
                        <option value="desc">Giá giảm dần</option>
                    </select>
                </div>
                <div id="list-products" class="home-products-grid"></div>
            </div>
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