<?php
  $currentPage = $page ?? 'home';
  $isHomeActive = $currentPage === 'home';
  $isBooksActive = in_array($currentPage, ['books', 'book'], true);
  $isMessagesActive = $currentPage === 'messages';
  $isLoginActive = in_array($currentPage, ['login', 'register'], true);
  $isAccountActive = $currentPage === 'account';
  $isAdminActive = $currentPage === 'admin';
  $connectedUser = $_SESSION['user'] ?? null;
  $isConnectedAdmin = ($connectedUser['userType'] ?? null) === 'admin';
  $connectedUserPicture = $connectedUser['profilePicture'] ?? UserManager::DEFAULT_PROFILE_PICTURE;
  $connectedUserPseudo = $connectedUser['pseudo'] ?? 'lecteur';
?>

<header class="header">
  <div class="header__container">
    <a class="header__logo-link" href="index.php?page=home" aria-label="Retour à l'accueil Tom Troc">
      <img
        class="header__logo header__logo--desktop"
        src="assets/svg/logo.svg"
        alt="Logo Tom Troc">
      <img
        class="header__logo header__logo--mobile"
        src="assets/mobile/svg/mobileHeaderLogo.svg"
        alt="Logo Tom Troc">
    </a>

    <nav class="header__nav" id="header-main-navigation" aria-label="Navigation principale">
      <ul class="header__nav-list">
        <li class="header__nav-item">
          <a
            class="header__nav-link<?= $isHomeActive ? ' header__nav-link--active' : '' ?>"
            href="index.php?page=home"
            <?= $isHomeActive ? 'aria-current="page"' : '' ?>>
            Accueil
          </a>
        </li>

        <li class="header__nav-item">
          <a
            class="header__nav-link<?= $isBooksActive ? ' header__nav-link--active' : '' ?>"
            href="index.php?page=books"
            <?= $isBooksActive ? 'aria-current="page"' : '' ?>>
            Nos livres à l'échange
          </a>
        </li>
      </ul>
    </nav>

    <div class="header__separator" aria-hidden="true"></div>

    <nav class="header__user-nav" id="header-user-navigation" aria-label="Navigation utilisateur">
      <ul class="header__user-list">
        <?php if ($isConnectedAdmin): ?>
          <li class="header__user-item">
            <a
              class="header__user-link<?= $isAdminActive ? ' header__user-link--active' : '' ?>"
              href="index.php?page=admin"
              <?= $isAdminActive ? 'aria-current="page"' : '' ?>>
              Admin
            </a>
          </li>
        <?php endif; ?>

        <?php if ($connectedUser !== null): ?>
          <li class="header__user-item">
            <a
              class="header__user-link<?= $isMessagesActive ? ' header__user-link--active' : '' ?>"
              href="index.php?page=messages"
              <?= $isMessagesActive ? 'aria-current="page"' : '' ?>>
              <img
                class="header__message-icon"
                src="assets/svg/messageIcon.svg"
                alt=""
                aria-hidden="true">
              <span>Messagerie</span>
              <span class="header__message-count">1</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if ($connectedUser !== null): ?>
          <li class="header__user-item">
            <a
              class="header__user-link<?= $isAccountActive ? ' header__user-link--active' : '' ?>"
              href="index.php?page=account"
              <?= $isAccountActive ? 'aria-current="page"' : '' ?>>
              <img
                class="header__account-icon"
                src="assets/svg/myAccountLogo.svg"
                alt=""
                aria-hidden="true">
              <span>Mon compte</span>
            </a>
          </li>
        <?php endif; ?>

        <li class="header__user-item">
          <?php if ($connectedUser !== null): ?>
            <div class="header__account-menu">
              <a class="header__account-trigger" href="index.php?page=account" aria-label="Ouvrir le menu utilisateur">
                <img
                  class="header__profile-picture"
                  src="<?= htmlspecialchars($connectedUserPicture) ?>"
                  alt="">
              </a>

              <div class="header__dropdown" aria-label="Menu utilisateur">
                <p class="header__dropdown-title">
                  Bonjour <?= htmlspecialchars($connectedUserPseudo) ?>
                </p>
                <a class="header__dropdown-link" href="index.php?page=logout">
                  Se déconnecter
                </a>
              </div>
            </div>
          <?php else: ?>
            <a
              class="header__user-link<?= $isLoginActive ? ' header__user-link--active' : '' ?>"
              href="index.php?page=login"
              <?= $isLoginActive ? 'aria-current="page"' : '' ?>>
              Connexion
            </a>
          <?php endif; ?>
        </li>

        <?php if ($connectedUser !== null): ?>
          <li class="header__user-item header__logout-mobile-item">
            <a class="header__user-link" href="index.php?page=logout">
              Se déconnecter
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>

    <button
      class="header__burger-button"
      type="button"
      aria-label="Ouvrir le menu"
      aria-controls="header-main-navigation header-user-navigation"
      aria-expanded="false">
      <img
        class="header__burger-icon"
        src="assets/mobile/svg/burgerMenu.svg"
        alt=""
        aria-hidden="true">
    </button>
  </div>
</header>
