const profilePictureForm = document.querySelector('[data-profile-picture-form]');
const profilePictureInput = profilePictureForm?.querySelector('[data-profile-picture-input]');

if (profilePictureForm && profilePictureInput) {
  profilePictureInput.addEventListener('change', () => {
    if (profilePictureInput.files.length > 0) {
      profilePictureForm.submit();
    }
  });
}
