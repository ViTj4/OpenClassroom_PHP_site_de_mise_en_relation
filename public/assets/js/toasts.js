const toasts = document.querySelectorAll('[data-toast]');

toasts.forEach((toast) => {
  const closeButton = toast.querySelector('[data-toast-close]');
  const timeoutId = window.setTimeout(() => {
    toast.remove();
  }, 5200);

  closeButton?.addEventListener('click', () => {
    window.clearTimeout(timeoutId);
    toast.remove();
  });
});
