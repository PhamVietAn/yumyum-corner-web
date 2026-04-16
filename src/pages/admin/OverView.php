<!DOCTYPE html>
<html lang="en" data-theme="bumblebee">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YumYum Corner</title>
    <link rel="icon" type="image/jpeg" href="/public/logo.jpg" />
    <link href="/public/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-black font-[Inter] grid grid-cols-[256px_1fr]">
    <div class="min-h-screen w-[256px] bg-white text-[#314158] flex flex-col">
        <div>
            <div class="flex border-b border-gray-200 items-center justify-between p-6">
                <img src="../../../public/logo.jpg" alt="logo" title="logo" class="w-10.5 ">
                <div>
                    <h1 class="text-[20px] font-bold ">YumYum Corner</h1>
                    <p class="text-gray-500 text-[12px]">Admin Dashboard</p>
                </div>
            </div>
            <nav class="flex-1 p-4">
                <ul>
                    <li class="mb-2"><a href="/src/pages/admin/OverView.php" class="py-2 px-4 rounded-[10px] bg-orange-500 text-white hover:bg-orange-600 flex gap-3"><img src="../../assets/iconAdminDashboard/tongquan-white.svg" alt="" title="Tong quan"> Tổng quan</a></li>
                    <li class="mb-2"><a href="/src/pages/admin/productManager.php" class=" py-2 px-4 rounded-[10px] hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/qlsanpham.svg" alt="" title="Quản lý sản phẩm"> Quản lý sản phẩm</a></li>
                    <li class="mb-2"><a href="/src/pages/admin/order.php" class=" py-2 px-4 rounded-[10px] hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/donhang.svg" alt="" title="Quan ly don hang"> Quản lý đơn hàng</a></li>
                    <li class="mb-2"><a href="/src/pages/admin/categories.php" class=" py-2 px-4 rounded-[10px] hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/category.svg" alt="" title="Quan ly danh muc"> Quản lý danh mục</a></li>
                    <li class="mb-2"><a href="/src/pages/admin/customer.php" class=" py-2 px-4 rounded-[10px] hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/user.svg" alt="" title="Quan ly nguoi dung"> Quản lý khách hàng</a></li>
                </ul>
            </nav>
        </div>
        <div class="border-t border-gray-200 mt-[calc(100vh-372px-70px)] p-4">
            <a href="/src/pages/auth/dashboard.php" class="w-full py-2 px-4 rounded-[10px] flex gap-3 hover:bg-gray-100">
                <img src="../../assets/iconAdminDashboard/out.svg" alt="">
                Về trang chủ
            </a>
        </div>
    </div>
    <div class="p-8 w-full">
        <h1 class="text-3xl font-bold">Tổng quan</h1>
        <p class="text-gray-500 mt-2">Theo dõi hiệu suất kinh doanh của bạn</p>
        <div class="flex grid-cols-3 justify-between mt-6 gap-6">
            <div class="w-full h-37.5 bg-white rounded-[14px] p-6  ">
                <h2 class="text-xl font-bold mb-6">Tổng doanh thu</h2>
                <div>
                    <p class="text-2xl font-bold" id="totalRevenueValue">0đ</p>
                    <div class="flex gap-1.5">
                        <p class="text-green-500 text-[12px]" id="totalRevenueDelta">0%</p>
                        <p class="text-gray-500 text-[12px]">so với tháng trước</p>
                    </div>
                </div>
            </div>
            <div class="w-full h-37.5 bg-white rounded-[14px] p-6  ">
                <h2 class="text-xl font-bold mb-6">Tổng lượt mua</h2>
                <div>
                    <p class="text-2xl font-bold" id="totalPurchaseValue">0</p>
                    <div class="flex gap-1.5">
                        <p class="text-green-500 text-[12px]" id="totalPurchaseDelta">0%</p>
                        <p class="text-gray-500 text-[12px]">so với tháng trước</p>
                    </div>
                </div>
            </div>
            <div class="w-full h-37.5 bg-white rounded-[14px] p-6  ">
                <h2 class="text-xl font-bold mb-6">Người dùng mới</h2>
                <div>
                    <p class="text-2xl font-bold" id="newUsersValue">0</p>
                    <div class="flex gap-1.5">
                        <p class="text-green-500 text-[12px]" id="newUsersDelta">0%</p>
                        <p class="text-gray-500 text-[12px]">so với tháng trước</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex grid-cols-2 justify-between mt-6 gap-6">
            <div class="w-full h-105 bg-white rounded-[14px] p-6 overflow-hidden">
                <div class="flex justify-between ">
                    <div>
                        <p>Doanh thu theo tháng</p>
                        <p class="text-gray-500">6 tháng gần nhất</p>
                    </div>
                    <select title="Lọc theo danh mục" name="overviewCategoryFilter" id="overviewCategoryFilter" class="select border border-gray-300 rounded-[10px] p-3 focus:outline-none" style="width: 180px;">
                        <option value="all">Toàn bộ cửa hàng</option>
                    </select>
                </div>
                <div class="relative mt-4 h-80 w-full">
                    <canvas id="doanhThu" class="h-full w-full"></canvas>
                </div>
            </div>
            <div class="w-full h-105 bg-white rounded-[14px] p-6">
                <div>
                    <p>Số lượng đơn hàng</p>
                    <p class="text-gray-500">6 tháng gần nhất</p>
                </div>
                <div class="relative mt-4 h-80 w-full">
                    <canvas id="myChart" ></canvas>
                </div>
                
            </div>
        </div>
        <div class="bg-white rounded-[14px] mt-6 p-6">
            <p>Sản phẩm bán chạy nhất</p>
            <p class="text-gray-500">Top 5 sản phẩm trong tháng</p>
            <div class="overflow-x-auto mt-5">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên sản phẩm</th>
                            <th class="text-right">Số lượng bán</th>
                            <th class="text-right">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody id="topProductsBody">
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 py-8">Chưa có dữ liệu</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
    <script type="module" src="../../js/admin/overview.js"></script>
</body>
</html>
