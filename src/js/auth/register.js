ensureFreshDataAfterNavigation();

document.addEventListener("DOMContentLoaded", () => {
	const registerForm = document.getElementById("registerForm");
	const nameInput = document.getElementById("name");
	const emailInput = document.getElementById("email");
	const passwordInput = document.getElementById("password");
	const confirmInput = document.getElementById("confirmPassword");
	const submitBtn = registerForm?.querySelector('button[type="submit"]');
	const successMsg = document.getElementById("successMsg");

	const fields = {
		name: {
			input: nameInput,
			error: document.getElementById("nameError"),
			group: document.getElementById("nameFieldGroup"),
		},
		email: {
			input: emailInput,
			error: document.getElementById("emailError"),
			group: document.getElementById("emailFieldGroup"),
		},
		password: {
			input: passwordInput,
			error: document.getElementById("passwordError"),
			group: document.getElementById("passwordFieldGroup"),
		},
		confirm: {
			input: confirmInput,
			error: document.getElementById("confirmError"),
			group: document.getElementById("confirmFieldGroup"),
		},
	};

	if (!registerForm || !nameInput || !emailInput || !passwordInput || !confirmInput) {
		return;
	}

	prefillEmailFromStorage();

	bindRealtimeValidation();
	bindSocialButtons();

	registerForm.addEventListener("submit", async (event) => {
		event.preventDefault();

		const isNameValid = validateName();
		const isEmailValid = validateEmail(true);
		const isPasswordValid = validatePassword();
		const isConfirmValid = validateConfirm();

		if (!(isNameValid && isEmailValid && isPasswordValid && isConfirmValid)) {
			return;
		}

		const normalizedEmail = emailInput.value.trim().toLowerCase();
		try {
			await fetchJson("/backend/api/register.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
				},
				body: JSON.stringify({
					fullName: nameInput.value.trim(),
					email: normalizedEmail,
					password: passwordInput.value,
				}),
			});

			if (successMsg) {
				successMsg.innerText = "Đăng ký thành công! Đang chuyển sang trang đăng nhập...";
			}

			if (submitBtn) submitBtn.disabled = true;

			setTimeout(() => {
				window.location.href = "login.php?email=" + encodeURIComponent(normalizedEmail);
			}, 1200);
		} catch (error) {
			setFieldError("email", String(error?.message || "Đăng ký thất bại"));
		}
	});

	function bindRealtimeValidation() {
		nameInput.addEventListener("input", validateName);
		nameInput.addEventListener("blur", validateName);

		emailInput.addEventListener("input", () => validateEmail(false));
		emailInput.addEventListener("blur", () => validateEmail(true));

		passwordInput.addEventListener("input", () => {
			validatePassword();
			validateConfirm();
		});
		passwordInput.addEventListener("blur", validatePassword);

		confirmInput.addEventListener("input", validateConfirm);
		confirmInput.addEventListener("blur", validateConfirm);
	}

	function bindSocialButtons() {
		const openAuthPopup = (url, title) => {
			const width = 500;
			const height = 650;
			const left = (window.screen.width - width) / 2;
			const top = (window.screen.height - height) / 2;

			return window.open(
				url,
				title,
				`width=${width},height=${height},top=${top},left=${left}`,
			);
		};

		document.getElementById("btnGoogleRegister")?.addEventListener("click", () => {
			openAuthPopup("https://accounts.google.com/signin", "Google Register");
		});

		document.getElementById("btnFacebookRegister")?.addEventListener("click", () => {
			openAuthPopup("https://www.facebook.com/login", "Facebook Register");
		});
	}

	function validateName() {
		const value = nameInput.value.trim();

		if (!value) {
			setFieldError("name", "Không được để trống họ và tên");
			return false;
		}

		if (value.length < 2) {
			setFieldError("name", "Họ và tên tối thiểu 2 ký tự");
			return false;
		}

		const nameRegex = /^[A-Za-zÀ-ỹ\s]+$/u;
		if (!nameRegex.test(value)) {
			setFieldError("name", "Họ và tên chỉ gồm chữ cái và khoảng trắng");
			return false;
		}

		clearFieldError("name");
		return true;
	}

	function validateEmail(checkDuplicate) {
		const value = emailInput.value.trim().toLowerCase();
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

		if (!value) {
			setFieldError("email", "Không được để trống email");
			return false;
		}

		if (!emailRegex.test(value)) {
			setFieldError("email", "Email không hợp lệ");
			return false;
		}

		if (checkDuplicate) {
			if (successMsg) {
				successMsg.innerText = "";
			}
		}

		clearFieldError("email");
		return true;
	}

	function validatePassword() {
		const value = passwordInput.value;

		if (!value) {
			setFieldError("password", "Không được để trống mật khẩu");
			return false;
		}

		if (value.length < 8) {
			setFieldError("password", "Mật khẩu tối thiểu 8 ký tự");
			return false;
		}

		if (!/[A-Z]/.test(value) || !/[a-z]/.test(value) || !/\d/.test(value)) {
			setFieldError("password", "Mật khẩu cần có chữ hoa, chữ thường và số");
			return false;
		}

		clearFieldError("password");
		return true;
	}

	function validateConfirm() {
		const value = confirmInput.value;

		if (!value) {
			setFieldError("confirm", "Không được để trống xác nhận mật khẩu");
			return false;
		}

		if (value !== passwordInput.value) {
			setFieldError("confirm", "Xác nhận mật khẩu không khớp");
			return false;
		}

		clearFieldError("confirm");
		return true;
	}

	function setFieldError(key, message) {
		const field = fields[key];
		if (!field) return;

		if (field.error) field.error.innerText = message;
		if (field.group) field.group.classList.add("error");
		if (successMsg) successMsg.innerText = "";
	}

	function clearFieldError(key) {
		const field = fields[key];
		if (!field) return;

		if (field.error) field.error.innerText = "";
		if (field.group) field.group.classList.remove("error");
	}

	function prefillEmailFromStorage() {
		const params = new URLSearchParams(window.location.search);
		const email = params.get("email") || "";
		if (email && !emailInput.value) {
			emailInput.value = email;
			validateEmail(false);
		}
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
