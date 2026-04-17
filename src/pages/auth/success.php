<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="/public/logo.jpg" />
    <title>Thành công</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: "Inter", sans-serif;
            background: #f5f5f5;
        }

        .box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
            background: #f97316;
            color: white;
            border: none;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <div class="box">
        <h2>🎉 Đặt hàng thành công!</h2>
        <button onclick="location.href='dashboard.php'">Về trang chủ</button>
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