ensureFreshDataAfterNavigation();

document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");
  const emailInput = document.getElementById("emailInput");
  const passwordInput = document.getElementById("passwordField");
  const passwordToggle = document.getElementById("togglePassword");

  const emailError = document.getElementById("emailError");
  const passwordError = document.getElementById("passwordError");
  const emailGroup = document.getElementById("emailFieldGroup");
  const passwordGroup = document.getElementById("passwordFieldGroup");

  prefillEmailFromStorage();

  emailInput?.addEventListener("input", validateEmailField);
  emailInput?.addEventListener("blur", validateEmailField);
  passwordInput?.addEventListener("input", validatePasswordField);
  passwordInput?.addEventListener("blur", validatePasswordField);

  if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const email = emailInput?.value.trim() || "";
      const pass = passwordInput?.value.trim() || "";

      const isEmailValid = validateEmailField();
      const isPasswordValid = validatePasswordField();

      if (!isEmailValid || !isPasswordValid) {
        return;
      }

      try {
        const payload = await fetchJson("/backend/api/login.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            email,
            password: pass,
          }),
        });

        clearFieldError("email");
        clearFieldError("password");

        const role = String(payload?.data?.role || "user").toLowerCase();
        if (role === "admin") {
          window.location.href = "../admin/OverView.php";
        } else {
          window.location.href = "dashboard.php";
        }
      } catch (error) {
        const message = String(error?.message || "Đăng nhập thất bại");
        if (message.toLowerCase().includes("mật khẩu")) {
          setFieldError("password", message);
        } else {
          setFieldError("email", message);
        }
      }
    });
  }

  if (passwordToggle) {
    passwordToggle.addEventListener("click", function () {
      const isPassword = passwordInput.type === "password";
      passwordInput.type = isPassword ? "text" : "password";
      this.classList.toggle("fa-eye");
      this.classList.toggle("fa-eye-slash");
    });
  }

  const openAuthPopup = (url, title) => {
    const w = 500,
      h = 600;
    const left = (screen.width - w) / 2;
    const top = (screen.height - h) / 2;
    return window.open(
      url,
      title,
      `width=${w},height=${h},top=${top},left=${left}`,
    );
  };

  document.getElementById("btnGoogle")?.addEventListener("click", () => {
    openAuthPopup("https://accounts.google.com/signin", "Google Login");
  });

  document.getElementById("btnFacebook")?.addEventListener("click", () => {
    openAuthPopup("https://www.facebook.com/login", "Facebook Login");
  });

  function validateEmailField() {
    const email = emailInput?.value.trim() || "";
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!email) {
      setFieldError("email", "Không được để trống email");
      return false;
    }

    if (!emailRegex.test(email)) {
      setFieldError("email", "Email không hợp lệ");
      return false;
    }

    clearFieldError("email");
    return true;
  }

  function validatePasswordField() {
    const password = passwordInput?.value || "";

    if (!password) {
      setFieldError("password", "Không được để trống mật khẩu");
      return false;
    }

    if (password.length < 8) {
      setFieldError("password", "Mật khẩu tối thiểu 8 ký tự");
      return false;
    }

    clearFieldError("password");
    return true;
  }

  function setFieldError(field, message) {
    if (field === "email") {
      if (emailError) emailError.innerText = message;
      emailGroup?.classList.add("error");
      return;
    }

    if (field === "password") {
      if (passwordError) passwordError.innerText = message;
      passwordGroup?.classList.add("error");
    }
  }

  function clearFieldError(field) {
    if (field === "email") {
      if (emailError) emailError.innerText = "";
      emailGroup?.classList.remove("error");
      return;
    }

    if (field === "password") {
      if (passwordError) passwordError.innerText = "";
      passwordGroup?.classList.remove("error");
    }
  }

  function prefillEmailFromStorage() {
    const params = new URLSearchParams(window.location.search);
    const email = params.get("email") || "";
    if (!emailInput || !email) return;

    emailInput.value = email;
    validateEmailField();
  }

  async function fetchJson(url, options) {
    const response = await fetch(url, options);
    const payload = await response.json().catch(() => ({}));

    if (!response.ok || payload?.success === false) {
      throw new Error(payload?.message || "Yêu cầu thất bại");
    }

    return payload;
  }
});

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
