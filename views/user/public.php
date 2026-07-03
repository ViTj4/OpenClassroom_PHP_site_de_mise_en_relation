<section class="public-user" aria-labelledby="public-user-title">
  <div class="public-user__inner">
    <article class="public-user__profile-card">
      <img
        class="public-user__avatar"
        src="<?= htmlspecialchars($profileUser->getProfilePicture()) ?>"
        alt="">

      <div class="public-user__separator" aria-hidden="true"></div>

      <h1 class="public-user__pseudo" id="public-user-title">
        <?= htmlspecialchars($profileUser->getPseudo()) ?>
      </h1>

      <p class="public-user__member-since">
        <?= htmlspecialchars($memberSince) ?>
      </p>

      <p class="public-user__library-label">
        Bibliothèque
      </p>

      <p class="public-user__books-count">
        <img
          class="public-user__books-icon"
          src="assets/svg/booksCounter.svg"
          alt=""
          aria-hidden="true">
        <span><?= (int) $booksCount ?> livre<?= $booksCount > 1 ? 's' : '' ?></span>
      </p>

      <a class="public-user__message-button" href="index.php?page=messages">
        Écrire un message
      </a>
    </article>

    <section class="public-user__library" aria-label="Livres de <?= htmlspecialchars($profileUser->getPseudo()) ?>">
      <?php if (empty($books)): ?>
        <p class="public-user__empty">
          Cet utilisateur n'a pas encore ajouté de livre.
        </p>
      <?php else: ?>
        <div class="public-user__table-wrapper">
          <table class="public-user__table">
            <thead>
              <tr>
                <th scope="col">Photo</th>
                <th scope="col">Titre</th>
                <th scope="col">Auteur</th>
                <th scope="col">Description</th>
              </tr>
            </thead>

            <tbody>
              <?php foreach ($books as $book): ?>
                <?php
                  $description = $book->getDescription();
                  $descriptionExcerpt = strlen($description) > 94
                      ? substr($description, 0, 94) . '...'
                      : $description;
                ?>

                <tr
                  class="public-user__book-row"
                  data-row-link="index.php?page=book&amp;uuid=<?= htmlspecialchars($book->getUuid()) ?>"
                  tabindex="0"
                  role="link"
                  aria-label="Voir le livre <?= htmlspecialchars($book->getTitle()) ?>">
                  <td>
                    <img
                      class="public-user__book-image"
                      src="<?= htmlspecialchars($book->getImage()) ?>"
                      alt="<?= htmlspecialchars($book->getAltText()) ?>">
                  </td>

                  <td><?= htmlspecialchars($book->getTitle()) ?></td>
                  <td><?= htmlspecialchars($book->getAuthor()) ?></td>
                  <td>
                    <p class="public-user__book-description">
                      <?= htmlspecialchars($descriptionExcerpt) ?>
                    </p>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>
  </div>
</section>
