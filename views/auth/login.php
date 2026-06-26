<section class="auth-page auth-page--login">
  <div class="auth-page__form-panel">
    <div class="auth-page__form-inner">
      <h1 class="auth-page__title">Connexion</h1>

      <form class="auth-page__form" action="index.php?page=login" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="auth-page__field">
          <label class="auth-page__label" for="login-email">Adresse email</label>
          <input
            class="auth-page__input"
            id="login-email"
            name="email"
            type="email"
            value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
            autocomplete="email"
            pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
            required>
        </div>

        <div class="auth-page__field">
          <label class="auth-page__label" for="login-password">Mot de passe</label>
          <input
            class="auth-page__input"
            id="login-password"
            name="password"
            type="password"
            autocomplete="current-password"
            required>
        </div>

        <button class="auth-page__button" type="submit">
          Se connecter
        </button>
      </form>

      <p class="auth-page__switch">
        Pas de compte ?
        <a class="auth-page__switch-link" href="index.php?page=register">Inscrivez-vous</a>
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
