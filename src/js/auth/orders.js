const STATUS_LABELS = {
  pending: "Chờ xử lý",
  processing: "Đang xử lý",
  shipped: "Đã gửi",
  delivered: "Đã giao",
  cancelled: "Đã hủy",
};

const STATUS_CLASSES = {
  pending: "status-pending",
  processing: "status-processing",
  shipped: "status-shipped",
  delivered: "status-delivered",
  cancelled: "status-cancelled",
};

let ordersState = [];
let selectedOrder = null;
let toastTimer = null;

function showToast(message, type = "success") {
  const toast = document.getElementById("ordersToast");
  if (!toast) return;

  toast.innerText = message;
  toast.classList.remove("success", "warning", "error");
  toast.classList.add(type, "show");

  if (toastTimer) {
    clearTimeout(toastTimer);
  }

  toastTimer = setTimeout(() => {
    toast.classList.remove("show");
  }, 1800);
}

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

function formatDate(dateString) {
  const date = new Date(dateString || Date.now());
  return date.toLocaleString("vi-VN", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  });
}

async function updateCartCount() {
  try {
    const payload = await fetchJson("/backend/api/cart.php", {
      method: "GET",
      headers: { Accept: "application/json" },
    });

    const cart = Array.isArray(payload?.data) ? payload.data : [];
    const cartCount = document.getElementById("cart-count");
    if (!cartCount) return;
    cartCount.innerText = String(cart.length);
  } catch (_error) {
    const cartCount = document.getElementById("cart-count");
    if (!cartCount) return;
    cartCount.innerText = "0";
  }
}

function renderOrders() {
  const list = document.getElementById("ordersList");
  const empty = document.getElementById("ordersEmpty");
  if (!list || !empty) return;

  if (ordersState.length === 0) {
    list.innerHTML = "";
    empty.classList.remove("hidden");
    return;
  }

  empty.classList.add("hidden");
  list.innerHTML = ordersState
    .map((order) => {
      const previewItems = order.items.slice(0, 3);
      const hasMore = order.items.length > 3;

      return `
        <article class="order-card">
          <div class="order-top">
            <div class="order-main">
              <div class="order-number-wrap">
                <h2 class="order-number">${order.orderNumber}</h2>
                <span class="order-status ${STATUS_CLASSES[order.status]}">${STATUS_LABELS[order.status]}</span>
              </div>

              <div class="order-meta">
                <p><i class="fa-regular fa-calendar"></i>Ngày đặt: ${formatDate(order.orderDate)}</p>
                <p><i class="fa-regular fa-credit-card"></i>Tổng tiền: <span class="order-total-value">${formatPrice(order.totalAmount)}</span></p>
              </div>
            </div>

            <div class="order-actions">
              <button type="button" class="order-btn view" onclick="viewOrderDetail('${order.id}')">Xem chi tiết</button>
              ${
                order.status === "pending"
                  ? `<button type="button" class="order-btn cancel" onclick="cancelOrder('${order.id}')">Hủy đơn</button>`
                  : ""
              }
            </div>
          </div>

          <hr class="order-divider">

          <div class="order-preview-grid">
            ${previewItems
              .map(
                (item) => `
                  <div class="order-item-preview">
                    <img src="${item.product.image}" alt="${item.product.name}" onerror="this.src='https://via.placeholder.com/80'">
                    <div>
                      <h4>${item.product.name}</h4>
                      <p>Số lượng: ${item.quantity}</p>
                    </div>
                  </div>
                `,
              )
              .join("")}
            ${
              hasMore
                ? `<div class="order-more-item">+${order.items.length - 3} sản phẩm khác</div>`
                : ""
            }
          </div>
        </article>
      `;
    })
    .join("");
}

function closeOrderModal() {
  const modal = document.getElementById("orderModal");
  if (!modal) return;

  selectedOrder = null;
  modal.classList.add("hidden");
  modal.setAttribute("aria-hidden", "true");
}

function renderOrderModal(order) {
  const modalBody = document.getElementById("orderModalBody");
  if (!modalBody) return;

  modalBody.innerHTML = `
    <div class="modal-info-grid">
      <div class="modal-info-box">
        <p>Mã đơn hàng</p>
        <p>${order.orderNumber}</p>
      </div>
      <div class="modal-info-box">
        <p>Trạng thái</p>
        <p><span class="order-status ${STATUS_CLASSES[order.status]}">${STATUS_LABELS[order.status]}</span></p>
      </div>
      <div class="modal-info-box">
        <p>Ngày đặt</p>
        <p>${formatDate(order.orderDate)}</p>
      </div>
      <div class="modal-info-box">
        <p>Thanh toan</p>
        <p>${order.paymentMethod}</p>
      </div>
    </div>

    <div class="modal-address">
      <i class="fa-solid fa-location-dot"></i>
      <div>
        <p class="modal-address-title">Dia chi giao hang</p>
        <p>${order.shippingAddress}</p>
      </div>
    </div>

    <h4 class="modal-section-title">Sản phẩm đã đặt</h4>
    <div class="modal-item-list">
      ${order.items
        .map(
          (item) => `
            <article class="modal-item-card">
              <img src="${item.product.image}" alt="${item.product.name}" onerror="this.src='https://via.placeholder.com/80'">
              <div class="modal-item-main">
                <h5>${item.product.name}</h5>
                <p>${formatPrice(item.product.price)} x ${item.quantity}</p>
              </div>
              <div class="modal-item-subtotal">${formatPrice(Number(item.product.price) * Number(item.quantity))}</div>
            </article>
          `,
        )
        .join("")}
    </div>

    <div class="modal-total-box">
      <span>Tổng cộng:</span>
      <strong>${formatPrice(order.totalAmount)}</strong>
    </div>
  `;
}

function openOrderModal(order) {
  const modal = document.getElementById("orderModal");
  if (!modal) return;

  selectedOrder = order;
  renderOrderModal(order);
  modal.classList.remove("hidden");
  modal.setAttribute("aria-hidden", "false");
}

function viewOrderDetail(orderId) {
  const order = ordersState.find((item) => item.id === String(orderId));
  if (!order) return;
  openOrderModal(order);
}

async function cancelOrder(orderId) {
  try {
    await fetchJson("/backend/api/orders.php", {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        orderId,
        status: "cancelled",
      }),
    });

    ordersState = ordersState.map((order) =>
      order.id === String(orderId) ? { ...order, status: "cancelled" } : order,
    );
    renderOrders();
    showToast("Đơn hàng đã được hủy", "success");

    if (selectedOrder && selectedOrder.id === String(orderId)) {
      selectedOrder.status = "cancelled";
      renderOrderModal(selectedOrder);
    }
  } catch (error) {
    showToast(String(error?.message || "Không thể hủy đơn"), "error");
  }
}

async function bootstrapOrdersPage() {
  try {
    const payload = await fetchJson("/backend/api/orders.php", {
      method: "GET",
      headers: { Accept: "application/json" },
    });

    ordersState = Array.isArray(payload?.data) ? payload.data : [];
    renderOrders();
    updateCartCount();
  } catch (error) {
    if (error?.status === 401) {
      window.location.href = "login.php";
      return;
    }

    showToast(String(error?.message || "Không tải được đơn hàng"), "error");
  }
}

document.addEventListener("DOMContentLoaded", () => {
  bootstrapOrdersPage();

  const modal = document.getElementById("orderModal");
  document.getElementById("closeOrderModalBtn")?.addEventListener("click", closeOrderModal);

  modal?.addEventListener("click", (event) => {
    if (event.target === modal) {
      closeOrderModal();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeOrderModal();
    }
  });
});

window.viewOrderDetail = viewOrderDetail;
window.cancelOrder = cancelOrder;
