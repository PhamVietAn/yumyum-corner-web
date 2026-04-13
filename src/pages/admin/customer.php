<!DOCTYPE html>
<html lang="en" data-theme="bumblebee">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YumYum Corner</title>
    <link rel="icon" type="image/jpeg" href="/logo.jpg" />
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
                    <li class="mb-2"><a href="/src/pages/admin/OverView.php" class="py-2 px-4 rounded-[10px] hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/tongquan.svg" alt="" title="Tong quan"> Tổng quan</a></li>
                    <li class="mb-2"><a href="/src/pages/admin/productManager.php" class=" py-2 px-4 rounded-[10px] hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/qlsanpham.svg" alt="" title="Quản lý sản phẩm"> Quản lý sản phẩm</a></li>
                    <li class="mb-2"><a href="/src/pages/admin/order.php" class=" py-2 px-4 rounded-[10px] hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/donhang.svg" alt="" title="Quan ly don hang"> Quản lý đơn hàng</a></li>
                    <li class="mb-2"><a href="/src/pages/admin/categories.php" class=" py-2 px-4 rounded-[10px] hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/category.svg" alt="" title="Quan ly danh muc"> Quản lý danh mục</a></li>
                    <li class="mb-2"><a href="/src/pages/admin/customer.php" class=" py-2 px-4 rounded-[10px] bg-orange-500 text-white hover:bg-orange-600 flex gap-3"><img src="../../assets/iconAdminDashboard/user-white.svg" alt="" title="Quan ly nguoi dung"> Quản lý khách hàng</a></li>
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
            <h1 class="text-3xl font-bold">Quản lý khách hàng</h1>
            <p class="text-gray-500 mt-2">Quản lý thông tin và hoạt động của khách hàng</p>
        </div>

        <div class="grid gap-4 md:grid-cols-3 mt-6">
            <div class="bg-white rounded-[14px] p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium">Tổng khách hàng</p>
                    <i class="fa-solid fa-user-check text-[#f97316]"></i>
                </div>
                <p class="text-2xl font-bold" id="totalCustomersValue">0</p>
                <p class="text-xs text-gray-500"><span id="activeCustomersValue">0</span> đang hoạt động</p>
            </div>

            <div class="bg-white rounded-[14px] p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium">Tổng doanh thu</p>
                    <i class="fa-solid fa-money-bill-wave text-[#f97316]"></i>
                </div>
                <p class="text-2xl font-bold" id="totalRevenueValue">0đ</p>
                <p class="text-xs text-gray-500">Từ tất cả khách hàng</p>
            </div>

            <div class="bg-white rounded-[14px] p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium">Đơn hàng trung bình</p>
                    <i class="fa-solid fa-box text-[#f97316]"></i>
                </div>
                <p class="text-2xl font-bold" id="avgOrdersValue">0</p>
                <p class="text-xs text-gray-500">Đơn/khách hàng</p>
            </div>
        </div>

        <div class="bg-white rounded-[14px] p-6 mt-6">
            <div class="relative">
                <label for="customerSearchInput" class="sr-only">Tìm kiếm khách hàng</label>
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input
                    id="customerSearchInput"
                    type="text"
                    placeholder="Tìm kiếm theo tên, email hoặc số điện thoại..."
                    class="w-full border border-gray-300 rounded-[10px] py-3 pl-12 pr-4 focus:outline-none focus:border-orange-500"
                >
            </div>
        </div>

        <div class="bg-white rounded-[14px] p-6 mt-6">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xl font-semibold">Danh sách khách hàng</p>
                <p class="text-gray-500">Tổng: <span id="totalFilteredCustomers">0</span> khách hàng</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th class="border-b border-gray-200 py-2 px-2">Khách hàng</th>
                            <th class="border-b border-gray-200 py-2 px-2 w-62.5">Liên hệ</th>
                            <th class="border-b border-gray-200 py-2 px-2 text-center">Số đơn hàng</th>
                            <th class="border-b border-gray-200 py-2 px-2 text-center">Tổng chi tiêu</th>
                            <th class="border-b border-gray-200 py-2 px-2 text-center">Ngày tham gia</th>
                            <th class="border-b border-gray-200 py-2 px-2 text-center">Trạng thái</th>
                            <th class="border-b border-gray-200 py-2 px-2 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="customersTableBody">
                        <tr>
                            <td colspan="7" class="border-b border-gray-200 py-10 text-center text-gray-500">Đang tải danh sách khách hàng...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between mt-5">
                <p class="text-gray-500 text-sm" id="customerPaginationSummary">Trang 1/1</p>
                <div class="flex items-center gap-2" id="customerPaginationControls"></div>
            </div>
        </div>

        <div id="customerDetailDialog" class="fixed inset-0 bg-black/45 z-50 hidden items-center justify-center px-4 py-6">
            <div class="bg-white rounded-[14px] w-full max-w-190 max-h-[90vh] overflow-y-auto p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-2xl font-bold">Chi tiết khách hàng</h3>
                        <p class="text-gray-500 mt-1">Thông tin chi tiết về khách hàng</p>
                    </div>
                    <button id="closeCustomerDialogBtn" type="button" class="text-gray-500 hover:text-gray-700 text-xl">×</button>
                </div>

                <div class="space-y-5 mt-5" id="customerDetailContent"></div>
            </div>
        </div>

        <div id="customerToast" class="fixed top-5 right-5 z-60 hidden px-4 py-2 rounded-[10px] text-white text-sm font-medium"></div>
    </div>

    <script src="https://kit.fontawesome.com/e9591eab39.js" crossorigin="anonymous"></script>
    <script type="module" src="../../js/admin/customer.js"></script>
</body>
</html>
