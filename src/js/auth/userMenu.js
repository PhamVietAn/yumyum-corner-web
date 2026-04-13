document.addEventListener("DOMContentLoaded", async () => {
  const headerRight = document.querySelector(".header-right");
  if (!headerRight) return;

  if (headerRight.querySelector(".user-menu")) return;

  const loginAnchor = findLoginAnchor(headerRight);
  const authState = await getAuthState();

  if (!authState.isLoggedIn || !authState.user) {
    if (loginAnchor) {
      loginAnchor.setAttribute("href", "/src/pages/auth/login.php");
    }
    return;
  }

  const { fullName, email } = authState.user;

  const wrapper = document.createElement("div");
  wrapper.className = "user-menu";
  wrapper.innerHTML = `
    <button type="button" class="user-menu-toggle" aria-expanded="false" aria-haspopup="true">
      <i class="fa-regular fa-user"></i>
      <span class="user-menu-name">${escapeHtml(fullName || "Tài khoản")}</span>
      <i class="fa-solid fa-chevron-down user-menu-arrow"></i>
    </button>

    <div class="user-dropdown" role="menu" aria-label="Tài khoản">
      <div class="user-dropdown-header">
        <p class="user-dropdown-name">${escapeHtml(fullName || "Tài khoản")}</p>
        <p class="user-dropdown-email">${escapeHtml(email || "")}</p>
      </div>

      <a class="user-dropdown-item" href="/src/pages/auth/profile.php" role="menuitem">
        <i class="fa-solid fa-gear"></i>
        <span>Sửa thông tin</span>
      </a>

      <a class="user-dropdown-item" href="/src/pages/auth/orders.php" role="menuitem">
        <i class="fa-regular fa-clipboard"></i>
        <span>Đơn hàng của tôi</span>
      </a>

      <button type="button" class="user-dropdown-item danger" id="logoutButton" role="menuitem">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
        <span>Đăng xuất</span>
      </button>
    </div>
  `;

  if (loginAnchor) {
    loginAnchor.replaceWith(wrapper);
  } else {
    headerRight.appendChild(wrapper);
  }

  const toggleButton = wrapper.querySelector(".user-menu-toggle");
  const logoutButton = wrapper.querySelector("#logoutButton");

  toggleButton?.addEventListener("click", (event) => {
    event.stopPropagation();
    toggleMenu(wrapper, toggleButton);
  });

  logoutButton?.addEventListener("click", async () => {
    try {
      await fetch("/backend/api/logout.php", {
        method: "POST",
      });
    } catch (_error) {
      // Ignore network/logout errors and still navigate to login.
    }

    window.location.href = "/src/pages/auth/login.php";
  });

  document.addEventListener("click", (event) => {
    if (!wrapper.contains(event.target)) {
      closeMenu(wrapper, toggleButton);
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeMenu(wrapper, toggleButton);
    }
  });
});

function findLoginAnchor(container) {
  const anchors = Array.from(container.querySelectorAll("a"));
  return anchors.find((anchor) => {
    const icon = anchor.querySelector("i");
    if (!icon) return false;
    return icon.classList.contains("fa-user");
  });
}

async function getAuthState() {
  try {
    const response = await fetch("/backend/api/session.php", {
      method: "GET",
      headers: {
        Accept: "application/json",
      },
    });

    const payload = await response.json().catch(() => ({}));
    if (!response.ok || payload?.success === false) {
      return {
        isLoggedIn: false,
        user: null,
      };
    }

    return {
      isLoggedIn: Boolean(payload?.isLoggedIn),
      user: payload?.data || null,
    };
  } catch (_error) {
    return {
      isLoggedIn: false,
      user: null,
    };
  }
}

function toggleMenu(wrapper, toggleButton) {
  const isOpen = wrapper.classList.toggle("open");
  toggleButton?.setAttribute("aria-expanded", isOpen ? "true" : "false");
}

function closeMenu(wrapper, toggleButton) {
  wrapper.classList.remove("open");
  toggleButton?.setAttribute("aria-expanded", "false");
}

function escapeHtml(input) {
  return String(input)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}
