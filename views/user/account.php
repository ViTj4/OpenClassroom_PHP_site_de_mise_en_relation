<section class="account-page">
  <div class="account-page__inner">
    <h1 class="account-page__title">Mon compte</h1>

    <div class="account-page__grid">
      <article class="account-card account-card--profile">
        <div class="account-card__profile-content">
          <img
            class="account-card__avatar"
            src="<?= htmlspecialchars($user->getProfilePicture()) ?>"
            alt="">

          <form
            class="account-card__picture-form"
            data-profile-picture-form
            action="index.php?page=account-picture"
            method="post"
            enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <label class="account-card__edit-picture" for="account-profile-picture">
              modifier
            </label>
            <input
              class="account-card__picture-input"
              data-profile-picture-input
              id="account-profile-picture"
              name="profile_picture"
              type="file"
              accept="image/jpeg,image/png,image/webp">

            <button class="account-card__picture-submit" type="submit">
              Enregistrer la photo
            </button>
          </form>

          <div class="account-card__separator" aria-hidden="true"></div>

          <h2 class="account-card__pseudo">
            <?= htmlspecialchars($user->getPseudo()) ?>
          </h2>

          <p class="account-card__member-since">
            <?= htmlspecialchars($memberSince) ?>
          </p>

          <p class="account-card__library-label">
            Bibliothèque
          </p>

          <p class="account-card__books-count">
            <img
              class="account-card__books-icon"
              src="assets/svg/booksCounter.svg"
              alt=""
              aria-hidden="true">
            <span><?= (int) $booksCount ?> livre<?= $booksCount > 1 ? 's' : '' ?></span>
          </p>
        </div>
      </article>

      <article class="account-card account-card--form">
        <h2 class="account-card__form-title">
          Vos informations personnelles
        </h2>

        <form class="account-card__form" action="index.php?page=account" method="post">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

          <div class="account-card__field">
            <label class="account-card__label" for="account-email">Adresse email</label>
            <input
              class="account-card__input"
              id="account-email"
              name="email"
              type="email"
              value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
              autocomplete="email"
              pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
              required>
          </div>

          <div class="account-card__field">
            <label class="account-card__label" for="account-password">Mot de passe</label>
            <input
              class="account-card__input"
              id="account-password"
              name="password"
              type="password"
              placeholder="••••••••"
              minlength="8"
              pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}"
              title="Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial."
              autocomplete="new-password">
          </div>

          <div class="account-card__field">
            <label class="account-card__label" for="account-pseudo">Pseudo</label>
            <input
              class="account-card__input"
              id="account-pseudo"
              name="pseudo"
              type="text"
              value="<?= htmlspecialchars($formData['pseudo'] ?? '') ?>"
              autocomplete="username"
              minlength="2"
              maxlength="<?= FormValidator::PSEUDO_MAX_LENGTH ?>"
              pattern="[A-Za-z0-9_-]{2,30}"
              required>
          </div>

          <button class="account-card__button" type="submit">
            Enregistrer
          </button>
        </form>
      </article>
    </div>

    <section class="account-books" aria-labelledby="account-books-title">
      <div class="account-books__header">
        <h2 class="account-books__title" id="account-books-title">
          Ma bibliothèque
        </h2>

        <a class="account-books__add-link" href="index.php?page=book-create">
          Ajouter un livre
        </a>
      </div>

      <div class="account-books__table-wrapper">
        <table class="account-books__table">
          <thead>
            <tr>
              <th scope="col">Photo</th>
              <th scope="col">Titre</th>
              <th scope="col">Auteur</th>
              <th scope="col">Description</th>
              <th scope="col">Disponibilité</th>
              <th scope="col">Action</th>
            </tr>
          </thead>

          <tbody>
            <?php if (empty($books)): ?>
              <tr>
                <td class="account-books__empty" colspan="6">
                  Vous n'avez pas encore ajouté de livre.
                </td>
              </tr>
            <?php endif; ?>

            <?php foreach ($books as $book): ?>
              <?php
                $description = $book->getDescription();
                $descriptionExcerpt = strlen($description) > 94
                    ? substr($description, 0, 94) . '...'
                    : $description;
              ?>

              <tr>
                <td>
                  <img
                    class="account-books__image"
                    src="<?= htmlspecialchars($book->getImage()) ?>"
                    alt="<?= htmlspecialchars($book->getAltText()) ?>">
                </td>

                <td><?= htmlspecialchars($book->getTitle()) ?></td>
                <td><?= htmlspecialchars($book->getAuthor()) ?></td>
                <td>
                  <p class="account-books__description">
                    <?= htmlspecialchars($descriptionExcerpt) ?>
                  </p>
                </td>
                <td>
                  <span class="account-books__status account-books__status--<?= htmlspecialchars($book->getStatusCssModifier()) ?>">
                    <?= htmlspecialchars($book->getStatusLabel()) ?>
                  </span>
                </td>
                <td>
                  <div class="account-books__actions">
                    <a class="account-books__edit-link" href="index.php?page=book-edit&amp;uuid=<?= htmlspecialchars($book->getUuid()) ?>">
                      Éditer
                    </a>

                    <a class="account-books__delete-link" href="index.php?page=book-delete&amp;uuid=<?= htmlspecialchars($book->getUuid()) ?>">
                      Supprimer
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</section>
