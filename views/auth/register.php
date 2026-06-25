<section class="auth-page auth-page--register">
  <div class="auth-page__form-panel">
    <div class="auth-page__form-inner">
      <h1 class="auth-page__title">Inscription</h1>

      <?php if (!empty($errors)): ?>
        <div class="auth-page__alert auth-page__alert--error" role="alert">
          <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form class="auth-page__form" action="index.php?page=register" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="auth-page__field">
          <label class="auth-page__label" for="register-username">Pseudo</label>
          <input
            class="auth-page__input"
            id="register-username"
            name="username"
            type="text"
            value="<?= htmlspecialchars($formData['username'] ?? '') ?>"
            autocomplete="username"
            required>
        </div>

        <div class="auth-page__field">
          <label class="auth-page__label" for="register-email">Adresse email</label>
          <input
            class="auth-page__input"
            id="register-email"
            name="email"
            type="email"
            value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
            autocomplete="email"
            required>
        </div>

        <div class="auth-page__field">
          <label class="auth-page__label" for="register-password">Mot de passe</label>
          <input
            class="auth-page__input"
            id="register-password"
            name="password"
            type="password"
            autocomplete="new-password"
            required>
        </div>

        <button class="auth-page__button" type="submit">
          S'inscrire
        </button>
      </form>

      <p class="auth-page__switch">
        Déjà inscrit ?
        <a class="auth-page__switch-link" href="index.php?page=login">Connectez-vous</a>
      </p>
    </div>
  </div>

  <div class="auth-page__image-panel" aria-hidden="true">
    <img
      class="auth-page__image"
      src="assets/images/register_pic.png"
      alt="">
  </div>
</section>
