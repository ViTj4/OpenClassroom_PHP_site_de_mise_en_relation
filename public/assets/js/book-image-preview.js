const bookImageInputs = document.querySelectorAll('[data-book-image-input]');

bookImageInputs.forEach((bookImageInput) => {
  const form = bookImageInput.closest('form');
  const bookImagePreview = form?.querySelector('[data-book-image-preview]');

  if (!bookImagePreview) {
    return;
  }

  bookImageInput.addEventListener('change', () => {
    const [file] = bookImageInput.files;

    if (!file) {
      return;
    }

    bookImagePreview.src = URL.createObjectURL(file);
    bookImagePreview.hidden = false;
  });
});
