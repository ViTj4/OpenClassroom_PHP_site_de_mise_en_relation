<?php
  $books = [
    [
      'title' => 'Esther',
      'author' => 'Alabaster',
      'seller' => 'CamilleClubLit',
      'image' => 'assets/images/Esther_Alabaster.png',
      'alt' => 'Livre Esther posé devant un tissu vert',
    ],
    [
      'title' => 'The Kinfolk Table',
      'author' => 'Nathan Williams',
      'seller' => 'Alexlecture',
      'image' => 'assets/images/Nathan_Williams.png',
      'alt' => 'Livre The Kinfolk Table posé avec des fleurs',
    ],
    [
      'title' => 'Wabi Sabi',
      'author' => 'Beth Kempton',
      'seller' => 'Alexlecture',
      'image' => 'assets/images/Wabi_Sabi.png',
      'alt' => 'Livre Wabi Sabi posé sur une table',
    ],
    [
      'title' => 'Milk & honey',
      'author' => 'Rupi Kaur',
      'seller' => 'Hugo1990_12',
      'image' => 'assets/images/Milk_and_Honey.png',
      'alt' => 'Livre Milk and honey posé sur une table ronde',
    ],
    [
      'title' => 'The Creative Act',
      'author' => 'Rick Rubin',
      'seller' => 'Nathalire',
      'image' => 'assets/images/Esther_Alabaster.png',
      'alt' => 'Livre The Creative Act',
    ],
    [
      'title' => 'Design as Art',
      'author' => 'Bruno Munari',
      'seller' => 'CamilleClubLit',
      'image' => 'assets/images/Nathan_Williams.png',
      'alt' => 'Livre Design as Art',
    ],
    [
      'title' => 'Minimalist Graphics',
      'author' => 'Gestalten',
      'seller' => 'Alexlecture',
      'image' => 'assets/images/Wabi_Sabi.png',
      'alt' => 'Livre Minimalist Graphics',
    ],
    [
      'title' => 'Hygge',
      'author' => 'Meik Wiking',
      'seller' => 'Hugo1990_12',
      'image' => 'assets/images/Milk_and_Honey.png',
      'alt' => 'Livre Hygge',
    ],
  ];
?>

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
          $searchText = implode(' ', [$book['title'], $book['author'], $book['seller']]);
        ?>

        <a
          class="books-page__card"
          href="index.php?page=book"
          data-book-card
          data-search="<?= htmlspecialchars($searchText) ?>">
          <img
            class="books-page__image"
            src="<?= htmlspecialchars($book['image']) ?>"
            alt="<?= htmlspecialchars($book['alt']) ?>">

          <span class="books-page__body">
            <span class="books-page__book-title">
              <?= htmlspecialchars($book['title']) ?>
            </span>

            <span class="books-page__author">
              <?= htmlspecialchars($book['author']) ?>
            </span>

            <span class="books-page__seller">
              Vendu par : <?= htmlspecialchars($book['seller']) ?>
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
