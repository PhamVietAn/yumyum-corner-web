<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chính Sách Chi Tiết - YumYum Corners</title>
    <style>
        :root {
            --primary-color: #ffcc00;
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #e0e0e0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.8;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        .policy-card {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            border-left: 5px solid var(--primary-color);
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        h1 {
            text-align: center;
            color: var(--primary-color);
            font-size: 2.5rem;
            margin-bottom: 50px;
        }
        h2 {
            color: var(--primary-color);
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
        }
        h2 i { margin-right: 10px; }
        ul { padding-left: 20px; }
        li { margin-bottom: 10px; }
        .back-home {
            display: block;
            width: fit-content;
            margin: 40px auto;
            padding: 12px 30px;
            background: var(--primary-color);
            color: #000;
            text-decoration: none;
            font-weight: bold;
            border-radius: 30px;
            transition: 0.3s;
        }
        .back-home:hover {
            transform: scale(1.05);
            background: #ffdb4d;
        }
        strong { color: #fff; }
    </style>
</head>
<body>

<div class="container">
    <h1>QUY ĐỊNH & CHÍNH SÁCH</h1>

    <div class="policy-card" id="doi-tra">
        <h2>1. Chính sách đổi trả</h2>
        <p>YumYum Corners luôn đặt sự hài lòng của khách hàng lên hàng đầu. Quy định đổi trả như sau:</p>
        <ul>
            <li><strong>Thời gian:</strong> Hỗ trợ đổi trả trong vòng <strong>24h</strong> đối với thực phẩm tươi và <strong>7 ngày</strong> đối với thực phẩm đóng gói.</li>
            <li><strong>Điều kiện:</strong> Sản phẩm còn nguyên bao bì, chưa qua sử dụng hoặc có bằng chứng (hình ảnh/video) sản phẩm bị hư hỏng, hết hạn khi vừa nhận hàng.</li>
            <li><strong>Chi phí:</strong> Miễn phí 100% chi phí đổi trả nếu lỗi thuộc về cửa hàng.</li>
        </ul>
    </div>

    <div class="policy-card" id="van-chuyen">
        <h2>2. Chính sách vận chuyển</h2>
        <p>Chúng tôi cam kết giao hàng nhanh nhất để giữ được hương vị món ăn:</p>
        <ul>
            <li><strong>Nội thành Hà Nội:</strong> Giao hàng từ 30 - 45 phút kể từ khi xác nhận đơn hàng.</li>
            <li><strong>Phí ship:</strong> Miễn phí vận chuyển cho đơn hàng trên 200.000đ. Đơn hàng dưới 200.000đ áp dụng phí cố định 20.000đ.</li>
            <li><strong>Thời gian hoạt động:</strong> Từ 08:00 đến 22:00 tất cả các ngày trong tuần.</li>
        </ul>
    </div>

    <div class="policy-card" id="bao-mat">
        <h2>3. Chính sách bảo mật</h2>
        <p>Thông tin của bạn được bảo vệ nghiêm ngặt:</p>
        <ul>
            <li><strong>Mục đích:</strong> Chỉ sử dụng thông tin (Tên, SĐT, Địa chỉ) để xử lý đơn hàng và chăm sóc khách hàng.</li>
            <li><strong>Cam kết:</strong> Không chia sẻ, bán hoặc cung cấp thông tin cá nhân cho bên thứ ba vì mục đích thương mại.</li>
            <li><strong>Lưu trữ:</strong> Thông tin giao dịch được lưu trữ mã hóa trên hệ thống an toàn của YumYum.</li>
        </ul>
    </div>

    <a href="dashboard.php" class="back-home">Về Trang Chủ</a>
</div>

<script>
    (function ensureFreshDataAfterNavigation() {
        const reloadFlagKey = "__forceReloadOnce:" + window.location.pathname + window.location.search;
        const navigationEntries =
            typeof performance.getEntriesByType === "function"
                ? performance.getEntriesByType("navigation")
                : [];
        const navigationType = navigationEntries && navigationEntries[0] ? navigationEntries[0].type : "";

        const hasInternalReferrer = (function () {
            if (!document.referrer) return false;
            try {
                return new URL(document.referrer).origin === window.location.origin;
            } catch (_error) {
                return false;
            }
        })();

        if (hasInternalReferrer && navigationType === "navigate") {
            if (sessionStorage.getItem(reloadFlagKey) !== "1") {
                sessionStorage.setItem(reloadFlagKey, "1");
                window.location.reload();
                return;
            }
            sessionStorage.removeItem(reloadFlagKey);
        }

        window.addEventListener("pageshow", function (event) {
            const navEntries =
                typeof performance.getEntriesByType === "function"
                    ? performance.getEntriesByType("navigation")
                    : [];
            const navType = navEntries && navEntries[0] ? navEntries[0].type : "";

            if (event.persisted || navType === "back_forward") {
                window.location.reload();
            }
        });
    })();
</script>

</body>
</html>