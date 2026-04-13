const ITEMS_PER_PAGE = 10;
const FALLBACK_IMAGE = "https://via.placeholder.com/80";

let productsState = [];
let categoriesState = [];
let searchTerm = "";
let currentPage = 1;
let editingProductId = null;
let toastTimer = null;

async function fetchJson(url, options = {}) {
  const response = await fetch(url, options);
  const payload = await response.json().catch(() => ({}));

  if (!response.ok || payload?.success === false) {
    const error = new Error(payload?.message || "Yêu cầu thất bại");
    error.status = response.status;
    throw error;
  }

  return payload;
}

function escapeHtml(value) {
  return String(value ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function formatPrice(value) {
  return `${Number(value || 0).toLocaleString("vi-VN")}đ`;
}

function getFilteredProducts() {
  const keyword = searchTerm.trim().toLowerCase();
  if (!keyword) {
    return productsState;
  }

  return productsState.filter((product) => {
    const name = String(product.name || "").toLowerCase();
    const category = String(product.category || "").toLowerCase();
    return name.includes(keyword) || category.includes(keyword);
  });
}

function getPageCount(totalItems) {
  return Math.max(1, Math.ceil(totalItems / ITEMS_PER_PAGE));
}

function getPageItems(list, page) {
  const start = (page - 1) * ITEMS_PER_PAGE;
  return list.slice(start, start + ITEMS_PER_PAGE);
}

function showToast(message, type = "success") {
  const toast = document.getElementById("productToast");
  if (!toast) return;

  const colorClass =
    type === "error"
      ? "bg-red-500"
      : type === "warning"
        ? "bg-amber-500"
        : "bg-emerald-500";

  toast.classList.remove("hidden", "bg-red-500", "bg-amber-500", "bg-emerald-500");
  toast.classList.add(colorClass);
  toast.innerText = message;

  if (toastTimer) clearTimeout(toastTimer);

  toastTimer = setTimeout(() => {
    toast.classList.add("hidden");
  }, 1800);
}

function populateCategorySelect() {
  const select = document.getElementById("productCategoryInput");
  if (!select) return;

  const options = categoriesState.length > 0 ? categoriesState : [{ id: 1, name: "Khac" }];
  select.innerHTML = options
    .map((category) => `<option value="${escapeHtml(category.id)}">${escapeHtml(category.name)}</option>`)
    .join("");
}

function openDialog(product = null) {
  const dialog = document.getElementById("productDialog");
  const title = document.getElementById("productDialogTitle");
  const desc = document.getElementById("productDialogDesc");

  if (!dialog || !title || !desc) return;

  const nameInput = document.getElementById("productNameInput");
  const priceInput = document.getElementById("productPriceInput");
  const originalPriceInput = document.getElementById("productOriginalPriceInput");
  const categoryInput = document.getElementById("productCategoryInput");
  const imageInput = document.getElementById("productImageInput");
  const descInput = document.getElementById("productDescriptionInput");
  const ratingInput = document.getElementById("productRatingInput");
  const reviewInput = document.getElementById("productReviewCountInput");
  const stockInput = document.getElementById("productInStockInput");

  const defaultCategoryId = categoriesState[0]?.id || 1;

  if (product) {
    editingProductId = Number(product.id);
    title.innerText = "Chỉnh sửa sản phẩm";
    desc.innerText = "Cập nhật thông tin sản phẩm";
    if (nameInput) nameInput.value = product.name || "";
    if (priceInput) priceInput.value = String(Number(product.price || 0));
    if (originalPriceInput) {
      originalPriceInput.value = product.originalPrice ? String(Number(product.originalPrice)) : "";
    }
    if (categoryInput) categoryInput.value = String(product.categoryId || defaultCategoryId);
    if (imageInput) imageInput.value = product.image || "";
    if (descInput) descInput.value = product.description || "";
    if (ratingInput) ratingInput.value = String(Number(product.rating || 5));
    if (reviewInput) reviewInput.value = String(Number(product.reviewCount || 0));
    if (stockInput) stockInput.checked = Number(product.stock || 0) > 0;
  } else {
    editingProductId = null;
    title.innerText = "Thêm sản phẩm mới";
    desc.innerText = "Điền thông tin sản phẩm mới";
    if (nameInput) nameInput.value = "";
    if (priceInput) priceInput.value = "";
    if (originalPriceInput) originalPriceInput.value = "";
    if (categoryInput) categoryInput.value = String(defaultCategoryId);
    if (imageInput) imageInput.value = "";
    if (descInput) descInput.value = "";
    if (ratingInput) ratingInput.value = "5";
    if (reviewInput) reviewInput.value = "0";
    if (stockInput) stockInput.checked = true;
  }

  dialog.classList.remove("hidden");
  dialog.classList.add("flex");
}

function closeDialog() {
  const dialog = document.getElementById("productDialog");
  if (!dialog) return;

  dialog.classList.add("hidden");
  dialog.classList.remove("flex");
}

function getFormData() {
  const name = String(document.getElementById("productNameInput")?.value || "").trim();
  const price = Number(document.getElementById("productPriceInput")?.value || 0);
  const originalPrice = Number(document.getElementById("productOriginalPriceInput")?.value || 0);
  const categoryId = Number(document.getElementById("productCategoryInput")?.value || 0);
  const image = String(document.getElementById("productImageInput")?.value || "").trim();
  const description = String(document.getElementById("productDescriptionInput")?.value || "").trim();
  const rating = Number(document.getElementById("productRatingInput")?.value || 5);
  const reviewCount = Number(document.getElementById("productReviewCountInput")?.value || 0);
  const inStock = Boolean(document.getElementById("productInStockInput")?.checked);

  return {
    name,
    price,
    originalPrice: originalPrice > 0 ? originalPrice : null,
    categoryId,
    image: image || FALLBACK_IMAGE,
    description,
    rating: Math.min(5, Math.max(1, Number.isFinite(rating) ? rating : 5)),
    reviewCount: Math.max(0, Number.isFinite(reviewCount) ? reviewCount : 0),
    inStock,
    stock: inStock ? 100 : 0,
  };
}

async function handleSaveProduct() {
  const formData = getFormData();

  if (!formData.name || formData.categoryId <= 0 || formData.price <= 0) {
    showToast("Vui lòng điền đầy đủ thông tin bắt buộc", "error");
    return;
  }

  try {
    if (editingProductId) {
      await fetchJson("/backend/api/products.php", {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify({
          id: editingProductId,
          ...formData,
        }),
      });
      showToast("Cập nhật sản phẩm thành công", "success");
    } else {
      await fetchJson("/backend/api/products.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify(formData),
      });
      showToast("Thêm sản phẩm thành công", "success");
    }

    closeDialog();
    await loadData();
    renderProductManager();
  } catch (error) {
    showToast(String(error?.message || "Không thể lưu sản phẩm"), "error");
  }
}

async function handleDeleteProduct(productId) {
  const target = productsState.find((product) => Number(product.id) === Number(productId));
  if (!target) return;

  const confirmed = window.confirm(`Bạn có chắc chắn muốn xóa "${target.name}"?`);
  if (!confirmed) return;

  try {
    await fetchJson(`/backend/api/products.php?id=${Number(productId)}`, {
      method: "DELETE",
      headers: { Accept: "application/json" },
    });

    await loadData();
    renderProductManager();
    showToast("Xóa sản phẩm thành công", "success");
  } catch (error) {
    showToast(String(error?.message || "Không thể xóa sản phẩm"), "error");
  }
}

function renderTableRows(items) {
  const tbody = document.getElementById("productTableBody");
  if (!tbody) return;

  if (items.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="7" class="border-b border-gray-200 py-10 text-center text-gray-500">Không có sản phẩm nào</td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = items
    .map(
      (item) => `
        <tr>
          <td class="border-b border-gray-200 py-3">
            <img src="${escapeHtml(item.image || FALLBACK_IMAGE)}" alt="${escapeHtml(item.name)}" class="h-12 w-12 rounded-lg object-cover" onerror="this.src='${FALLBACK_IMAGE}'">
          </td>
          <td class="border-b border-gray-200 py-3 font-medium">${escapeHtml(item.name)}</td>
          <td class="border-b border-gray-200 py-3">${escapeHtml(item.category || "-")}</td>
          <td class="border-b border-gray-200 py-3 text-right">
            <div class="text-[#ff6b35] font-medium">${formatPrice(item.price)}</div>
            ${item.originalPrice ? `<div class="text-xs text-gray-400 line-through">${formatPrice(item.originalPrice)}</div>` : ""}
          </td>
          <td class="border-b border-gray-200 py-3 text-center">
            <div class="font-medium">⭐ ${Number(item.rating || 0).toFixed(1)}</div>
            <div class="text-xs text-gray-400">(${Number(item.reviewCount || 0)})</div>
          </td>
          <td class="border-b border-gray-200 py-3 text-center">
            <span class="${Number(item.stock || 0) > 0 ? "bg-green-100 text-green-800" : "bg-red-100 text-red-700"} py-1 px-3 rounded-full text-sm">
              ${Number(item.stock || 0) > 0 ? "Còn hàng" : "Hết hàng"}
            </span>
          </td>
          <td class="border-b border-gray-200 py-3 text-right">
            <button type="button" class="hover:underline mr-4" data-action="edit" data-id="${escapeHtml(item.id)}"><img src="../../assets/iconAdminDashboard/editIcon.svg" alt="Sửa" class="w-5 h-5"></button>
            <button type="button" class="hover:underline" data-action="delete" data-id="${escapeHtml(item.id)}"><img src="../../assets/iconAdminDashboard/deleteIcon.svg" alt="Xóa" class="w-5 h-5"></button>
          </td>
        </tr>
      `,
    )
    .join("");
}

function renderPagination(totalItems) {
  const summary = document.getElementById("paginationSummary");
  const controls = document.getElementById("paginationControls");
  if (!summary || !controls) return;

  const totalPages = getPageCount(totalItems);

  if (currentPage > totalPages) {
    currentPage = totalPages;
  }

  summary.innerText = `Trang ${currentPage}/${totalPages}`;

  const prevDisabled = currentPage === 1 ? "disabled" : "";
  const nextDisabled = currentPage === totalPages ? "disabled" : "";

  const pageButtons = Array.from({ length: totalPages }, (_, index) => {
    const page = index + 1;
    const activeClass =
      page === currentPage
        ? "bg-orange-500 text-white border-orange-500"
        : "bg-white text-gray-700 border-gray-300 hover:bg-gray-50";

    return `
      <button
        type="button"
        class="page-btn min-w-9 h-9 px-3 rounded-md border text-sm ${activeClass}"
        data-page="${page}"
      >
        ${page}
      </button>
    `;
  }).join("");

  controls.innerHTML = `
    <button type="button" class="page-btn h-9 px-3 rounded-md border border-gray-300 text-sm ${
      prevDisabled ? "opacity-40 cursor-not-allowed" : "hover:bg-gray-50"
    }" data-page="prev" ${prevDisabled}>Truoc</button>
    ${pageButtons}
    <button type="button" class="page-btn h-9 px-3 rounded-md border border-gray-300 text-sm ${
      nextDisabled ? "opacity-40 cursor-not-allowed" : "hover:bg-gray-50"
    }" data-page="next" ${nextDisabled}>Sau</button>
  `;

  controls.querySelectorAll(".page-btn").forEach((button) => {
    button.addEventListener("click", () => {
      const action = button.dataset.page;
      if (!action) return;

      if (action === "prev" && currentPage > 1) {
        currentPage -= 1;
      } else if (action === "next" && currentPage < totalPages) {
        currentPage += 1;
      } else if (action !== "prev" && action !== "next") {
        currentPage = Number(action);
      }

      renderProductManager();
    });
  });
}

function renderProductManager() {
  const totalProducts = document.getElementById("totalProducts");
  const filteredProducts = getFilteredProducts();

  if (totalProducts) {
    totalProducts.innerText = String(filteredProducts.length);
  }

  const totalPages = getPageCount(filteredProducts.length);
  if (currentPage > totalPages) {
    currentPage = totalPages;
  }

  const pageItems = getPageItems(filteredProducts, currentPage);
  renderTableRows(pageItems);
  renderPagination(filteredProducts.length);
}

function bindEvents() {
  document.getElementById("productSearchInput")?.addEventListener("input", (event) => {
    searchTerm = String(event.target.value || "");
    currentPage = 1;
    renderProductManager();
  });

  document.getElementById("openProductDialogBtn")?.addEventListener("click", () => {
    openDialog();
  });

  document.getElementById("closeProductDialogBtn")?.addEventListener("click", closeDialog);
  document.getElementById("cancelProductDialogBtn")?.addEventListener("click", closeDialog);
  document.getElementById("saveProductBtn")?.addEventListener("click", handleSaveProduct);

  document.getElementById("productDialog")?.addEventListener("click", (event) => {
    if (event.target.id === "productDialog") {
      closeDialog();
    }
  });

  document.getElementById("productTableBody")?.addEventListener("click", (event) => {
    const button = event.target.closest("button[data-action]");
    if (!button) return;

    const action = button.dataset.action;
    const productId = Number(button.dataset.id || 0);
    if (!action || !productId) return;

    if (action === "edit") {
      const target = productsState.find((product) => Number(product.id) === productId);
      if (target) {
        openDialog(target);
      }
      return;
    }

    if (action === "delete") {
      handleDeleteProduct(productId);
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeDialog();
    }
  });
}

async function loadData() {
  const [productsPayload, categoriesPayload] = await Promise.all([
    fetchJson("/backend/api/products.php", { method: "GET", headers: { Accept: "application/json" } }),
    fetchJson("/backend/api/categories.php", { method: "GET", headers: { Accept: "application/json" } }),
  ]);

  productsState = Array.isArray(productsPayload?.data) ? productsPayload.data : [];
  categoriesState = Array.isArray(categoriesPayload?.data) ? categoriesPayload.data : [];
  populateCategorySelect();
}

document.addEventListener("DOMContentLoaded", async () => {
  bindEvents();

  try {
    await loadData();
    renderProductManager();
  } catch (error) {
    if (error?.status === 401 || error?.status === 403) {
      window.location.href = "/src/pages/auth/login.php";
      return;
    }

    showToast(String(error?.message || "Không tải được dữ liệu"), "error");
  }
});
