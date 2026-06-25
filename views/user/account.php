<section class="account-page">
  <div class="account-page__inner">
    <h1 class="account-page__title">Mon compte</h1>

    <div class="account-page__grid">
      <article class="account-card account-card--profile">
        <div class="account-card__profile-content">
          <img
            class="account-card__avatar"
            src="<?= htmlspecialchars($user->getProfilePicture() ?? 'assets/images/Alexlecture.png') ?>"
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

        <?php if (!empty($successMessage)): ?>
          <div class="account-card__alert account-card__alert--success" role="status">
            <p><?= htmlspecialchars($successMessage) ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
          <div class="account-card__alert account-card__alert--error" role="alert">
            <?php foreach ($errors as $error): ?>
              <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

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
              required>
          </div>

          <button class="account-card__button" type="submit">
            Enregistrer
          </button>
        </form>
      </article>
    </div>
  </div>
</section>
