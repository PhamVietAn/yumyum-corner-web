ensureFreshDataAfterNavigation();

const ITEMS_PER_PAGE = 10;

const STATUS_TEXT = {
  active: "Hoạt động",
  inactive: "Không hoạt động",
};

let customersState = [];
let searchTerm = "";
let currentPage = 1;
let selectedCustomerId = null;
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

function formatDate(dateString) {
  const date = new Date(dateString || Date.now());
  if (Number.isNaN(date.getTime())) return "-";

  return date.toLocaleDateString("vi-VN", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  });
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

function getInitial(name) {
  return String(name || "K").trim().charAt(0).toUpperCase() || "K";
}

function showToast(message, type = "success") {
  const toast = document.getElementById("customerToast");
  if (!toast) return;

  const className =
    type === "error"
      ? "bg-red-500"
      : type === "warning"
        ? "bg-amber-500"
        : "bg-emerald-500";

  toast.classList.remove("hidden", "bg-red-500", "bg-amber-500", "bg-emerald-500");
  toast.classList.add(className);
  toast.innerText = message;

  if (toastTimer) clearTimeout(toastTimer);

  toastTimer = setTimeout(() => {
    toast.classList.add("hidden");
  }, 1800);
}

function getFilteredCustomers() {
  const keyword = searchTerm.trim().toLowerCase();

  if (!keyword) {
    return customersState;
  }

  return customersState.filter((customer) =>
    String(customer.name || "").toLowerCase().includes(keyword) ||
    String(customer.email || "").toLowerCase().includes(keyword) ||
    String(customer.phone || "").includes(searchTerm.trim()),
  );
}

function getPageCount(totalItems) {
  return Math.max(1, Math.ceil(totalItems / ITEMS_PER_PAGE));
}

function getPageItems(list, page) {
  const start = (page - 1) * ITEMS_PER_PAGE;
  return list.slice(start, start + ITEMS_PER_PAGE);
}

function renderPagination(totalItems) {
  const summary = document.getElementById("customerPaginationSummary");
  const controls = document.getElementById("customerPaginationControls");
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
        class="customer-page-btn min-w-9 h-9 px-3 rounded-md border text-sm ${activeClass}"
        data-page="${page}"
      >
        ${page}
      </button>
    `;
  }).join("");

  controls.innerHTML = `
    <button type="button" class="customer-page-btn h-9 px-3 rounded-md border border-gray-300 text-sm ${
      prevDisabled ? "opacity-40 cursor-not-allowed" : "hover:bg-gray-50"
    }" data-page="prev" ${prevDisabled}>Truoc</button>
    ${pageButtons}
    <button type="button" class="customer-page-btn h-9 px-3 rounded-md border border-gray-300 text-sm ${
      nextDisabled ? "opacity-40 cursor-not-allowed" : "hover:bg-gray-50"
    }" data-page="next" ${nextDisabled}>Sau</button>
  `;

  controls.querySelectorAll(".customer-page-btn").forEach((button) => {
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

      renderCustomersTable();
    });
  });
}

function renderStats() {
  const totalCustomers = customersState.length;
  const totalActive = customersState.filter((customer) => customer.status === "active").length;
  const totalRevenue = customersState.reduce((sum, customer) => sum + Number(customer.totalSpent || 0), 0);
  const totalOrders = customersState.reduce((sum, customer) => sum + Number(customer.totalOrders || 0), 0);

  const avgOrders = totalCustomers > 0 ? Math.round(totalOrders / totalCustomers) : 0;

  document.getElementById("totalCustomersValue").innerText = String(totalCustomers);
  document.getElementById("activeCustomersValue").innerText = String(totalActive);
  document.getElementById("totalRevenueValue").innerText = formatPrice(totalRevenue);
  document.getElementById("avgOrdersValue").innerText = String(avgOrders);
}

function renderCustomerRows(customers) {
  const tbody = document.getElementById("customersTableBody");
  if (!tbody) return;

  if (customers.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="7" class="border-b border-gray-200 py-10 text-center text-gray-500">Không có khách hàng phù hợp</td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = customers
    .map(
      (customer) => `
        <tr>
          <td class="border-b border-gray-200 py-3 px-2">
            <div class="flex items-center gap-3">
              <div class="font-medium">${escapeHtml(customer.name)}</div>
            </div>
          </td>
          <td class="border-b border-gray-200 py-3 px-2 w-67.5">
            <div class="text-[13px] leading-5">
              <div class="truncate">${escapeHtml(customer.email)}</div>
              <div class="text-gray-500 truncate">${escapeHtml(customer.phone || "-")}</div>
            </div>
          </td>
          <td class="border-b border-gray-200 py-3 px-2 text-center font-medium">${Number(customer.totalOrders || 0)}</td>
          <td class="border-b border-gray-200 py-3 px-2 text-center font-medium text-[#FF6B35] whitespace-nowrap text-[13px]">${formatPrice(customer.totalSpent)}</td>
          <td class="border-b border-gray-200 py-3 px-2 text-center whitespace-nowrap text-[13px]">${formatDate(customer.joinDate)}</td>
          <td class="border-b border-gray-200 py-3 px-2 text-center">
            <span class="py-1 px-3 rounded-full text-xs font-medium ${
              customer.status === "active"
                ? "bg-green-100 text-green-700"
                : "bg-gray-100 text-gray-700"
            }">
              ${customer.status === "active" ? "Hoạt động" : "Không hoạt động"}
            </span>
          </td>
          <td class="border-b border-gray-200 py-3 px-2 text-right">
            <div class="flex justify-end gap-2">
              <button
                type="button"
                class="h-9 w-9 rounded-md border border-gray-300 hover:bg-gray-50"
                title="Xem chi tiết"
                data-action="view"
                data-id="${escapeHtml(customer.id)}"
              >
                <i class="fa-regular fa-eye"></i>
              </button>
              <button
                type="button"
                class="h-9 w-9 rounded-md border ${
                  customer.status === "active"
                    ? "border-red-200 text-red-600 hover:bg-red-50"
                    : "border-green-200 text-green-600 hover:bg-green-50"
                }"
                title="${customer.status === "active" ? "Vô hiệu hóa" : "Kích hoạt"}"
                data-action="toggle-status"
                data-id="${escapeHtml(customer.id)}"
              >
                <i class="fa-solid ${customer.status === "active" ? "fa-user-xmark" : "fa-user-check"}"></i>
              </button>
            </div>
          </td>
        </tr>
      `,
    )
    .join("");
}

function renderCustomersTable() {
  const filtered = getFilteredCustomers();
  const total = document.getElementById("totalFilteredCustomers");
  if (total) {
    total.innerText = String(filtered.length);
  }

  const totalPages = getPageCount(filtered.length);
  if (currentPage > totalPages) {
    currentPage = totalPages;
  }

  const pageCustomers = getPageItems(filtered, currentPage);
  renderCustomerRows(pageCustomers);
  renderPagination(filtered.length);
}

function renderDetailDialog(customer) {
  const content = document.getElementById("customerDetailContent");
  if (!content) return;

  const avgSpend = customer.totalOrders > 0
    ? Math.round(Number(customer.totalSpent || 0) / Number(customer.totalOrders || 1))
    : 0;

  content.innerHTML = `
    <div class="flex items-center gap-4">
      <div class="flex h-16 w-16 items-center justify-center rounded-full bg-linear-to-br from-[#FF6B35] to-[#FFC15E] text-2xl font-medium text-white">
        ${escapeHtml(getInitial(customer.name))}
      </div>
      <div class="flex-1">
        <h4 class="text-xl font-bold">${escapeHtml(customer.name)}</h4>
        <span class="inline-flex mt-2 py-1 px-3 rounded-full text-xs font-medium ${
          customer.status === "active"
            ? "bg-green-100 text-green-700"
            : "bg-gray-100 text-gray-700"
        }">
          ${STATUS_TEXT[customer.status]}
        </span>
      </div>
    </div>

    <section class="grid gap-4 rounded-lg border border-gray-200 p-4">
      <h4 class="font-semibold">Thông tin liên hệ</h4>
      <div class="grid gap-3 text-sm">
        <div class="flex justify-between"><span class="text-gray-500">Email:</span><span class="font-medium text-right">${escapeHtml(customer.email)}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Số điện thoại:</span><span class="font-medium text-right">${escapeHtml(customer.phone || "-")}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Địa chỉ:</span><span class="font-medium text-right max-w-[60%]">${escapeHtml(customer.address || "Chưa cập nhật")}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Ngày tham gia:</span><span class="font-medium text-right">${formatDate(customer.joinDate)}</span></div>
      </div>
    </section>

    <section class="grid gap-4 rounded-lg border border-gray-200 p-4">
      <h4 class="font-semibold">Thong ke mua hang</h4>
      <div class="grid grid-cols-2 gap-4">
        <div class="rounded-lg border border-gray-200 p-3">
          <p class="text-sm text-gray-500">Tổng đơn hàng</p>
          <p class="text-2xl font-bold text-[#FF6B35]">${Number(customer.totalOrders || 0)}</p>
        </div>
        <div class="rounded-lg border border-gray-200 p-3">
          <p class="text-sm text-gray-500">Tổng chi tiêu</p>
          <p class="text-2xl font-bold text-[#FF6B35]">${formatPrice(customer.totalSpent)}</p>
        </div>
      </div>
      <p class="text-sm text-gray-500">Gia tri trung binh moi don: <span class="font-medium text-[#FF6B35]">${formatPrice(avgSpend)}</span></p>
    </section>

    <div class="flex justify-end gap-2">
      <button
        type="button"
        id="toggleStatusFromDialogBtn"
        class="border rounded-[10px] px-4 py-2 ${
          customer.status === "active"
            ? "border-red-200 text-red-600 hover:bg-red-50"
            : "border-green-200 text-green-600 hover:bg-green-50"
        }"
      >
        <i class="fa-solid ${customer.status === "active" ? "fa-user-xmark" : "fa-user-check"} mr-2"></i>
        ${customer.status === "active" ? "Vô hiệu hóa" : "Kích hoạt"}
      </button>
      <button type="button" id="closeCustomerDialogActionBtn" class="border border-gray-300 rounded-[10px] px-4 py-2 hover:bg-gray-50">Đóng</button>
    </div>
  `;

  document.getElementById("toggleStatusFromDialogBtn")?.addEventListener("click", async () => {
    await toggleCustomerStatus(customer.id);

    const refreshed = customersState.find((item) => item.id === customer.id);
    if (refreshed) {
      selectedCustomerId = refreshed.id;
      renderDetailDialog(refreshed);
    }
  });

  document.getElementById("closeCustomerDialogActionBtn")?.addEventListener("click", closeCustomerDialog);
}

function openCustomerDialog(customerId) {
  const dialog = document.getElementById("customerDetailDialog");
  if (!dialog) return;

  const customer = customersState.find((item) => item.id === String(customerId));
  if (!customer) return;

  selectedCustomerId = customer.id;
  renderDetailDialog(customer);

  dialog.classList.remove("hidden");
  dialog.classList.add("flex");
}

function closeCustomerDialog() {
  const dialog = document.getElementById("customerDetailDialog");
  if (!dialog) return;

  selectedCustomerId = null;
  dialog.classList.add("hidden");
  dialog.classList.remove("flex");
}

async function toggleCustomerStatus(customerId) {
  const target = customersState.find((customer) => customer.id === String(customerId));
  if (!target) return;

  const nextStatus = target.status === "active" ? "inactive" : "active";

  try {
    await fetchJson("/backend/api/customers.php", {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        id: Number(customerId),
        status: nextStatus,
      }),
    });

    customersState = customersState.map((customer) =>
      customer.id === String(customerId) ? { ...customer, status: nextStatus } : customer,
    );

    renderStats();
    renderCustomersTable();

    const actionText = nextStatus === "active" ? "kích hoạt" : "vô hiệu hóa";
    showToast(`Đã ${actionText} tài khoản khách hàng`, "success");
  } catch (error) {
    showToast(String(error?.message || "Không thể cập nhật trạng thái"), "error");
  }
}

function bindEvents() {
  document.getElementById("customerSearchInput")?.addEventListener("input", (event) => {
    searchTerm = String(event.target.value || "");
    currentPage = 1;
    renderCustomersTable();
  });

  document.getElementById("customersTableBody")?.addEventListener("click", (event) => {
    const button = event.target.closest("button[data-action]");
    if (!button) return;

    const action = button.dataset.action;
    const customerId = button.dataset.id;
    if (!action || !customerId) return;

    if (action === "view") {
      openCustomerDialog(customerId);
      return;
    }

    if (action === "toggle-status") {
      toggleCustomerStatus(customerId);
    }
  });

  document.getElementById("closeCustomerDialogBtn")?.addEventListener("click", closeCustomerDialog);

  document.getElementById("customerDetailDialog")?.addEventListener("click", (event) => {
    if (event.target.id === "customerDetailDialog") {
      closeCustomerDialog();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeCustomerDialog();
    }
  });
}

async function loadCustomers() {
  const payload = await fetchJson("/backend/api/customers.php", {
    method: "GET",
    headers: { Accept: "application/json" },
  });

  customersState = Array.isArray(payload?.data) ? payload.data : [];
}

document.addEventListener("DOMContentLoaded", async () => {
  bindEvents();

  try {
    await loadCustomers();
    renderStats();
    renderCustomersTable();
  } catch (error) {
    if (error?.status === 401 || error?.status === 403) {
      window.location.href = "/src/pages/auth/login.php";
      return;
    }

    showToast(String(error?.message || "Không tải được dữ liệu khách hàng"), "error");
  }
});
