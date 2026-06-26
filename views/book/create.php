<section class="book-form-page">
  <div class="book-form-page__inner">
    <a class="book-form-page__back-link" href="index.php?page=account">
      ← retour
    </a>

    <h1 class="book-form-page__title">
      Ajouter un livre
    </h1>

    <form class="book-form" action="index.php?page=book-create" method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

      <div class="book-form__field">
        <label class="book-form__label" for="book-title">Titre</label>
        <input
          class="book-form__input"
          id="book-title"
          name="title"
          type="text"
          value="<?= htmlspecialchars($formData['title'] ?? '') ?>"
          maxlength="<?= FormValidator::BOOK_TITLE_MAX_LENGTH ?>"
          required>
      </div>

      <div class="book-form__field">
        <label class="book-form__label" for="book-author">Auteur</label>
        <input
          class="book-form__input"
          id="book-author"
          name="author"
          type="text"
          value="<?= htmlspecialchars($formData['author'] ?? '') ?>"
          maxlength="<?= FormValidator::BOOK_AUTHOR_MAX_LENGTH ?>"
          required>
      </div>

      <div class="book-form__field book-form__field--image">
        <input
          class="book-form__file-input"
          id="book-image"
          name="image"
          type="file"
          accept="image/jpeg,image/png,image/webp"
          data-book-image-input>

        <label class="book-form__image-picker" for="book-image">
          <img
            class="book-form__image-preview"
            alt=""
            data-book-image-preview
            hidden>
          <span class="book-form__image-icon" aria-hidden="true">
            <img class="book-form__image-svg" src="assets/svg/picture.svg" alt="">
          </span>
          <span class="book-form__image-text">Ajouter une photo</span>
        </label>
      </div>

      <div class="book-form__field">
        <label class="book-form__label" for="book-description">Commentaire</label>
        <textarea
          class="book-form__textarea"
          id="book-description"
          name="description"
          maxlength="<?= FormValidator::BOOK_DESCRIPTION_MAX_LENGTH ?>"
          required><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
      </div>

      <button class="book-form__button" type="submit">
        Ajouter le livre
      </button>
    </form>
  </div>
</section>
