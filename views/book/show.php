<?php
  $descriptionParagraphs = preg_split('/\R{2,}/', trim($book->getDescription())) ?: [];
?>

<section class="book-detail" aria-labelledby="book-detail-title">
  <div class="book-detail__breadcrumb" aria-label="Fil d'Ariane">
    <a class="book-detail__breadcrumb-link" href="index.php?page=books">Nos livres</a>
    <span class="book-detail__breadcrumb-separator" aria-hidden="true">&gt;</span>
    <span class="book-detail__breadcrumb-current"><?= htmlspecialchars($book->getTitle()) ?></span>
  </div>

  <div class="book-detail__layout">
    <div class="book-detail__image-frame">
      <img
        class="book-detail__image"
        src="<?= htmlspecialchars($book->getImage()) ?>"
        alt="<?= htmlspecialchars($book->getAltText()) ?>">
    </div>

    <article class="book-detail__content">
      <h1 class="book-detail__title" id="book-detail-title">
        <?= htmlspecialchars($book->getTitle()) ?>
      </h1>

      <p class="book-detail__author">
        par <?= htmlspecialchars($book->getAuthor()) ?>
      </p>

      <div class="book-detail__separator" aria-hidden="true"></div>

      <h2 class="book-detail__section-title">
        Description
      </h2>

      <div class="book-detail__description">
        <?php foreach ($descriptionParagraphs as $paragraph) : ?>
          <p>
            <?= nl2br(htmlspecialchars($paragraph)) ?>
          </p>
        <?php endforeach; ?>
      </div>

      <h2 class="book-detail__section-title book-detail__section-title--owner">
        Propriétaire
      </h2>

      <a
        class="book-detail__owner-card"
        href="index.php?page=user&amp;uuid=<?= htmlspecialchars($book->getOwnerUuid()) ?>">
        <img
          class="book-detail__owner-image"
          src="<?= htmlspecialchars($book->getOwnerProfilePicture()) ?>"
          alt="">

        <span class="book-detail__owner-name">
          <?= htmlspecialchars($book->getOwnerPseudo()) ?>
        </span>
      </a>

      <a
        class="book-detail__button"
        href="index.php?page=message-start&amp;recipient=<?= htmlspecialchars($book->getOwnerUuid()) ?>">
        Envoyer un message
      </a>
    </article>
  </div>
</section>
