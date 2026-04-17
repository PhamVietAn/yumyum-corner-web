const ITEMS_PER_PAGE = 10;

ensureFreshDataAfterNavigation();

const STATUS_LABELS = {
  pending: "Chờ xử lý",
  processing: "Đang xử lý",
  shipped: "Đã gửi",
  delivered: "Đã giao",
  cancelled: "Đã hủy",
};

const STATUS_BADGE_CLASSES = {
  pending: "bg-amber-100 text-amber-700",
  processing: "bg-blue-100 text-blue-700",
  shipped: "bg-violet-100 text-violet-700",

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
  delivered: "bg-green-100 text-green-700",
  cancelled: "bg-red-100 text-red-700",
};

const STATUS_SELECT_CLASSES = {
  pending: "bg-amber-50 text-amber-700 border-amber-300",
  processing: "bg-blue-50 text-blue-700 border-blue-300",
  shipped: "bg-violet-50 text-violet-700 border-violet-300",
  delivered: "bg-green-50 text-green-700 border-green-300",
  cancelled: "bg-red-50 text-red-700 border-red-300",
};

let ordersState = [];
let searchTerm = "";
let statusFilter = "all";
let currentPage = 1;
let selectedOrderId = null;
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

function formatPrice(value) {
  return `${Number(value || 0).toLocaleString("vi-VN")}đ`;
}

function formatDate(value) {
  const date = new Date(value || Date.now());
  if (Number.isNaN(date.getTime())) return "-";

  return date.toLocaleString("vi-VN", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  });
}

function escapeHtml(value) {
  return String(value ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function showToast(message, type = "success") {
  const toast = document.getElementById("orderToast");
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

function getFilteredOrders() {
  const keyword = searchTerm.trim().toLowerCase();

  return ordersState.filter((order) => {
    const matchesSearch =
      !keyword ||
      String(order.orderNumber).toLowerCase().includes(keyword) ||
      String(order.customerName).toLowerCase().includes(keyword) ||
      String(order.customerEmail).toLowerCase().includes(keyword);

    const matchesStatus = statusFilter === "all" || order.status === statusFilter;

    return matchesSearch && matchesStatus;
  });
}

function getPageCount(totalItems) {
  return Math.max(1, Math.ceil(totalItems / ITEMS_PER_PAGE));
}

function getPageItems(list, page) {
  const start = (page - 1) * ITEMS_PER_PAGE;
  return list.slice(start, start + ITEMS_PER_PAGE);
}

function renderPagination(totalItems) {
  const summary = document.getElementById("orderPaginationSummary");
  const controls = document.getElementById("orderPaginationControls");
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
        class="order-page-btn min-w-9 h-9 px-3 rounded-md border text-sm ${activeClass}"
        data-page="${page}"
      >
        ${page}
      </button>
    `;
  }).join("");

  controls.innerHTML = `
    <button type="button" class="order-page-btn h-9 px-3 rounded-md border border-gray-300 text-sm ${
      prevDisabled ? "opacity-40 cursor-not-allowed" : "hover:bg-gray-50"
    }" data-page="prev" ${prevDisabled}>Trước</button>
    ${pageButtons}
    <button type="button" class="order-page-btn h-9 px-3 rounded-md border border-gray-300 text-sm ${
      nextDisabled ? "opacity-40 cursor-not-allowed" : "hover:bg-gray-50"
    }" data-page="next" ${nextDisabled}>Sau</button>
  `;

  controls.querySelectorAll(".order-page-btn").forEach((button) => {
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

      renderOrdersTable();
    });
  });
}

function renderOrderRows(orders) {
  const tbody = document.getElementById("ordersTableBody");
  if (!tbody) return;

  if (orders.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" class="border-b border-gray-200 py-10 text-center text-gray-500">Không có đơn hàng phù hợp</td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = orders
    .map(
      (order) => `
        <tr>
          <td class="border-b border-gray-200 py-3 font-medium">${escapeHtml(order.orderNumber)}</td>
          <td class="border-b border-gray-200 py-3">
            <div class="font-medium">${escapeHtml(order.customerName)}</div>
            <div class="text-xs text-gray-500">${escapeHtml(order.customerEmail || "-")}</div>
          </td>
          <td class="border-b border-gray-200 py-3">${escapeHtml(formatDate(order.orderDate))}</td>
          <td class="border-b border-gray-200 py-3 text-right font-medium text-[#FF6B35]">${formatPrice(order.totalAmount)}</td>
          <td class="border-b border-gray-200 py-3 text-center">
            <select
              class="status-select select rounded-full px-2.5 py-1 text-xs font-medium focus:outline-none ${STATUS_SELECT_CLASSES[order.status] || "bg-gray-50 text-gray-700 border-gray-300"}"
              data-id="${escapeHtml(order.id)}"
              style="width: 124px;"
            >
              ${Object.entries(STATUS_LABELS)
                .map(
                  ([value, label]) =>
                    `<option value="${value}" ${value === order.status ? "selected" : ""}>${label}</option>`,
                )
                .join("")}
            </select>
          </td>
          <td class="border-b border-gray-200 py-3 text-right">
            <button type="button" class="text-blue-500 hover:underline" data-action="view" data-id="${escapeHtml(order.id)}">Xem chi tiết</button>
          </td>
        </tr>
      `,
    )
    .join("");
}

function renderOrdersTable() {
  const filteredOrders = getFilteredOrders().sort(
    (a, b) => new Date(b.orderDate).getTime() - new Date(a.orderDate).getTime(),
  );

  const total = document.getElementById("totalOrders");
  if (total) {
    total.innerText = String(filteredOrders.length);
  }

  const totalPages = getPageCount(filteredOrders.length);
  if (currentPage > totalPages) {
    currentPage = totalPages;
  }

  const pageOrders = getPageItems(filteredOrders, currentPage);
  renderOrderRows(pageOrders);
  renderPagination(filteredOrders.length);
}

function renderDetailDialog(order) {
  const orderNumber = document.getElementById("orderDialogNumber");
  const content = document.getElementById("orderDetailContent");
  if (!orderNumber || !content) return;

  orderNumber.innerText = order.orderNumber;

  content.innerHTML = `
    <section class="grid gap-3 rounded-lg border border-gray-200 p-4">
      <h4 class="font-semibold">Thông tin khách hàng</h4>
      <div class="grid gap-2 text-sm">
        <div class="flex justify-between gap-4"><span class="text-gray-500">Tên khách hàng:</span><span class="font-medium text-right">${escapeHtml(order.customerName)}</span></div>
        <div class="flex justify-between gap-4"><span class="text-gray-500">Email:</span><span class="font-medium text-right">${escapeHtml(order.customerEmail || "-")}</span></div>
        <div class="flex justify-between gap-4"><span class="text-gray-500">Địa chỉ giao hàng:</span><span class="font-medium text-right max-w-[60%]">${escapeHtml(order.shippingAddress)}</span></div>
      </div>
    </section>

    <section class="grid gap-3 rounded-lg border border-gray-200 p-4">
      <h4 class="font-semibold">Thông tin đơn hàng</h4>
      <div class="grid gap-2 text-sm">
        <div class="flex justify-between gap-4"><span class="text-gray-500">Ngày đặt:</span><span class="font-medium text-right">${formatDate(order.orderDate)}</span></div>
        <div class="flex justify-between gap-4"><span class="text-gray-500">Phương thức thanh toán:</span><span class="font-medium text-right">${escapeHtml(order.paymentMethod)}</span></div>
        <div class="flex justify-between gap-4"><span class="text-gray-500">Trạng thái:</span><span class="py-1 px-3 rounded-full text-xs font-medium ${STATUS_BADGE_CLASSES[order.status]}">${STATUS_LABELS[order.status]}</span></div>
      </div>
    </section>

    <section class="rounded-lg border border-gray-200 p-4">
      <h4 class="font-semibold mb-4">Sản phẩm đã đặt</h4>
      <div class="space-y-3">
        ${order.items
          .map(
            (item) => `
              <article class="flex items-center gap-4 rounded-lg bg-slate-50 p-3">
                <img src="${escapeHtml(item.product.image)}" alt="${escapeHtml(item.product.name)}" class="h-16 w-16 rounded-lg object-cover" onerror="this.src='https://via.placeholder.com/80'">
                <div class="flex-1">
                  <div class="font-medium">${escapeHtml(item.product.name)}</div>
                  <div class="text-sm text-gray-500">${formatPrice(item.product.price)} x ${item.quantity}</div>
                </div>
                <div class="font-medium text-[#FF6B35]">${formatPrice(item.product.price * item.quantity)}</div>
              </article>
            `,
          )
          .join("")}
      </div>
      <div class="mt-4 flex justify-between border-t pt-4">
        <span class="font-semibold">Tổng cộng:</span>
        <span class="text-lg font-bold text-[#FF6B35]">${formatPrice(order.totalAmount)}</span>
      </div>
    </section>

    <section class="grid gap-2">
      <label for="orderStatusUpdateSelect" class="font-medium">Cập nhật trạng thái đơn hàng</label>
      <select id="orderStatusUpdateSelect" class="select rounded-[10px] p-2.5 focus:outline-none w-full font-medium ${STATUS_SELECT_CLASSES[order.status] || "bg-gray-50 text-gray-700 border-gray-300"}">
        ${Object.entries(STATUS_LABELS)
          .map(
            ([value, label]) =>
              `<option value="${value}" ${value === order.status ? "selected" : ""}>${label}</option>`,
          )
          .join("")}
      </select>
    </section>
  `;

  const statusSelect = document.getElementById("orderStatusUpdateSelect");
  statusSelect?.addEventListener("change", async (event) => {
    const newStatus = String(event.target.value || order.status);
    await updateOrderStatus(order.id, newStatus);

    const refreshed = ordersState.find((item) => item.id === order.id);
    if (refreshed) {
      selectedOrderId = refreshed.id;
      renderDetailDialog(refreshed);
    }
  });
}

function openDetailDialog(orderId) {
  const dialog = document.getElementById("orderDetailDialog");
  if (!dialog) return;

  const order = ordersState.find((item) => item.id === String(orderId));
  if (!order) return;

  selectedOrderId = order.id;
  renderDetailDialog(order);

  dialog.classList.remove("hidden");
  dialog.classList.add("flex");
}

function closeDetailDialog() {
  const dialog = document.getElementById("orderDetailDialog");
  if (!dialog) return;

  selectedOrderId = null;
  dialog.classList.add("hidden");
  dialog.classList.remove("flex");
}

async function updateOrderStatus(orderId, newStatus) {
  if (!STATUS_LABELS[newStatus]) return;

  try {
    await fetchJson("/backend/api/orders.php", {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        orderId: Number(orderId),
        status: newStatus,
      }),
    });

    ordersState = ordersState.map((order) =>
      order.id === String(orderId)
        ? {
            ...order,
            status: newStatus,
          }
        : order,
    );

    renderOrdersTable();
    showToast("Cập nhật trạng thái đơn hàng thành công", "success");
  } catch (error) {
    showToast(String(error?.message || "Không thể cập nhật trạng thái"), "error");
  }
}

function bindEvents() {
  document.getElementById("orderSearchInput")?.addEventListener("input", (event) => {
    searchTerm = String(event.target.value || "");
    currentPage = 1;
    renderOrdersTable();
  });

  document.getElementById("orderStatusFilter")?.addEventListener("change", (event) => {
    statusFilter = String(event.target.value || "all");
    currentPage = 1;
    renderOrdersTable();
  });

  document.getElementById("ordersTableBody")?.addEventListener("click", (event) => {
    const button = event.target.closest("button[data-action='view']");
    if (!button) return;

    const id = button.dataset.id;
    if (!id) return;

    openDetailDialog(id);
  });

  document.getElementById("ordersTableBody")?.addEventListener("change", (event) => {
    const select = event.target.closest("select.status-select");
    if (!select) return;

    const orderId = select.dataset.id;
    const newStatus = select.value;
    if (!orderId || !newStatus) return;

    updateOrderStatus(orderId, newStatus);
  });

  document.getElementById("closeOrderDialogBtn")?.addEventListener("click", closeDetailDialog);

  document.getElementById("orderDetailDialog")?.addEventListener("click", (event) => {
    if (event.target.id === "orderDetailDialog") {
      closeDetailDialog();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeDetailDialog();
    }
  });
}

async function loadOrders() {
  const payload = await fetchJson("/backend/api/orders.php?scope=all", {
    method: "GET",
    headers: { Accept: "application/json" },
  });

  ordersState = Array.isArray(payload?.data) ? payload.data : [];
}

document.addEventListener("DOMContentLoaded", async () => {
  bindEvents();

  try {
    await loadOrders();
    renderOrdersTable();
  } catch (error) {
    if (error?.status === 401 || error?.status === 403) {
      window.location.href = "/src/pages/auth/login.php";
      return;
    }

    showToast(String(error?.message || "Không tải được dữ liệu đơn hàng"), "error");
  }
});
