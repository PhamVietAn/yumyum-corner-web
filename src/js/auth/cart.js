ensureFreshDataAfterNavigation();

function ensureFreshDataAfterNavigation() {
  if (window.__forceReloadOnNavigationAttached) {
    return;
  }

  window.__forceReloadOnNavigationAttached = true;

  const reloadFlagKey = `__forceReloadOnce:${window.location.pathname}${window.location.search}`;
  const navigationEntries =
    typeof performance.getEntriesByType === "function"
      ? performance.getEntriesByType("navigation")
      : [];
  const navigationType = navigationEntries?.[0]?.type;

  const hasInternalReferrer = (() => {
    if (!document.referrer) return false;
    try {
      return new URL(document.referrer).origin === window.location.origin;
    } catch (_error) {
      return false;
    }
  })();

  if (hasInternalReferrer && navigationType === "navigate") {
    if (sessionStorage.getItem(reloadFlagKey) !== "1") {
      sessionStorage.setItem(reloadFlagKey, "1");
      window.location.reload();
      return;
    }

    sessionStorage.removeItem(reloadFlagKey);
  }

  window.addEventListener("pageshow", (event) => {
    const navEntries =
      typeof performance.getEntriesByType === "function"
        ? performance.getEntriesByType("navigation")
        : [];
    const navType = navEntries?.[0]?.type;

    if (event.persisted || navType === "back_forward") {
      window.location.reload();
    }
  });
}

const SHIPPING_FEE = 25000;
let pendingRemoveItemId = null;
let toastTimer = null;
let cartState = [];

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
  return `${Number(value).toLocaleString("vi-VN")}đ`;
}

function updateCartCount() {
  const cartCount = document.getElementById("cart-count");
  if (!cartCount) return;
  cartCount.innerText = String(cartState.length);
}

function renderCartItems() {
  const container = document.getElementById("cartItems");
  if (!container) return;

  container.innerHTML = cartState
    .map(
      (item) => `
        <article class="cart-item">
          <img
            class="cart-item-image"
            src="${item.img || "https://via.placeholder.com/200x200"}"
            alt="${item.name || "Sản phẩm"}"
            onerror="this.src='https://via.placeholder.com/200x200'"
          >

          <div class="item-info">
            <h3 class="item-name">${item.name || "Sản phẩm"}</h3>
            <div class="item-category">${item.category || "Snack"}</div>
            <div class="item-price">${formatPrice(item.price)}</div>

            <div class="item-actions">
              <div class="qty-control">
                <button class="qty-btn" type="button" onclick="changeQty(${item.itemId}, -1)">−</button>
                <span class="qty-value">${Number(item.qty || 1)}</span>
                <button class="qty-btn" type="button" onclick="changeQty(${item.itemId}, 1)">+</button>
              </div>
            </div>
          </div>

          <button class="remove-btn" type="button" onclick="removeItem(${item.itemId})" aria-label="Xóa sản phẩm">
            <i class="fa-regular fa-trash-can"></i>
          </button>
        </article>
      `,
    )
    .join("");
}

function openRemoveModal(itemId) {
  const modal = document.getElementById("removeModal");
  const modalText = document.getElementById("removeModalText");
  if (!modal || !modalText) return;

  const item = cartState.find((cartItem) => Number(cartItem.itemId) === Number(itemId));
  if (!item) return;

  pendingRemoveItemId = Number(itemId);
  modalText.innerText = `Bạn có chắc chắn muốn xóa "${item.name || "sản phẩm"}" khỏi giỏ hàng?`;

  modal.classList.remove("hidden");
  modal.setAttribute("aria-hidden", "false");
}

function closeRemoveModal() {
  const modal = document.getElementById("removeModal");
  if (!modal) return;

  pendingRemoveItemId = null;
  modal.classList.add("hidden");
  modal.setAttribute("aria-hidden", "true");
}

function showToast(message) {
  const toast = document.getElementById("cartToast");
  if (!toast) return;

  toast.innerText = message;
  toast.classList.add("show");

  if (toastTimer) {
    clearTimeout(toastTimer);
  }

  toastTimer = setTimeout(() => {
    toast.classList.remove("show");
  }, 1800);
}

async function confirmRemoveItem() {
  const itemId = Number(pendingRemoveItemId);
  if (Number.isNaN(itemId) || itemId <= 0) {
    closeRemoveModal();
    return;
  }

  try {
    const payload = await fetchJson(`/backend/api/cart.php?itemId=${itemId}`, {
      method: "DELETE",
      headers: { Accept: "application/json" },
    });

    cartState = Array.isArray(payload?.data) ? payload.data : [];
    closeRemoveModal();
    renderCart();
    showToast("Đã xóa sản phẩm khỏi giỏ hàng");
  } catch (error) {
    showToast(String(error?.message || "Xóa sản phẩm thất bại"));
  }
}

function renderCart() {
  const empty = document.getElementById("emptyCart");
  const content = document.getElementById("cartContent");
  const subtotalEl = document.getElementById("subtotal");
  const totalEl = document.getElementById("total");
  const infoEl = document.getElementById("cart-info");

  if (!empty || !content || !subtotalEl || !totalEl || !infoEl) return;

  if (cartState.length === 0) {
    empty.classList.remove("hidden");
    content.classList.add("hidden");
    updateCartCount();
    return;
  }

  empty.classList.add("hidden");
  content.classList.remove("hidden");

  const subtotal = cartState.reduce((sum, item) => sum + Number(item.price) * Number(item.qty), 0);
  const total = subtotal + SHIPPING_FEE;

  renderCartItems();

  subtotalEl.innerText = formatPrice(subtotal);
  totalEl.innerText = formatPrice(total);
  infoEl.innerText = `Bạn có ${cartState.length} sản phẩm trong giỏ hàng`;

  updateCartCount();
}

async function changeQty(itemId, delta) {
  const item = cartState.find((cartItem) => Number(cartItem.itemId) === Number(itemId));
  if (!item) return;

  const nextQty = Number(item.qty) + Number(delta);
  if (nextQty < 1) return;

  try {
    const payload = await fetchJson("/backend/api/cart.php", {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        itemId,
        quantity: nextQty,
      }),
    });

    cartState = Array.isArray(payload?.data) ? payload.data : [];
    renderCart();
  } catch (error) {
    showToast(String(error?.message || "Cập nhật số lượng thất bại"));
  }
}

function removeItem(itemId) {
  openRemoveModal(itemId);
}

function goCheckout() {
  if (cartState.length === 0) {
    showToast("Giỏ hàng đang trống");
    return;
  }

  window.location.href = "checkout.php";
}

async function bootstrapCart() {
  try {
    const payload = await fetchJson("/backend/api/cart.php", {
      method: "GET",
      headers: { Accept: "application/json" },
    });

    cartState = Array.isArray(payload?.data) ? payload.data : [];
    renderCart();
  } catch (error) {
    if (error?.status === 401) {
      window.location.href = "login.php";
      return;
    }

    showToast(String(error?.message || "Không tải được giỏ hàng"));
  }
}

document.addEventListener("DOMContentLoaded", () => {
  bootstrapCart();

  const modal = document.getElementById("removeModal");
  const cancelBtn = document.getElementById("modalCancelBtn");
  const confirmBtn = document.getElementById("modalConfirmBtn");
  const checkoutBtn = document.getElementById("checkoutBtn");

  if (cancelBtn) {
    cancelBtn.addEventListener("click", closeRemoveModal);
  }

  if (confirmBtn) {
    confirmBtn.addEventListener("click", confirmRemoveItem);
  }

  if (checkoutBtn) {
    checkoutBtn.addEventListener("click", goCheckout);
  }

  if (modal) {
    modal.addEventListener("click", (event) => {
      if (event.target === modal) {
        closeRemoveModal();
      }
    });
  }

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeRemoveModal();
    }
  });
});

window.changeQty = changeQty;
window.removeItem = removeItem;
