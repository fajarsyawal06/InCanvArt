document.addEventListener("DOMContentLoaded", () => {
  function setupToggle(btnId, inputId, openId, closedId) {
    const btn = document.getElementById(btnId);
    const input = document.getElementById(inputId);
    const eyeOpen = document.getElementById(openId);
    const eyeClosed = document.getElementById(closedId);

    if (!btn || !input) return;

    btn.addEventListener("click", () => {
      const isHidden = input.type === "password";
      input.type = isHidden ? "text" : "password";
      eyeOpen.classList.toggle("hidden", isHidden);
      eyeClosed.classList.toggle("hidden", !isHidden);
    });
  }

  // Setup masing-masing field
  setupToggle("togglePassword1", "password", "eyeOpen1", "eyeClosed1");
  setupToggle("togglePassword2", "password_confirmation", "eyeOpen2", "eyeClosed2");
});
