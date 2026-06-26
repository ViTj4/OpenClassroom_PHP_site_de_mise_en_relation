<section class="book-edit-page">
  <div class="book-edit-page__inner">
    <a class="book-edit-page__back-link" href="index.php?page=account">
      ← retour
    </a>

    <h1 class="book-edit-page__title">
      Modifier les informations
    </h1>

    <form class="book-edit" action="index.php?page=book-edit" method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
      <input type="hidden" name="uuid" value="<?= htmlspecialchars($book->getUuid()) ?>">

      <div class="book-edit__picture-column">
        <p class="book-edit__label book-edit__label--static">Photo</p>

        <img
          class="book-edit__image"
          data-book-image-preview
          src="<?= htmlspecialchars($book->getImage()) ?>"
          alt="<?= htmlspecialchars($book->getAltText()) ?>">

        <label class="book-edit__picture-link" for="book-image">
          Modifier la photo
        </label>

        <input
          class="book-edit__picture-input"
          id="book-image"
          name="image"
          type="file"
          accept="image/jpeg,image/png,image/webp"
          data-book-image-input>
      </div>

      <div class="book-edit__fields">
        <div class="book-edit__field">
          <label class="book-edit__label" for="book-title">Titre</label>
          <input
            class="book-edit__input"
            id="book-title"
            name="title"
            type="text"
            value="<?= htmlspecialchars($formData['title'] ?? '') ?>"
            maxlength="<?= FormValidator::BOOK_TITLE_MAX_LENGTH ?>"
            required>
        </div>

        <div class="book-edit__field">
          <label class="book-edit__label" for="book-author">Auteur</label>
          <input
            class="book-edit__input"
            id="book-author"
            name="author"
            type="text"
            value="<?= htmlspecialchars($formData['author'] ?? '') ?>"
            maxlength="<?= FormValidator::BOOK_AUTHOR_MAX_LENGTH ?>"
            required>
        </div>

        <div class="book-edit__field">
          <label class="book-edit__label" for="book-description">Commentaire</label>
          <textarea
            class="book-edit__textarea"
            id="book-description"
            name="description"
            maxlength="<?= FormValidator::BOOK_DESCRIPTION_MAX_LENGTH ?>"
            required><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
        </div>

        <div class="book-edit__field">
          <label class="book-edit__label" for="book-status">Disponibilité</label>
          <select class="book-edit__select" id="book-status" name="status">
            <option value="available" <?= ($formData['status'] ?? '') === 'available' ? 'selected' : '' ?>>
              disponible
            </option>
            <option value="reserved" <?= ($formData['status'] ?? '') === 'reserved' ? 'selected' : '' ?>>
              non dispo.
            </option>
            <option value="exchanged" <?= ($formData['status'] ?? '') === 'exchanged' ? 'selected' : '' ?>>
              échangé
            </option>
            <option value="removed" <?= ($formData['status'] ?? '') === 'removed' ? 'selected' : '' ?>>
              retiré
            </option>
          </select>
        </div>

        <button class="book-edit__button" type="submit">
          Valider
        </button>
      </div>
    </form>
  </div>
</section>
