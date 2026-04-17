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

let shipFee = 25000;
let payMethod = "cod";
let toastTimer = null;
let cartState = [];

function formatPrice(value) {
  return `${Number(value).toLocaleString("vi-VN")}đ`;
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

function showToast(message, type = "warning") {
  const toast = document.getElementById("checkoutToast");
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

function updateCartCount() {
  const cartCount = document.getElementById("cart-count");
  if (!cartCount) return;
  cartCount.innerText = String(cartState.length);
}

async function removeCheckoutItem(itemId) {
  try {
    const payload = await fetchJson(`/backend/api/cart.php?itemId=${itemId}`, {
      method: "DELETE",
      headers: { Accept: "application/json" },
    });

    cartState = Array.isArray(payload?.data) ? payload.data : [];
    renderCartSummary();
  } catch (error) {
    showToast(String(error?.message || "Không thể xóa sản phẩm"), "error");
  }
}

function renderCartSummary() {
  const cartItems = document.getElementById("cartItems");
  const emptyMsg = document.getElementById("emptyMsg");
  const subtotalEl = document.getElementById("subtotal");
  const shipEl = document.getElementById("ship");
  const totalEl = document.getElementById("total");
  const placeOrderBtn = document.getElementById("placeOrderBtn");

  if (!cartItems || !emptyMsg || !subtotalEl || !shipEl || !totalEl || !placeOrderBtn) {
    return;
  }

  if (cartState.length === 0) {
    cartItems.innerHTML = "";
    emptyMsg.classList.remove("hidden");
    placeOrderBtn.disabled = true;
    subtotalEl.innerText = formatPrice(0);
    shipEl.innerText = formatPrice(shipFee);
    totalEl.innerText = formatPrice(shipFee);
    updateCartCount();
    return;
  }

  emptyMsg.classList.add("hidden");
  placeOrderBtn.disabled = false;

  cartItems.innerHTML = cartState
    .map(
      (item) => `
        <article class="checkout-item">
          <img class="checkout-item-img" src="${item.img}" alt="${item.name}" onerror="this.src='https://via.placeholder.com/120'">
          <div class="checkout-item-main">
            <h4>${item.name}</h4>
            <p class="checkout-item-meta">${item.category}</p>
            <p class="checkout-item-meta">Số lượng: ${item.qty}</p>
          </div>
          <div class="checkout-item-price-wrap">
            <div class="checkout-item-price">${formatPrice(item.price * item.qty)}</div>
            <button class="checkout-remove" type="button" onclick="removeCheckoutItem(${item.itemId})">Xóa</button>
          </div>
        </article>
      `,
    )
    .join("");

  const subtotal = cartState.reduce((sum, item) => sum + Number(item.price) * Number(item.qty), 0);
  subtotalEl.innerText = formatPrice(subtotal);
  shipEl.innerText = formatPrice(shipFee);
  totalEl.innerText = formatPrice(subtotal + shipFee);

  updateCartCount();
}

function bindShippingOptions() {
  document.querySelectorAll("#shippingBox .option").forEach((option) => {
    option.addEventListener("click", () => {
      document.querySelectorAll("#shippingBox .option").forEach((opt) => {
        opt.classList.remove("active");
      });

      option.classList.add("active");
      shipFee = Number(option.dataset.ship || 25000);
      renderCartSummary();
    });
  });
}

function bindPaymentOptions() {
  const qrBox = document.getElementById("qrBox");
  const qrImg = document.getElementById("qrImg");

  document.querySelectorAll("#paymentBox .option").forEach((option) => {
    option.addEventListener("click", () => {
      document.querySelectorAll("#paymentBox .option").forEach((opt) => {
        opt.classList.remove("active");
      });

      option.classList.add("active");
      payMethod = option.dataset.pay || "cod";

      if (!qrBox || !qrImg) return;

      if (payMethod === "vnpay") {
        qrBox.classList.remove("hidden");
        qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=PAY_${Date.now()}`;
      } else {
        qrBox.classList.add("hidden");
      }
    });
  });
}

function validateForm() {
  let valid = true;
  const name = document.getElementById("name");
  const phone = document.getElementById("phone");
  const email = document.getElementById("email");
  const address = document.getElementById("address");
  const city = document.getElementById("city");
  const district = document.getElementById("district");

  if (!name || !phone || !email || !address || !city || !district) {
    return false;
  }

  if (name.value.trim().length < 2) {
    name.classList.add("error");
    valid = false;
  }

  if (!/^0\d{9,10}$/.test(phone.value.trim())) {
    phone.classList.add("error");
    valid = false;
  }

  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
    email.classList.add("error");
    valid = false;
  }

  if (!address.value.trim()) {
    address.classList.add("error");
    valid = false;
  }

  if (!city.value) {
    city.classList.add("error");
    valid = false;
  }

  if (!district.value) {
    district.classList.add("error");
    valid = false;
  }

  return valid;
}

async function submitOrder() {
  if (cartState.length === 0) {
    showToast("Giỏ hàng đang trống", "warning");
    return;
  }

  if (!validateForm()) {
    showToast("Vui lòng nhập đầy đủ thông tin", "error");
    return;
  }

  const shipping = {
    fullName: document.getElementById("name")?.value.trim() || "",
    phone: document.getElementById("phone")?.value.trim() || "",
    email: document.getElementById("email")?.value.trim() || "",
    address: document.getElementById("address")?.value.trim() || "",
    city: document.getElementById("city")?.selectedOptions?.[0]?.text || "",
    district: document.getElementById("district")?.value || "",
  };

  try {
    await fetchJson("/backend/api/orders.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        shipping,
        paymentMethod: payMethod.toUpperCase(),
        shippingFee: shipFee,
      }),
    });

    cartState = [];
    renderCartSummary();
    showToast("Đặt hàng thành công", "success");

    setTimeout(() => {
      window.location.href = "success.php";
    }, 500);
  } catch (error) {
    if (error?.status === 401) {
      window.location.href = "login.php";
      return;
    }

    showToast(String(error?.message || "Đặt hàng thất bại"), "error");
  }
}

async function loadAddress() {
  try {
    const city = document.getElementById("city");
    const district = document.getElementById("district");
    if (!city || !district) return;

    const response = await fetch("https://provinces.open-api.vn/api/?depth=2");
    const data = await response.json();

    city.innerHTML = '<option value="">Tinh/Thanh pho *</option>';
    data.forEach((item) => {
      city.innerHTML += `<option value="${item.code}">${item.name}</option>`;
    });

    city.addEventListener("change", () => {
      district.classList.remove("error");
      const selected = data.find((item) => String(item.code) === String(city.value));
      district.innerHTML = '<option value="">Quan/Huyen *</option>';

      if (!selected) return;

      selected.districts.forEach((d) => {
        district.innerHTML += `<option value="${d.name}">${d.name}</option>`;
      });
    });
  } catch (_error) {
    showToast("Không tải được danh sách địa chỉ", "warning");
  }
}

async function prefillUserInfo() {
  try {
    const payload = await fetchJson("/backend/api/profile.php", {
      method: "GET",
      headers: { Accept: "application/json" },
    });

    const profile = payload?.data || {};

    const name = document.getElementById("name");
    const phone = document.getElementById("phone");
    const email = document.getElementById("email");
    const address = document.getElementById("address");

    if (name && profile?.fullName) name.value = profile.fullName;
    if (phone && profile?.phone) phone.value = profile.phone;
    if (email && profile?.email) email.value = profile.email;

    if (address && profile?.address) {
      const detail = profile.address.detail || "";
      const district = profile.address.district || "";
      const city = profile.address.city || "";
      address.value = [detail, district, city].filter(Boolean).join(", ");
    }
  } catch (_error) {
    // Keep checkout usable when profile cannot be prefetched.
  }
}

function bindClearErrorOnInput() {
  ["name", "phone", "email", "address"].forEach((id) => {
    const element = document.getElementById(id);
    element?.addEventListener("input", () => {
      element.classList.remove("error");
    });
  });

  ["city", "district"].forEach((id) => {
    const element = document.getElementById(id);
    element?.addEventListener("change", () => {
      element.classList.remove("error");
    });
  });
}

async function bootstrapCheckout() {
  try {
    const payload = await fetchJson("/backend/api/cart.php", {
      method: "GET",
      headers: { Accept: "application/json" },
    });

    cartState = Array.isArray(payload?.data) ? payload.data : [];
    renderCartSummary();
  } catch (error) {
    if (error?.status === 401) {
      window.location.href = "login.php";
      return;
    }

    showToast(String(error?.message || "Không tải được giỏ hàng"), "error");
  }
}

document.addEventListener("DOMContentLoaded", async () => {
  await bootstrapCheckout();
  loadAddress();
  prefillUserInfo();
  bindShippingOptions();
  bindPaymentOptions();
  bindClearErrorOnInput();

  document.getElementById("placeOrderBtn")?.addEventListener("click", submitOrder);
});

window.removeCheckoutItem = removeCheckoutItem;
