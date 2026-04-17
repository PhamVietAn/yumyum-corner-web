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

let profileUser = null;
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

function showToast(message, type = "success") {
  const toast = document.getElementById("profileToast");
  if (!toast) return;

  toast.innerText = message;
  toast.classList.remove("success", "warning", "error");
  toast.classList.add(type, "show");

  if (toastTimer) clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    toast.classList.remove("show");
  }, 1800);
}

function formatSpentShort(value) {
  const amount = Number(value || 0);
  if (amount >= 1000000) return `${(amount / 1000000).toFixed(1)}M`;
  if (amount >= 1000) return `${Math.round(amount / 1000)}K`;
  return `${amount.toLocaleString("vi-VN")}đ`;
}

async function updateCartCount() {
  try {
    const payload = await fetchJson("/backend/api/cart.php", {
      method: "GET",
      headers: { Accept: "application/json" },
    });
    const list = Array.isArray(payload?.data) ? payload.data : [];
    const cartCount = document.getElementById("cart-count");
    if (!cartCount) return;
    cartCount.innerText = String(list.length);
  } catch (_error) {
    const cartCount = document.getElementById("cart-count");
    if (!cartCount) return;
    cartCount.innerText = "0";
  }
}

function fillForm(user) {
  const fullName = document.getElementById("fullName");
  const email = document.getElementById("email");
  const phone = document.getElementById("phone");
  const address = document.getElementById("address");

  if (fullName) fullName.value = user.fullName || "";
  if (email) email.value = user.email || "";
  if (phone) phone.value = user.phone || "";

  if (address) {
    const detail = user.address?.detail || "";
    const district = user.address?.district || "";
    const city = user.address?.city || "";
    address.value = [detail, district, city].filter(Boolean).join(", ");
  }
}

function renderSidebar(user) {
  const avatarLetter = document.getElementById("avatarLetter");
  const sidebarName = document.getElementById("sidebarName");
  const sidebarEmail = document.getElementById("sidebarEmail");
  const totalOrders = document.getElementById("totalOrders");
  const totalSpent = document.getElementById("totalSpent");

  if (avatarLetter) {
    const firstChar = String(user.fullName || "U").trim().charAt(0).toUpperCase() || "U";
    avatarLetter.innerText = firstChar;
  }

  if (sidebarName) sidebarName.innerText = user.fullName || "Nguoi dung";
  if (sidebarEmail) sidebarEmail.innerText = user.email || "";

  if (totalOrders) totalOrders.innerText = String(user.totalOrders || 0);
  if (totalSpent) totalSpent.innerText = formatSpentShort(user.totalSpent || 0);

  const menuName = document.querySelector(".user-menu-name");
  const dropdownName = document.querySelector(".user-dropdown-name");
  const dropdownEmail = document.querySelector(".user-dropdown-email");

  if (menuName) menuName.textContent = user.fullName || "Tài khoản";
  if (dropdownName) dropdownName.textContent = user.fullName || "Tài khoản";
  if (dropdownEmail) dropdownEmail.textContent = user.email || "";
}

function clearError(input) {
  input?.classList.remove("error");
}

function bindRealtimeClearError() {
  ["fullName", "email", "phone", "address", "currentPassword", "newPassword", "confirmPassword"].forEach((id) => {
    const input = document.getElementById(id);
    input?.addEventListener("input", () => clearError(input));
  });
}

function parseAddress(addressText) {
  const text = String(addressText || "").trim();
  if (!text) {
    return { detail: "", district: "", city: "" };
  }

  return {
    detail: text,
    district: "",
    city: "",
  };
}

function validateProfileForm() {
  let valid = true;
  const fullName = document.getElementById("fullName");
  const email = document.getElementById("email");
  const phone = document.getElementById("phone");

  const fullNameVal = fullName?.value.trim() || "";
  const emailVal = email?.value.trim().toLowerCase() || "";
  const phoneVal = phone?.value.trim() || "";

  if (fullNameVal.length < 2) {
    fullName?.classList.add("error");
    valid = false;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(emailVal)) {
    email?.classList.add("error");
    valid = false;
  }

  if (phoneVal && !/^0\d{9,10}$/.test(phoneVal)) {
    phone?.classList.add("error");
    valid = false;
  }

  return valid;
}

async function handleUpdateProfile(event) {
  event.preventDefault();
  if (!validateProfileForm()) {
    showToast("Vui lòng kiểm tra thông tin", "warning");
    return;
  }

  const fullName = document.getElementById("fullName")?.value.trim() || "";
  const email = document.getElementById("email")?.value.trim().toLowerCase() || "";
  const phone = document.getElementById("phone")?.value.trim() || "";
  const addressText = document.getElementById("address")?.value.trim() || "";
  const address = parseAddress(addressText);

  try {
    await fetchJson("/backend/api/profile.php", {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        fullName,
        email,
        phone,
        address,
      }),
    });

    await bootstrapProfile();
    showToast("Cập nhật thông tin thành công!", "success");
  } catch (error) {
    showToast(String(error?.message || "Cập nhật thất bại"), "error");
  }
}

async function handleChangePassword(event) {
  event.preventDefault();
  const current = document.getElementById("currentPassword");
  const next = document.getElementById("newPassword");
  const confirm = document.getElementById("confirmPassword");

  if (!current || !next || !confirm) return;

  const currentVal = current.value;
  const nextVal = next.value;
  const confirmVal = confirm.value;

  let valid = true;

  if (!currentVal) {
    current.classList.add("error");
    valid = false;
  }

  if (!nextVal || nextVal.length < 8) {
    next.classList.add("error");
    valid = false;
  }

  if (nextVal !== confirmVal) {
    confirm.classList.add("error");
    valid = false;
  }

  if (!valid) {
    showToast("Thông tin đổi mật khẩu chưa hợp lệ", "error");
    return;
  }

  try {
    await fetchJson("/backend/api/profile.php", {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        currentPassword: currentVal,
        newPassword: nextVal,
      }),
    });

    current.value = "";
    next.value = "";
    confirm.value = "";
    showToast("Đổi mật khẩu thành công!", "success");
  } catch (error) {
    showToast(String(error?.message || "Đổi mật khẩu thất bại"), "error");
  }
}

function openDeleteModal() {
  const modal = document.getElementById("deleteModal");
  if (!modal) return;
  modal.classList.remove("hidden");
  modal.setAttribute("aria-hidden", "false");
}

function closeDeleteModal() {
  const modal = document.getElementById("deleteModal");
  if (!modal) return;
  modal.classList.add("hidden");
  modal.setAttribute("aria-hidden", "true");
}

async function handleDeleteAccount() {
  try {
    await fetchJson("/backend/api/profile.php", {
      method: "DELETE",
      headers: { Accept: "application/json" },
    });

    closeDeleteModal();
    showToast("Đã xóa tài khoản", "success");

    setTimeout(() => {
      window.location.href = "login.php";
    }, 650);
  } catch (error) {
    showToast(String(error?.message || "Không thể xóa tài khoản"), "error");
  }
}

async function bootstrapProfile() {
  try {
    const payload = await fetchJson("/backend/api/profile.php", {
      method: "GET",
      headers: { Accept: "application/json" },
    });

    profileUser = payload?.data || null;
    if (!profileUser) {
      window.location.href = "login.php";
      return false;
    }

    fillForm(profileUser);
    renderSidebar(profileUser);
    updateCartCount();
    return true;
  } catch (error) {
    if (error?.status === 401) {
      window.location.href = "login.php";
      return false;
    }

    showToast(String(error?.message || "Không tải được hồ sơ"), "error");
    return false;
  }
}

document.addEventListener("DOMContentLoaded", async () => {
  const canContinue = await bootstrapProfile();
  if (!canContinue) return;

  bindRealtimeClearError();

  document.getElementById("profileForm")?.addEventListener("submit", handleUpdateProfile);
  document.getElementById("passwordForm")?.addEventListener("submit", handleChangePassword);

  const deleteModal = document.getElementById("deleteModal");
  document.getElementById("deleteAccountBtn")?.addEventListener("click", openDeleteModal);
  document.getElementById("cancelDeleteBtn")?.addEventListener("click", closeDeleteModal);
  document.getElementById("confirmDeleteBtn")?.addEventListener("click", handleDeleteAccount);

  deleteModal?.addEventListener("click", (event) => {
    if (event.target === deleteModal) {
      closeDeleteModal();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeDeleteModal();
    }
  });
});
