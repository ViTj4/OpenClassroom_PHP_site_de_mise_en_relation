<?php
  $currentPage = $page ?? 'home';
  $isHomeActive = $currentPage === 'home';
  $isBooksActive = in_array($currentPage, ['books', 'book'], true);
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
        <li class="header__user-item">
          <a class="header__user-link" href="index.php?page=messages">
            <img
              class="header__message-icon"
              src="assets/svg/messageIcon.svg"
              alt=""
              aria-hidden="true">
            <span>Messagerie</span>
            <span class="header__message-count">1</span>
          </a>
        </li>

        <li class="header__user-item">
          <a class="header__user-link" href="index.php?page=account">
            <img
              class="header__account-icon"
              src="assets/svg/myAccountLogo.svg"
              alt=""
              aria-hidden="true">
            <span>Mon compte</span>
          </a>
        </li>

        <li class="header__user-item">
          <a class="header__user-link" href="index.php?page=login">
            Connexion
          </a>
        </li>
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
