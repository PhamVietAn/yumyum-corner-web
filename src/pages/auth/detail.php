<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết sản phẩm</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="/public/logo.jpg" />
    <link rel="stylesheet" href="../../css/detail.css">
    <link rel="stylesheet" href="../../css/home.css">
</head>

<body class="detail-page">
    <!-- head -->
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
        <div class="header-right">
            <div class="cart-icon">
                <a href="../../pages/auth/cart.php" title="Giỏ hàng" aria-label="Giỏ hàng"> <i class="fa-solid fa-cart-shopping"></i></a>
                <span id="cart-count">0</span>
            </div>
            <a href="../../pages/auth/login.php" title="Tài khoản" aria-label="Tài khoản"> <i class="fa-regular fa-user"></i></a>
        </div>
    </header>

    <main class="detail-shell">
        <div class="detail-breadcrumb-wrap">
            <a class="detail-back-link" href="products.php">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Quay lại</span>
            </a>
        </div>

        <section class="detail-grid">
            <div class="detail-gallery">
                <div class="detail-main-frame">
                    <img id="mainImg" class="detail-main-img" title="Hình sản phẩm" src="https://via.placeholder.com/300" alt="Sản phẩm">
                </div>
                <div class="detail-thumbs" id="thumbs"></div>
            </div>

            <div class="detail-info-pane">
                <div class="detail-category" id="category"></div>
                <h1 id="name" class="detail-title"></h1>

                <div class="detail-rating-row">
                    <div class="detail-stars" id="ratingStars"></div>
                    <span class="detail-rating-value" id="ratingValue">0.0</span>
                    <span class="detail-rating-count" id="reviewCountLabel">(0 đánh giá)</span>
                </div>

                <div class="detail-price-row" id="price"></div>

                <div class="detail-desc-block">
                    <h3>Mô tả sản phẩm</h3>
                    <p id="desc"></p>
                </div>

                <div class="detail-stock" id="stockStatus"></div>

                <div class="detail-qty-block">
                    <h3>Số lượng</h3>
                    <div class="quantity">
                        <button type="button" onclick="decrease()">-</button>
                        <input type="text" id="qty" value="1" title="Số lượng">
                        <button type="button" onclick="increase()">+</button>
                    </div>
                </div>

                <div class="actions">
                    <button class="add" type="button" onclick="handleAddToCart(false)">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span>Thêm vào giỏ hàng</span>
                    </button>
                    <button class="buy" type="button" onclick="handleAddToCart(true)">Mua ngay</button>
                </div>

                <div class="detail-extra-actions">
                    <button type="button" class="detail-icon-btn" title="Yêu thích" aria-label="Yêu thích">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                    <button type="button" class="detail-icon-btn" title="Chia sẻ" aria-label="Chia sẻ">
                        <i class="fa-solid fa-share-nodes"></i>
                    </button>
                </div>
            </div>
        </section>

        <section class="detail-reviews">
            <h2>Đánh giá từ khách hàng</h2>
            <div id="reviewList"></div>
        </section>

        <section class="detail-related">
            <h2>Sản phẩm tương tự</h2>
            <div class="related-list" id="relatedList"></div>
        </section>
    </main>

<div id="detailToast" class="detail-toast" role="status" aria-live="polite"></div>
<!-- footer -->
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
<script src="../../js/auth/detail.js"></script>

</body>
</html>