ensureFreshDataAfterNavigation();

const ITEMS_PER_PAGE = 10;
const FALLBACK_IMAGE = "https://via.placeholder.com/80";

let categoriesState = [];
let searchTerm = "";
let currentPage = 1;
let editingCategoryId = null;
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

function getFilteredCategories() {
  const keyword = searchTerm.trim().toLowerCase();
  if (!keyword) {
    return categoriesState;
  }

  return categoriesState.filter((category) =>
    String(category.name || "").toLowerCase().includes(keyword),
  );
}

function ensureFreshDataAfterNavigation() {
  if (window.__forceReloadOnNavigationAttached) {
    return;
  }

  window.__forceReloadOnNavigationAttached = true;

  window.addEventListener("pageshow", (event) => {
    const navigationEntries =
      typeof performance.getEntriesByType === "function"
        ? performance.getEntriesByType("navigation")
        : [];
    const navigationType = navigationEntries?.[0]?.type;

    if (event.persisted || navigationType === "back_forward") {
      window.location.reload();
    }
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
  const toast = document.getElementById("categoryToast");
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

function openDialog(category = null) {
  const dialog = document.getElementById("categoryDialog");
  const title = document.getElementById("categoryDialogTitle");
  const desc = document.getElementById("categoryDialogDesc");
  const nameInput = document.getElementById("categoryNameInput");
  const imageInput = document.getElementById("categoryImageInput");

  if (!dialog || !title || !desc || !nameInput || !imageInput) return;

  if (category) {
    editingCategoryId = Number(category.id);
    title.innerText = "Chỉnh sửa danh mục";
    desc.innerText = "Cập nhật thông tin danh mục";
    nameInput.value = category.name || "";
    imageInput.value = category.image || "";
  } else {
    editingCategoryId = null;
    title.innerText = "Thêm danh mục mới";
    desc.innerText = "Điền thông tin danh mục mới";
    nameInput.value = "";
    imageInput.value = "";
  }

  dialog.classList.remove("hidden");
  dialog.classList.add("flex");
}

function closeDialog() {
  const dialog = document.getElementById("categoryDialog");
  if (!dialog) return;

  dialog.classList.add("hidden");
  dialog.classList.remove("flex");
}

function getFormData() {
  const name = String(document.getElementById("categoryNameInput")?.value || "").trim();
  const image = String(document.getElementById("categoryImageInput")?.value || "").trim();

  return {
    name,
    image: image || FALLBACK_IMAGE,
  };
}

async function handleSaveCategory() {
  const formData = getFormData();

  if (!formData.name) {
    showToast("Vui lòng nhập tên danh mục", "error");
    return;
  }

  try {
    if (editingCategoryId) {
      await fetchJson("/backend/api/categories.php", {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify({
          id: editingCategoryId,
          ...formData,
        }),
      });
      showToast("Cập nhật danh mục thành công", "success");
    } else {
      await fetchJson("/backend/api/categories.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify(formData),
      });
      showToast("Thêm danh mục thành công", "success");
    }

    closeDialog();
    await loadCategories();
    renderCategoryManager();
  } catch (error) {
    showToast(String(error?.message || "Không thể lưu danh mục"), "error");
  }
}

async function handleDeleteCategory(categoryId) {
  const target = categoriesState.find((item) => Number(item.id) === Number(categoryId));
  if (!target) return;

  const confirmed = window.confirm(`Bạn có chắc chắn muốn xóa danh mục "${target.name}"?`);
  if (!confirmed) return;

  try {
    await fetchJson(`/backend/api/categories.php?id=${Number(categoryId)}`, {
      method: "DELETE",
      headers: { Accept: "application/json" },
    });

    await loadCategories();
    renderCategoryManager();
    showToast("Xóa danh mục thành công", "success");
  } catch (error) {
    showToast(String(error?.message || "Không thể xóa danh mục"), "error");
  }
}

function renderTableRows(items) {
  const tbody = document.getElementById("categoryTableBody");
  if (!tbody) return;

  if (items.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="4" class="border-b border-gray-200 py-10 text-center text-gray-500">Không có danh mục nào</td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = items
    .map(
      (category) => `
        <tr>
          <td class="border-b border-gray-200 py-3">
            <img src="${escapeHtml(category.image || FALLBACK_IMAGE)}" alt="${escapeHtml(category.name)}" class="h-12 w-12 rounded-lg object-cover" onerror="this.src='${FALLBACK_IMAGE}'">
          </td>
          <td class="border-b border-gray-200 py-3 font-medium">${escapeHtml(category.name)}</td>
          <td class="border-b border-gray-200 py-3 text-center">${Number(category.productCount || 0)}</td>
          <td class="border-b border-gray-200 py-3 text-right">
            <button type="button" class="text-blue-500 hover:underline mr-4" data-action="edit" data-id="${escapeHtml(category.id)}">Sửa</button>
            <button type="button" class="text-red-500 hover:underline" data-action="delete" data-id="${escapeHtml(category.id)}">Xóa</button>
          </td>
        </tr>
      `,
    )
    .join("");
}

function renderPagination(totalItems) {
  const summary = document.getElementById("categoryPaginationSummary");
  const controls = document.getElementById("categoryPaginationControls");
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
        class="category-page-btn min-w-9 h-9 px-3 rounded-md border text-sm ${activeClass}"
        data-page="${page}"
      >
        ${page}
      </button>
    `;
  }).join("");

  controls.innerHTML = `
    <button type="button" class="category-page-btn h-9 px-3 rounded-md border border-gray-300 text-sm ${
      prevDisabled ? "opacity-40 cursor-not-allowed" : "hover:bg-gray-50"
    }" data-page="prev" ${prevDisabled}>Truoc</button>
    ${pageButtons}
    <button type="button" class="category-page-btn h-9 px-3 rounded-md border border-gray-300 text-sm ${
      nextDisabled ? "opacity-40 cursor-not-allowed" : "hover:bg-gray-50"
    }" data-page="next" ${nextDisabled}>Sau</button>
  `;

  controls.querySelectorAll(".category-page-btn").forEach((button) => {
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

      renderCategoryManager();
    });
  });
}

function renderCategoryManager() {
  const total = document.getElementById("totalCategories");
  const filtered = getFilteredCategories();

  if (total) {
    total.innerText = String(filtered.length);
  }

  const totalPages = getPageCount(filtered.length);
  if (currentPage > totalPages) {
    currentPage = totalPages;
  }

  const pageItems = getPageItems(filtered, currentPage);
  renderTableRows(pageItems);
  renderPagination(filtered.length);
}

function bindEvents() {
  document.getElementById("categorySearchInput")?.addEventListener("input", (event) => {
    searchTerm = String(event.target.value || "");
    currentPage = 1;
    renderCategoryManager();
  });

  document.getElementById("openCategoryDialogBtn")?.addEventListener("click", () => {
    openDialog();
  });

  document.getElementById("closeCategoryDialogBtn")?.addEventListener("click", closeDialog);
  document.getElementById("cancelCategoryDialogBtn")?.addEventListener("click", closeDialog);
  document.getElementById("saveCategoryBtn")?.addEventListener("click", handleSaveCategory);

  document.getElementById("categoryDialog")?.addEventListener("click", (event) => {
    if (event.target.id === "categoryDialog") {
      closeDialog();
    }
  });

  document.getElementById("categoryTableBody")?.addEventListener("click", (event) => {
    const button = event.target.closest("button[data-action]");
    if (!button) return;

    const action = button.dataset.action;
    const categoryId = Number(button.dataset.id || 0);
    if (!action || !categoryId) return;

    if (action === "edit") {
      const target = categoriesState.find((item) => Number(item.id) === categoryId);
      if (target) {
        openDialog(target);
      }
      return;
    }

    if (action === "delete") {
      handleDeleteCategory(categoryId);
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeDialog();
    }
  });
}

async function loadCategories() {
  const payload = await fetchJson("/backend/api/categories.php", {
    method: "GET",
    headers: { Accept: "application/json" },
  });

  categoriesState = Array.isArray(payload?.data) ? payload.data : [];
}

document.addEventListener("DOMContentLoaded", async () => {
  bindEvents();

  try {
    await loadCategories();
    renderCategoryManager();
  } catch (error) {
    if (error?.status === 401 || error?.status === 403) {
      window.location.href = "/src/pages/auth/login.php";
      return;
    }

    showToast(String(error?.message || "Không tải được dữ liệu danh mục"), "error");
  }
});
