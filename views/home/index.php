<section class="home-hero">
  <div class="home-hero__inner">
    <div class="home-hero__content">
      <h1 class="home-hero__title">
        Rejoignez nos
        <span class="home-hero__break home-hero__break--desktop"><br></span>
        <span class="home-hero__space home-hero__space--mobile"> </span>
        lecteurs
        <span class="home-hero__break home-hero__break--mobile"><br></span>
        <span class="home-hero__space home-hero__space--desktop"> </span>
        passionnés
      </h1>

      <p class="home-hero__text">
        Donnez une nouvelle vie à vos livres en les
        échangeant avec d'autres amoureux de la lecture.
        Nous croyons en la magie du partage de connaissances et d'histoires à travers
        les livres.
      </p>

      <a class="home-hero__button" href="index.php?page=books">
        Découvrir
      </a>
    </div>

    <figure class="home-hero__figure">
      <img
        class="home-hero__image"
        src="assets/images/hamza-nouasria-home-picture.jpg"
        alt="Lecteur assis devant une librairie remplie de livres">

      <figcaption class="home-hero__caption">
        Hamza
      </figcaption>
    </figure>
  </div>
</section>

<section class="latest-books" aria-labelledby="latest-books-title">
  <div class="latest-books__inner">
    <h2 class="latest-books__title" id="latest-books-title">
      Les derniers livres ajoutés
    </h2>

    <div class="latest-books__grid">
      <?php foreach ($latestBooks as $book) : ?>
        <a class="latest-books__card" href="index.php?page=book&amp;uuid=<?= htmlspecialchars($book->getUuid()) ?>">
          <img
            class="latest-books__image"
            src="<?= htmlspecialchars($book->getImage()) ?>"
            alt="<?= htmlspecialchars($book->getAltText()) ?>">

          <span class="latest-books__body">
            <span class="latest-books__book-title">
              <?= htmlspecialchars($book->getTitle()) ?>
            </span>

            <span class="latest-books__author">
              <?= htmlspecialchars($book->getAuthor()) ?>
            </span>

            <span class="latest-books__seller">
              Vendu par : <?= htmlspecialchars($book->getOwnerPseudo()) ?>
            </span>
          </span>
        </a>
      <?php endforeach; ?>
    </div>

    <a class="latest-books__button" href="index.php?page=books">
      Voir tous les livres
    </a>
  </div>
</section>

<section class="how-it-works" aria-labelledby="how-it-works-title">
  <div class="how-it-works__inner">
    <h2 class="how-it-works__title" id="how-it-works-title">
      Comment ça marche ?
    </h2>

    <p class="how-it-works__text">
      Échanger des livres avec TomTroc c'est simple et amusant ! Suivez ces étapes pour commencer :
    </p>

    <div class="how-it-works__grid">
      <article class="how-it-works__card">
        <p class="how-it-works__card-text">
          Inscrivez-vous gratuitement sur notre plateforme.
        </p>
      </article>

      <article class="how-it-works__card">
        <p class="how-it-works__card-text">
          Ajoutez les livres que vous souhaitez échanger à votre profil.
        </p>
      </article>

      <article class="how-it-works__card">
        <p class="how-it-works__card-text">
          Parcourez les livres disponibles chez d'autres membres.
        </p>
      </article>

      <article class="how-it-works__card">
        <p class="how-it-works__card-text">
          Proposez un échange et discutez avec d'autres passionnés de lecture.
        </p>
      </article>
    </div>

    <a class="how-it-works__button" href="index.php?page=books">
      Voir tous les livres
    </a>
  </div>
</section>

<section class="home-values" aria-labelledby="home-values-title">
  <img
    class="home-values__image"
    src="assets/images/library_pic_last_section.webp"
    alt="Personne qui parcourt les rayons d'une bibliothèque">

  <div class="home-values__body">
    <div class="home-values__copy">
      <h2 class="home-values__title" id="home-values-title">
        Nos valeurs
      </h2>

      <div class="home-values__text">
        <p>
          Chez Tom Troc, nous mettons l'accent sur le partage, la découverte et la communauté. Nos valeurs sont ancrées dans notre passion pour les livres et notre désir de créer des liens entre les lecteurs. Nous croyons en la puissance des histoires pour rassembler les gens et inspirer des conversations enrichissantes.
        </p>

        <p>
          Notre association a été fondée avec une conviction profonde : chaque livre mérite d'être lu et partagé.
        </p>

        <p>
          Nous sommes passionnés par la création d'une plateforme conviviale qui permet aux lecteurs de se connecter, de partager leurs découvertes littéraires et d'échanger des livres qui attendent patiemment sur les étagères.
        </p>
      </div>

      <p class="home-values__signature">
        L'équipe Tom Troc
      </p>
    </div>

    <img
      class="home-values__heart"
      src="assets/svg/heart.svg"
      alt=""
      aria-hidden="true">
  </div>
</section>
