<!DOCTYPE html>
<html lang="en" data-theme="bumblebee">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                    <li class="mb-2"><a href="/src/pages/admin/productManager.php" class=" py-2 px-4 rounded-[10px] bg-orange-500 hover:bg-orange-600 text-white flex gap-3"><img src="../../assets/iconAdminDashboard/qlsp-white.svg" alt="" title="Quản lý sản phẩm"> Quản lý sản phẩm</a></li>
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
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">Quản lý sản phẩm</h1>
                <p class="text-gray-500 mt-2">Quản lý danh sách sản phẩm của cửa hàng</p>
            </div>
            <button id="openProductDialogBtn" class="bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-[10px]">Thêm sản phẩm</button>
        </div>

        <div class="bg-white rounded-[14px] p-6 mt-6">
            <div class="mb-5">
                <label for="productSearchInput" class="sr-only">Tìm kiếm sản phẩm</label>
                <input
                    id="productSearchInput"
                    type="text"
                    placeholder="Tìm kiếm sản phẩm theo tên hoặc danh mục..."
                    class="w-full border border-gray-300 rounded-[10px] py-3 px-4 focus:outline-none focus:border-orange-500"
                >
            </div>

            <div class="flex items-center justify-between mb-4">
                <p class="text-xl font-semibold">Danh sách sản phẩm</p>
                <p class="text-gray-500">Tổng: <span id="totalProducts">0</span> sản phẩm</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-245">
                    <thead>
                        <tr>
                            <th class="border-b border-gray-200 py-2">Hình ảnh</th>
                            <th class="border-b border-gray-200 py-2">Tên sản phẩm</th>
                            <th class="border-b border-gray-200 py-2">Danh mục</th>
                            <th class="border-b border-gray-200 py-2 text-right">Giá</th>
                            <th class="border-b border-gray-200 py-2 text-center">Đánh giá</th>
                            <th class="border-b border-gray-200 py-2 text-center">Trạng thái</th>
                            <th class="border-b border-gray-200 py-2 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <tr>
                            <td colspan="7" class="border-b border-gray-200 py-10 text-center text-gray-500">Đang tải danh sách sản phẩm...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between mt-5">
                <p class="text-gray-500 text-sm" id="paginationSummary">Trang 1/1</p>
                <div class="flex items-center gap-2" id="paginationControls"></div>
            </div>
        </div>
    </div>

    <div id="productDialog" class="fixed inset-0 bg-black/45 z-50 hidden items-center justify-center px-4 py-6">
        <div class="bg-white rounded-[14px] w-full max-w-190 max-h-[90vh] overflow-y-auto p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 id="productDialogTitle" class="text-2xl font-bold">Thêm sản phẩm mới</h3>
                    <p id="productDialogDesc" class="text-gray-500 mt-1">Điền thông tin sản phẩm mới</p>
                </div>
                <button id="closeProductDialogBtn" type="button" class="text-gray-500 hover:text-gray-700 text-xl">×</button>
            </div>

            <div class="grid gap-4 mt-5">
                <div>
                    <label for="productNameInput" class="block mb-2 font-medium">Tên sản phẩm *</label>
                    <input id="productNameInput" type="text" class="w-full border border-gray-300 rounded-[10px] py-2.5 px-3 focus:outline-none focus:border-orange-500" placeholder="Nhập tên sản phẩm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="productPriceInput" class="block mb-2 font-medium">Giá bán *</label>
                        <input id="productPriceInput" type="number" min="0" class="w-full border border-gray-300 rounded-[10px] py-2.5 px-3 focus:outline-none focus:border-orange-500" placeholder="0">
                    </div>
                    <div>
                        <label for="productOriginalPriceInput" class="block mb-2 font-medium">Giá gốc</label>
                        <input id="productOriginalPriceInput" type="number" min="0" class="w-full border border-gray-300 rounded-[10px] py-2.5 px-3 focus:outline-none focus:border-orange-500" placeholder="0">
                    </div>
                </div>

                <div>
                    <label for="productCategoryInput" class="block mb-2 font-medium">Danh mục *</label>
                    <select id="productCategoryInput" class="w-full border border-gray-300 rounded-[10px] py-2.5 px-3 focus:outline-none focus:border-orange-500"></select>
                </div>

                <div>
                    <label for="productImageInput" class="block mb-2 font-medium">URL hình ảnh</label>
                    <input id="productImageInput" type="text" class="w-full border border-gray-300 rounded-[10px] py-2.5 px-3 focus:outline-none focus:border-orange-500" placeholder="https://example.com/image.jpg">
                </div>

                <div>
                    <label for="productDescriptionInput" class="block mb-2 font-medium">Mô tả</label>
                    <textarea id="productDescriptionInput" rows="4" class="w-full border border-gray-300 rounded-[10px] py-2.5 px-3 focus:outline-none focus:border-orange-500" placeholder="Nhập mô tả sản phẩm"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="productRatingInput" class="block mb-2 font-medium">Đánh giá (1-5)</label>
                        <input id="productRatingInput" type="number" min="1" max="5" step="0.1" class="w-full border border-gray-300 rounded-[10px] py-2.5 px-3 focus:outline-none focus:border-orange-500" value="5">
                    </div>
                    <div>
                        <label for="productReviewCountInput" class="block mb-2 font-medium">Số lượt đánh giá</label>
                        <input id="productReviewCountInput" type="number" min="0" class="w-full border border-gray-300 rounded-[10px] py-2.5 px-3 focus:outline-none focus:border-orange-500" value="0">
                    </div>
                </div>

                <label class="inline-flex items-center gap-2 mt-1">
                    <input id="productInStockInput" type="checkbox" class="h-4 w-4" checked>
                    <span>Còn hàng</span>
                </label>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button id="cancelProductDialogBtn" type="button" class="border border-gray-300 rounded-[10px] px-4 py-2 hover:bg-gray-50">Hủy</button>
                <button id="saveProductBtn" type="button" class="bg-orange-500 hover:bg-orange-600 text-white rounded-[10px] px-4 py-2">Lưu sản phẩm</button>
            </div>
        </div>
    </div>

    <div id="productToast" class="fixed top-5 right-5 z-60 hidden px-4 py-2 rounded-[10px] text-white text-sm font-medium"></div>

    <script type="module" src="../../js/admin/productManager.js"></script>
</body>
</html>
