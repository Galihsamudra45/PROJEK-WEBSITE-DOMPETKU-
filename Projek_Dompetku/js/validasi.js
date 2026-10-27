document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('registerForm');
  const usernameInput = form.username;
  const passwordInput = form.password;
  const roleSelect = form.role;

  form.addEventListener('submit', (e) => {
    clearErrors();
    let valid = true;

    // Validasi username
    if (!usernameInput.value.trim()) {
      showError(usernameInput, 'Username wajib diisi');
      valid = false;
    } else if (usernameInput.value.trim().length < 3) {
      showError(usernameInput, 'Username minimal 3 karakter');
      valid = false;
    }

    // Validasi password
    if (!passwordInput.value) {
      showError(passwordInput, 'Password wajib diisi');
      valid = false;
    } else if (passwordInput.value.length < 6) {
      showError(passwordInput, 'Password minimal 6 karakter');
      valid = false;
    }

    // Validasi role
    if (!roleSelect.value) {
      showError(roleSelect, 'Pilih role terlebih dahulu');
      valid = false;
    }

    if (!valid) {
      e.preventDefault();
    }
  });

  function showError(element, message) {
    // Cari elemen .error-message yang ada tepat setelah elemen input/select
    const errorEl = element.parentElement.querySelector('.error-message');
    if (errorEl) {
      errorEl.textContent = message;
    }
  }

  function clearErrors() {
    form.querySelectorAll('.error-message').forEach(el => el.textContent = '');
  }
});
