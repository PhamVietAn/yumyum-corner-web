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

const list = document.getElementById("list-products");

let PRODUCT_LIST = getNormalizedProducts();
let currentList = [...PRODUCT_LIST];
let currentCategory = "all";

function renderProducts(arr){
    if (!list) return;
    list.innerHTML="";
    if (arr.length === 0) {
        list.innerHTML = `<p style="grid-column: 1/-1; text-align: center;">Không tìm thấy món nào!</p>`;
        return;
    }
    arr.forEach(p=>{
        const imageSrc = p.img || p.image || "";
        const rating = Number(p.rating || 4.8);
        const reviewCount = Number(p.reviewCount || 0);
        const oldPrice = Number(p.oldPrice || p.originalPrice || 0);
        const hasOldPrice = oldPrice > Number(p.price);
        const discountPercent = hasOldPrice
            ? Math.round(((oldPrice - Number(p.price)) / oldPrice) * 100)
            : 0;
        const badgeHtml =
            discountPercent > 0
                ? `<span class="badge">-${discountPercent}%</span>`
                : "";
        const categoryLabel = p._categoryLabel || toCategoryLabel(p.category, p._categoryCode);

        list.innerHTML += `
        <div class="products-card">
            <div class="product-image-wrap">
                ${badgeHtml}
                <img class="product-image" src="${imageSrc}" alt="${p.name}">
            </div>
            <div class="product-info">
                <span class="product-category">${categoryLabel}</span>
                <div class="product-name">${p.name}</div>
                <div class="rating-line">
                    <span class="rating-star"><i class="fa-solid fa-star"></i></span>
                    <span class="rating-value">${rating.toFixed(1)}</span>
                    <span class="rating-count">(${reviewCount})</span>
                </div>
                <div class="price-line">
                    <div class="price1">${Number(p.price).toLocaleString()}đ</div>
                    ${hasOldPrice ? `<div class="old-price-card">${oldPrice.toLocaleString()}đ</div>` : ""}
                </div>
                <a class="add-btn" href="detail.php?id=${p.id}">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Thêm vào giỏ</span>
                </a>
            </div>
        </div>
        `;
    });
}
renderProducts(PRODUCT_LIST);
renderCategoryFilters(PRODUCT_LIST);
bootstrapProducts();
syncCartCount();

let searchInput = document.getElementById("search");
if(searchInput){
    searchInput.addEventListener("keyup", function(){
        let keyword = this.value.toLowerCase();
        let filtered = PRODUCT_LIST.filter(sp =>
            sp.name.toLowerCase().includes(keyword)
        );
        currentList = filtered;
        renderProducts(filtered);
    });
}
/* filter danh mục */
function filterCategory(category,el){
    document.querySelectorAll(".filter-item").forEach(i => i.classList.remove("active"));
    el.classList.add("active");
    currentCategory = category;
    if(category == "all"){
        currentList = [...PRODUCT_LIST];
    } else {
        currentList = PRODUCT_LIST.filter(sp => (sp._categoryKey || sp._categoryCode || "") === category);
    }
    renderProducts(currentList);
    return;
}

// Alias để tương thích với inline handler hiện tại: onclick="filterCate(...)"
function filterCate(category, el) {
    filterCategory(category, el);
}
/* filter giá */
let range = document.getElementById("priceRange");
if(range){
    range.addEventListener("input", function(){
    let value = Number(this.value);
    document.getElementById("priceValue").innerText = value.toLocaleString();
    let filteredByPrice = currentList.filter(sp => sp.price <= value);
    renderProducts(filteredByPrice);
    });
}

/* sort giá */
function sortPrice(type) {
    if (type === "") return;
    let sortedList = [...currentList];
    if (type === "asc") {
        sortedList.sort((a, b) => a.price - b.price);
    } else if (type === "desc") {
        sortedList.sort((a, b) => b.price - a.price);
    }
    renderProducts(sortedList);
}
function handleSubscribe(){
    const emailInput = document.getElementById("emailInput");
    const message = document.getElementById("message");

    if (!emailInput || !message) return;

    const email = emailInput.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    message.textContent = "";
    message.className = "message";
    if(email === ""){
        message.textContent = "Vui lòng nhập email!";
        message.classList.add("error");
        return;
    }
    if(!emailRegex.test(email)){
        message.textContent = "Email không hợp lệ!";
        message.classList.add("error");
        return;
    }
    message.textContent = "Cảm ơn bạn! Vui lòng đăng ký tài khoản để nhận ưu đãi.";
    message.classList.add("success");
    setTimeout(() => {
        window.location.href = "register.php?email=" + encodeURIComponent(email);
    }, 1200);
}

function getNormalizedProducts() {
    const globalProducts =
        (typeof PRODUCTS !== "undefined" && Array.isArray(PRODUCTS) && PRODUCTS) ||
        (typeof products !== "undefined" && Array.isArray(products) && products) ||
        (typeof globalThis !== "undefined" && Array.isArray(globalThis.PRODUCTS) && globalThis.PRODUCTS) ||
        (typeof globalThis !== "undefined" && Array.isArray(globalThis.products) && globalThis.products) ||
        [];

    return normalizeProducts(globalProducts);
}

function normalizeProducts(sourceProducts) {
    return sourceProducts.map((product) => ({
        ...product,
        img: product.img || product.image || "",
        category: product.category || "",
        _categoryCode: toCategoryCode(product.category),
        _categoryKey: toCategoryKey(product),
        _categoryLabel: toCategoryLabel(product.category, toCategoryCode(product.category)),
    }));
}

async function bootstrapProducts() {
    await bootstrapProductsFromDatabase();
}

async function bootstrapProductsFromDatabase() {
    try {
        const response = await fetch("/backend/api/products.php", {
            headers: {
                Accept: "application/json",
            },
        });

        if (!response.ok) return;

        const payload = await response.json();
        const dbProducts = Array.isArray(payload?.data) ? payload.data : [];

        if (!payload?.success || dbProducts.length === 0) return;

        PRODUCT_LIST = normalizeProducts(dbProducts);
        currentList = [...PRODUCT_LIST];
        if (currentCategory !== "all") {
            currentList = PRODUCT_LIST.filter((sp) => (sp._categoryKey || sp._categoryCode || "") === currentCategory);
        }
        renderProducts(currentList);
        renderCategoryFilters(PRODUCT_LIST);
    } catch (_error) {
        // Keep existing UI if API cannot be reached.
    }
}

function renderCategoryFilters(products) {
    const container = document.getElementById("categoryFilters");
    if (!container) return;

    const categoryMap = new Map();
    products.forEach((product) => {
        const key = product._categoryKey || product._categoryCode;
        if (!key || key === "all") return;
        const label = product._categoryLabel || toCategoryLabel(product.category, product._categoryCode);
        if (!categoryMap.has(key)) {
            categoryMap.set(key, label);
        }
    });

    const categories = [...categoryMap.entries()].sort((a, b) => a[1].localeCompare(b[1], "vi"));

    container.innerHTML = `
        <div class="filter-item ${currentCategory === "all" ? "active" : ""}" data-category="all">Tất cả</div>
        ${categories
            .map(
                ([key, label]) =>
                    `<div class="filter-item ${currentCategory === key ? "active" : ""}" data-category="${key}">${label}</div>`,
            )
            .join("")}
    `;

    container.querySelectorAll(".filter-item").forEach((item) => {
        item.addEventListener("click", () => {
            const category = item.getAttribute("data-category") || "all";
            filterCategory(category, item);
        });
    });
}

function toCategoryKey(product) {
    const id = Number(product?.categoryId || 0);
    if (Number.isInteger(id) && id > 0) {
        return `cat-${id}`;
    }

    const code = toCategoryCode(product?.category);
    return code ? `cat-${code}` : "cat-other";
}

async function syncCartCount() {
    const cartCount = document.getElementById("cart-count");
    if (!cartCount) return;

    try {
        const response = await fetch("/backend/api/cart.php", {
            headers: {
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            cartCount.innerText = "0";
            return;
        }

        const payload = await response.json();
        const items = Array.isArray(payload?.data) ? payload.data : [];
        cartCount.innerText = String(items.length);
    } catch (_error) {
        cartCount.innerText = "0";
    }
}

function toCategoryCode(categoryValue) {
    const value = String(categoryValue || "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/\p{Diacritic}/gu, "")
        .replace(/[^a-z0-9\s-]/g, " ")
        .trim();

    if (value.includes("snack")) return "snack";
    if (value.includes("drink") || value.includes("do uong") || /\buong\b/.test(value)) return "drink";
    if (value.includes("fruit") || value.includes("trai") || value.includes("say")) return "fruit";
    if (value.includes("cake") || value.includes("keo") || value.includes("banh")) return "cake";
    if (value.includes("seed") || value.includes("hat") || value.includes("nut")) return "seed";
    if (value.includes("ice") || value.includes("kem") || value.includes("dong lanh")) return "ice";

    return value;
}

function toCategoryLabel(rawCategory, code) {
    const normalizedCode = code || toCategoryCode(rawCategory);

    if (normalizedCode === "snack") return "Snack";
    if (normalizedCode === "drink") return "Đồ uống";
    if (normalizedCode === "fruit") return "Trái cây sấy";
    if (normalizedCode === "cake") return "Kẹo & Bánh";
    if (normalizedCode === "seed") return "Hạt dinh dưỡng";
    if (normalizedCode === "ice") return "Kem & Đông lạnh";

    return String(rawCategory || "Sản phẩm");
}
