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

/* Trang chi tiet san pham */

let PRODUCT_LIST = [];
let detailToastTimer = null;
let cartCount = 0;

function showToast(message, type = "success") {
  const toast = document.getElementById("detailToast");
  if (!toast) return;

  toast.innerText = message;
  toast.classList.remove("success", "warning", "error");
  toast.classList.add(type, "show");

  if (detailToastTimer) {
    clearTimeout(detailToastTimer);
  }

  detailToastTimer = setTimeout(() => {
    toast.classList.remove("show");
  }, 1800);
}

async function fetchJson(url, options = {}) {
  const response = await fetch(url, options);
  const payload = await response.json().catch(() => ({}));

  if (!response.ok || payload?.success === false) {
    const message = payload?.message || "Yêu cầu thất bại";
    const error = new Error(message);
    error.status = response.status;
    throw error;
  }

  return payload;
}

function getProductId() {
  const params = new URLSearchParams(window.location.search);
  return String(params.get("id") || "1");
}

function findProductById(id) {
  const normalizedId = String(id);
  return PRODUCT_LIST.find((product) => String(product.id) === normalizedId);
}

async function syncCartCount() {
  try {
    const payload = await fetchJson("/backend/api/cart.php", {
      method: "GET",
      headers: { Accept: "application/json" },
    });

    const items = Array.isArray(payload?.data) ? payload.data : [];
    cartCount = items.length;
  } catch (_error) {
    cartCount = 0;
  }

  const cartCountElement = document.getElementById("cart-count");
  if (cartCountElement) {
    cartCountElement.innerText = String(cartCount);
  }
}

function updateStockUI(product) {
  const status = document.getElementById("stockStatus");
  const addBtn = document.querySelector(".add");
  const buyBtn = document.querySelector(".buy");
  const qty = document.getElementById("qty");

  if (!status || !addBtn || !buyBtn || !qty) return;

  const hasStockNumber = typeof product.stock === "number";
  const stock = hasStockNumber ? product.stock : null;

  if (stock === 0) {
    status.innerHTML = `
      <span class="stock-dot out"></span>
      <span class="stock-text">Hết hàng</span>
    `;
    addBtn.disabled = true;
    buyBtn.disabled = true;
    qty.disabled = true;
    return;
  }

  if (stock !== null) {
    status.innerHTML = `
      <span class="stock-dot in"></span>
      <span class="stock-text">Còn hàng (${stock})</span>
    `;
  } else {
    status.innerHTML = `
      <span class="stock-dot in"></span>
      <span class="stock-text">Còn hàng</span>
    `;
  }

  addBtn.disabled = false;
  buyBtn.disabled = false;
  qty.disabled = false;
}

async function loadReviews(productId) {
  const list = document.getElementById("reviewList");
  if (!list) return;

  try {
    const payload = await fetchJson(`/backend/api/reviews.php?productId=${encodeURIComponent(productId)}`, {
      method: "GET",
      headers: { Accept: "application/json" },
    });

    const reviews = Array.isArray(payload?.data) ? payload.data : [];
    if (reviews.length === 0) {
      list.innerHTML = "<p>Chưa có đánh giá</p>";
      return;
    }

    list.innerHTML = reviews
      .map(
        (r) => `
          <article class="review-card">
              <div class="review-head">
                  <div>
                      <b class="review-name">${r.name}</b>
                      <div class="review-stars">
                          ${Array.from({ length: 5 })
                            .map(
                              (_, index) =>
                                `<i class="fa-solid fa-star ${index < Number(r.rating) ? "filled" : ""}"></i>`,
                            )
                            .join("")}
                      </div>
                  </div>
                  <span class="review-date">${r.date}</span>
              </div>
              <p class="review-content">${r.content}</p>
          </article>
        `,
      )
      .join("");
  } catch (_error) {
    list.innerHTML = "<p>Không tải được đánh giá</p>";
  }
}

function loadRelated(product) {
  const list = document.getElementById("relatedList");
  if (!list) return;

  let related = PRODUCT_LIST.filter(
    (p) => p.category === product.category && String(p.id) !== String(product.id),
  );
  if (related.length === 0) {
    related = PRODUCT_LIST.filter((p) => String(p.id) !== String(product.id)).slice(0, 4);
  }

  if (related.length === 0) {
    list.innerHTML = "<p>Không có sản phẩm tương tự</p>";
    return;
  }

  list.innerHTML = related
    .map((p) => {
      const rating = Number(p.rating || 4.8);
      const reviewCount = Number(p.reviewCount || 0);
      const oldPrice = Number(p.oldPrice ?? p.originalPrice ?? 0);
      const hasOldPrice = oldPrice > Number(p.price);
      const discountPercent = hasOldPrice
        ? Math.round(((oldPrice - Number(p.price)) / oldPrice) * 100)
        : 0;
      const badgeHtml = discountPercent > 0 ? `<span class="related-badge">-${discountPercent}%</span>` : "";

      return `
        <div class="related-card" onclick="goDetail('${p.id}')">
            <div class="related-image-wrap">
                ${badgeHtml}
                <img class="related-image" src="${p.image || p.img}" alt="${p.name}" onerror="this.src='https://via.placeholder.com/150'">
            </div>
            <div class="related-info">
                <span class="related-category">${p.category || "San pham"}</span>
                <h4 class="related-name">${p.name}</h4>
                <div class="related-rating-line">
                    <span class="related-rating-star"><i class="fa-solid fa-star"></i></span>
                    <span class="related-rating-value">${rating.toFixed(1)}</span>
                    <span class="related-rating-count">(${reviewCount})</span>
                </div>
                <div class="related-price-line">
                    <div class="related-price">${Number(p.price).toLocaleString()}đ</div>
                    ${hasOldPrice ? `<div class="related-old-price">${oldPrice.toLocaleString()}đ</div>` : ""}
                </div>
                <button type="button" class="related-view-btn" onclick="event.stopPropagation();goDetail('${p.id}')">
                    <i class="fa-solid fa-eye"></i>
                    <span>Xem chi tiết</span>
                </button>
            </div>
        </div>
      `;
    })
    .join("");
}

function renderProduct(product) {
  const nameEl = document.getElementById("name");
  const descEl = document.getElementById("desc");
  const mainImgEl = document.getElementById("mainImg");
  const categoryEl = document.getElementById("category");
  const ratingStarsEl = document.getElementById("ratingStars");
  const ratingValueEl = document.getElementById("ratingValue");
  const reviewCountLabelEl = document.getElementById("reviewCountLabel");
  const priceEl = document.getElementById("price");
  const thumbsEl = document.getElementById("thumbs");

  const ratingValue = Number(product.rating || 0);
  const reviewCount = Number(product.reviewCount || 0);

  if (nameEl) nameEl.innerText = product.name;
  if (descEl) descEl.innerText = product.description || product.desc || "";
  if (mainImgEl) mainImgEl.src = product.image || product.img || "https://via.placeholder.com/300";
  if (categoryEl) categoryEl.innerText = product.category || "San pham";

  if (ratingStarsEl) {
    ratingStarsEl.innerHTML = Array.from({ length: 5 })
      .map(
        (_, index) =>
          `<i class="fa-solid fa-star ${index < Math.floor(ratingValue) ? "filled" : ""}"></i>`,
      )
      .join("");
  }

  if (ratingValueEl) ratingValueEl.innerText = ratingValue.toFixed(1);
  if (reviewCountLabelEl) reviewCountLabelEl.innerText = `(${reviewCount} danh gia)`;

  if (priceEl) {
    const oldPrice = Number(product.oldPrice || product.originalPrice || 0);
    const hasOldPrice = oldPrice > Number(product.price);
    const discountPercent = hasOldPrice
      ? Math.round(((oldPrice - Number(product.price)) / oldPrice) * 100)
      : 0;

    priceEl.innerHTML = `
      <span class="new-price">${Number(product.price).toLocaleString()}đ</span>
      ${hasOldPrice ? `<span class="old-price">${oldPrice.toLocaleString()}đ</span>` : ""}
      ${hasOldPrice ? `<span class="discount-badge">-${discountPercent}%</span>` : ""}
    `;
  }

  if (thumbsEl) {
    const image = product.image || product.img || "https://via.placeholder.com/300";
    thumbsEl.innerHTML = `
      <button type="button" class="thumb-btn active" onclick="changeImg(this, '${image}')">
        <img class="thumb-image" src="${image}" alt="${product.name}" onerror="this.src='https://via.placeholder.com/60'" />
      </button>
    `;
  }

  updateStockUI(product);
  loadRelated(product);
  loadReviews(product.id);
}

function changeImg(target, srcOverride) {
  const mainImg = document.getElementById("mainImg");
  const nextSrc = srcOverride || target?.src || "";

  if (!nextSrc) return;
  if (mainImg) mainImg.src = nextSrc;

  const thumbButtons = document.querySelectorAll(".thumb-btn");
  thumbButtons.forEach((button) => button.classList.remove("active"));

  if (target && target.classList?.contains("thumb-btn")) {
    target.classList.add("active");
    return;
  }

  if (target?.closest) {
    const parentBtn = target.closest(".thumb-btn");
    if (parentBtn) parentBtn.classList.add("active");
  }
}

function increase() {
  const product = findProductById(getProductId());
  const qty = document.getElementById("qty");
  if (!product || !qty) return;

  const value = parseInt(qty.value, 10) || 1;
  if (typeof product.stock === "number") {
    if (value < product.stock) {
      qty.value = value + 1;
      return;
    }

    showToast("Toi da trong kho!", "warning");
    return;
  }

  qty.value = Math.min(value + 1, 99);
}

function decrease() {
  const qty = document.getElementById("qty");
  if (!qty) return;

  const value = parseInt(qty.value, 10) || 1;
  if (value > 1) {
    qty.value = value - 1;
  }
}

async function addToCart() {
  const id = Number(getProductId());
  const product = findProductById(id);
  const qtyInput = document.getElementById("qty");
  const qty = parseInt(qtyInput?.value || "1", 10);

  if (!product || Number.isNaN(qty) || qty < 1) {
    return false;
  }

  try {
    await fetchJson("/backend/api/cart.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        productId: id,
        quantity: qty,
      }),
    });

    await syncCartCount();
    showToast("Đã thêm vào giỏ hàng!", "success");
    return true;
  } catch (error) {
    if (error?.status === 401) {
      showToast("Vui lòng đăng nhập để thêm giỏ hàng", "warning");
      setTimeout(() => {
        window.location.href = "login.php";
      }, 800);
      return false;
    }

    showToast(String(error?.message || "Không thể thêm vào giỏ"), "error");
    return false;
  }
}

async function buyNow() {
  const success = await addToCart();
  if (success) {
    window.location.href = "cart.php";
  }
}

async function handleAddToCart(isBuyNow) {
  if (isBuyNow) {
    await buyNow();
    return;
  }

  await addToCart();
}

function goDetail(id) {
  window.location.href = "detail.php?id=" + id;
}

async function submitReview() {
  const nameInput = document.getElementById("reviewName");
  const contentInput = document.getElementById("reviewContent");

  if (!nameInput || !contentInput) {
    return false;
  }

  const name = nameInput.value.trim();
  const content = contentInput.value.trim();

  if (!name || !content) {
    showToast("Vui lòng nhập đầy đủ!", "warning");
    return false;
  }

  try {
    await fetchJson("/backend/api/reviews.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        productId: Number(getProductId()),
        name,
        content,
        rating: 5,
      }),
    });

    nameInput.value = "";
    contentInput.value = "";
    showToast("Gửi đánh giá thành công", "success");
    await bootstrapDataFromApi();
  } catch (error) {
    showToast(String(error?.message || "Gửi đánh giá thất bại"), "error");
  }

  return false;
}

async function bootstrapDataFromApi() {
  try {
    const payload = await fetchJson("/backend/api/products.php", {
      method: "GET",
      headers: { Accept: "application/json" },
    });

    PRODUCT_LIST = Array.isArray(payload?.data) ? payload.data : [];
    const product = findProductById(getProductId());
    if (!product) {
      showToast("Không tìm thấy sản phẩm", "error");
      return;
    }

    renderProduct(product);
  } catch (_error) {
    showToast("Không tải được dữ liệu sản phẩm", "error");
  }
}

document.addEventListener("DOMContentLoaded", async () => {
  await bootstrapDataFromApi();
  await syncCartCount();

  const qtyInput = document.getElementById("qty");
  if (qtyInput) {
    qtyInput.addEventListener("input", function () {
      const value = parseInt(this.value, 10);
      if (Number.isNaN(value) || value < 1) {
        this.value = 1;
      }
    });
  }
});

window.changeImg = changeImg;
window.increase = increase;
window.decrease = decrease;
window.handleAddToCart = handleAddToCart;
window.goDetail = goDetail;
window.submitReview = submitReview;
