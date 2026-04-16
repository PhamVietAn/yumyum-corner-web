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
                    <li class="mb-2"><a href="../admin/OverView.php" class="py-2 px-4 rounded-[10px]  hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/tongquan.svg" alt="" title="Tong quan"> Tổng quan</a></li>
                    <li class="mb-2"><a href="../admin/productManager.php" class=" py-2 px-4 rounded-[10px] hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/qlsanpham.svg" alt="" title="Quản lý sản phẩm"> Quản lý sản phẩm</a></li>
                    <li class="mb-2"><a href="../admin/order.php" class="py-2 px-4 rounded-[10px] bg-orange-500 text-white hover:bg-orange-600 flex gap-3"><img src="../../assets/iconAdminDashboard/donhang-white.svg" alt="" title="Quan ly don hang"> Quản lý đơn hàng</a></li>
                    <li class="mb-2"><a href="../admin/categories.php" class=" py-2 px-4 rounded-[10px] hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/category.svg" alt="" title="Quan ly danh muc"> Quản lý danh mục</a></li>
                    <li class="mb-2"><a href="../admin/customer.php" class=" py-2 px-4 rounded-[10px] hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/user.svg" alt="" title="Quan ly nguoi dung"> Quản lý khách hàng</a></li>
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
        <div>
            <h1 class="text-3xl font-bold">Quản lý đơn hàng</h1>
            <p class="text-gray-500 mt-2">Theo dõi và quản lý tất cả đơn hàng</p>
        </div>

        <div class="bg-white rounded-[14px] p-6 mt-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <label for="orderSearchInput" class="sr-only">Tìm kiếm đơn hàng</label>
                    <input
                        id="orderSearchInput"
                        type="text"
                        placeholder="Tìm kiếm theo mã đơn, tên khách hàng, email..."
                        class="w-full border border-gray-300 rounded-[10px] py-3 px-4 focus:outline-none focus:border-orange-500"
                    >
                </div>

                <div class="flex items-center gap-2">
                    <label for="orderStatusFilter" class="text-sm text-gray-500">Trạng thái</label>
                    <select id="orderStatusFilter" class="select border border-gray-300 rounded-[10px] p-3 focus:outline-none" style="width: 180px;">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="pending">Chờ xử lý</option>
                        <option value="processing">Đang xử lý</option>
                        <option value="shipped">Đã gửi</option>
                        <option value="delivered">Đã giao</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[14px] p-6 mt-6">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xl font-semibold">Danh sách đơn hàng</p>
                <p class="text-gray-500">Tổng: <span id="totalOrders">0</span> đơn hàng</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-245">
                    <thead>
                        <tr>
                            <th class="border-b border-gray-200 py-2">Mã đơn hàng</th>
                            <th class="border-b border-gray-200 py-2">Khách hàng</th>
                            <th class="border-b border-gray-200 py-2">Ngày đặt</th>
                            <th class="border-b border-gray-200 py-2 text-right">Tổng tiền</th>
                            <th class="border-b border-gray-200 py-2 text-center">Trạng thái</th>
                            <th class="border-b border-gray-200 py-2 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        <tr>
                            <td colspan="6" class="border-b border-gray-200 py-10 text-center text-gray-500">Đang tải danh sách đơn hàng...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between mt-5">
                <p class="text-gray-500 text-sm" id="orderPaginationSummary">Trang 1/1</p>
                <div class="flex items-center gap-2" id="orderPaginationControls"></div>
            </div>
        </div>

        <div id="orderDetailDialog" class="fixed inset-0 bg-black/45 z-50 hidden items-center justify-center px-4 py-6">
            <div class="bg-white rounded-[14px] w-full max-w-215 max-h-[90vh] overflow-y-auto p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-2xl font-bold">Chi tiết đơn hàng</h3>
                        <p class="text-gray-500 mt-1">Mã đơn hàng: <span id="orderDialogNumber">-</span></p>
                    </div>
                    <button id="closeOrderDialogBtn" type="button" class="text-gray-500 hover:text-gray-700 text-xl">×</button>
                </div>

                <div class="space-y-5 mt-5" id="orderDetailContent"></div>
            </div>
        </div>

        <div id="orderToast" class="fixed top-5 right-5 z-60 hidden px-4 py-2 rounded-[10px] text-white text-sm font-medium"></div>
    </div>

    <script type="module" src="../../js/admin/order.js"></script>
</body>
</html>
