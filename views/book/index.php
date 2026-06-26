<section class="books-page" aria-labelledby="books-page-title">
  <div class="books-page__inner">
    <header class="books-page__header">
      <h1 class="books-page__title" id="books-page-title">
        Nos livres à l’échange
      </h1>

      <form class="books-page__search" role="search">
        <label class="books-page__search-label" for="books-search">
          Rechercher un livre, un auteur ou un vendeur
        </label>

        <span class="books-page__search-icon" aria-hidden="true"></span>

        <input
          class="books-page__search-input"
          id="books-search"
          name="search"
          type="search"
          placeholder="Rechercher un livre"
          autocomplete="off"
          data-books-search>
      </form>
    </header>

    <div class="books-page__grid" data-books-grid>
      <?php foreach ($books as $book) : ?>
        <?php
          $searchText = implode(' ', [$book->getTitle(), $book->getAuthor(), $book->getOwnerPseudo()]);
        ?>

        <a
          class="books-page__card"
          href="index.php?page=book&amp;uuid=<?= htmlspecialchars($book->getUuid()) ?>"
          data-book-card
          data-search="<?= htmlspecialchars($searchText) ?>">
          <img
            class="books-page__image"
            src="<?= htmlspecialchars($book->getImage()) ?>"
            alt="<?= htmlspecialchars($book->getAltText()) ?>">

          <span class="books-page__body">
            <span class="books-page__book-title">
              <?= htmlspecialchars($book->getTitle()) ?>
            </span>

            <span class="books-page__author">
              <?= htmlspecialchars($book->getAuthor()) ?>
            </span>

            <span class="books-page__seller">
              Vendu par : <?= htmlspecialchars($book->getOwnerPseudo()) ?>
            </span>
          </span>
        </a>
      <?php endforeach; ?>
    </div>

    <p class="books-page__empty" data-books-empty hidden>
      Aucun livre ne correspond à votre recherche.
    </p>
  </div>
</section>
