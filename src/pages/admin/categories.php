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
                    <li class="mb-2"><a href="/src/pages/admin/OverView.php" class="py-2 px-4 rounded-[10px]  hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/tongquan.svg" alt="" title="Tong quan"> Tổng quan</a></li>
                    <li class="mb-2"><a href="/src/pages/admin/productManager.php" class=" py-2 px-4 rounded-[10px] hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/qlsanpham.svg" alt="" title="Quản lý sản phẩm"> Quản lý sản phẩm</a></li>
                    <li class="mb-2"><a href="/src/pages/admin/order.php" class=" py-2 px-4 rounded-[10px] hover:bg-gray-100 flex gap-3"><img src="../../assets/iconAdminDashboard/donhang.svg" alt="" title="Quan ly don hang"> Quản lý đơn hàng</a></li>
                    <li class="mb-2"><a href="/src/pages/admin/categories.php" class=" py-2 px-4 rounded-[10px] bg-orange-500 text-white hover:bg-orange-600 flex gap-3"><img src="../../assets/iconAdminDashboard/category-white.svg" alt="" title="Quan ly danh muc"> Quản lý danh mục</a></li>
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
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">Quản lý danh mục</h1>
                <p class="text-gray-500 mt-2">Quản lý danh sách danh mục sản phẩm của cửa hàng</p>
            </div>
            <button id="openCategoryDialogBtn" class="bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-[10px]">Thêm danh mục</button>
        </div>

        <div class="bg-white rounded-[14px] p-6 mt-6">
            <div class="mb-5">
                <label for="categorySearchInput" class="sr-only">Tìm kiếm danh mục</label>
                <input
                    id="categorySearchInput"
                    type="text"
                    placeholder="Tìm kiếm danh mục theo tên..."
                    class="w-full border border-gray-300 rounded-[10px] py-3 px-4 focus:outline-none focus:border-orange-500"
                >
            </div>

            <div class="flex items-center justify-between mb-4">
                <p class="text-xl font-semibold">Danh sách danh mục</p>
                <p class="text-gray-500">Tổng: <span id="totalCategories">0</span> danh mục</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-225">
                    <thead>
                        <tr>
                            <th class="border-b border-gray-200 py-2">Hình ảnh</th>
                            <th class="border-b border-gray-200 py-2">Tên danh mục</th>
                            <th class="border-b border-gray-200 py-2 text-center">Số sản phẩm</th>
                            <th class="border-b border-gray-200 py-2 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTableBody">
                        <tr>
                            <td colspan="4" class="border-b border-gray-200 py-10 text-center text-gray-500">Đang tải danh sách danh mục...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between mt-5">
                <p class="text-gray-500 text-sm" id="categoryPaginationSummary">Trang 1/1</p>
                <div class="flex items-center gap-2" id="categoryPaginationControls"></div>
            </div>
        </div>
    </div>

    <div id="categoryDialog" class="fixed inset-0 bg-black/45 z-50 hidden items-center justify-center px-4 py-6">
        <div class="bg-white rounded-[14px] w-full max-w-155 max-h-[90vh] overflow-y-auto p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 id="categoryDialogTitle" class="text-2xl font-bold">Thêm danh mục mới</h3>
                    <p id="categoryDialogDesc" class="text-gray-500 mt-1">Điền thông tin danh mục mới</p>
                </div>
                <button id="closeCategoryDialogBtn" type="button" class="text-gray-500 hover:text-gray-700 text-xl">×</button>
            </div>

            <div class="grid gap-4 mt-5">
                <div>
                    <label for="categoryNameInput" class="block mb-2 font-medium">Tên danh mục *</label>
                    <input id="categoryNameInput" type="text" class="w-full border border-gray-300 rounded-[10px] py-2.5 px-3 focus:outline-none focus:border-orange-500" placeholder="Nhập tên danh mục">
                </div>

                <div>
                    <label for="categoryImageInput" class="block mb-2 font-medium">URL hình ảnh</label>
                    <input id="categoryImageInput" type="text" class="w-full border border-gray-300 rounded-[10px] py-2.5 px-3 focus:outline-none focus:border-orange-500" placeholder="https://example.com/image.jpg">
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button id="cancelCategoryDialogBtn" type="button" class="border border-gray-300 rounded-[10px] px-4 py-2 hover:bg-gray-50">Hủy</button>
                <button id="saveCategoryBtn" type="button" class="bg-orange-500 hover:bg-orange-600 text-white rounded-[10px] px-4 py-2">Lưu danh mục</button>
            </div>
        </div>
    </div>

    <div id="categoryToast" class="fixed top-5 right-5 z-60 hidden px-4 py-2 rounded-[10px] text-white text-sm font-medium"></div>

    <script type="module" src="../../js/admin/categories.js"></script>
</body>
</html>
