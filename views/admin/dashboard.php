<section class="admin-page">
  <div class="admin-page__inner">
    <header class="admin-page__header">
      <p class="admin-page__eyebrow">
        Espace réservé
      </p>
      <h1 class="admin-page__title">
        Administration
      </h1>
      <p class="admin-page__intro">
        Bonjour <?= htmlspecialchars($admin->getPseudo()) ?>, voici une vue d'ensemble des utilisateurs et des livres de Tom Troc.
      </p>
    </header>

    <div class="admin-stats" aria-label="Indicateurs administrateur">
      <article class="admin-stats__item">
        <span class="admin-stats__value"><?= htmlspecialchars((string) $usersCount) ?></span>
        <span class="admin-stats__label">Utilisateurs</span>
      </article>

      <article class="admin-stats__item">
        <span class="admin-stats__value"><?= htmlspecialchars((string) $adminsCount) ?></span>
        <span class="admin-stats__label">Administrateurs</span>
      </article>

      <article class="admin-stats__item">
        <span class="admin-stats__value"><?= htmlspecialchars((string) $booksCount) ?></span>
        <span class="admin-stats__label">Livres</span>
      </article>

      <article class="admin-stats__item">
        <span class="admin-stats__value"><?= htmlspecialchars((string) $availableBooksCount) ?></span>
        <span class="admin-stats__label">Disponibles</span>
      </article>
    </div>

    <section class="admin-section" aria-labelledby="admin-users-title">
      <div class="admin-section__header">
        <h2 class="admin-section__title" id="admin-users-title">
          Utilisateurs
        </h2>
      </div>

      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th scope="col">Pseudo</th>
              <th scope="col">Email</th>
              <th scope="col">Type</th>
              <th scope="col">Inscription</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($users)): ?>
              <tr>
                <td class="admin-table__empty" colspan="4">
                  Aucun utilisateur pour le moment.
                </td>
              </tr>
            <?php endif; ?>

            <?php foreach ($users as $user): ?>
              <tr>
                <td><?= htmlspecialchars($user->getPseudo()) ?></td>
                <td><?= htmlspecialchars($user->getEmail()) ?></td>
                <td>
                  <span class="admin-table__badge admin-table__badge--<?= htmlspecialchars($user->getUserType()) ?>">
                    <?= htmlspecialchars($user->getUserType()) ?>
                  </span>
                </td>
                <td><?= htmlspecialchars(substr($user->getRegisterDate(), 0, 10)) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="admin-section" aria-labelledby="admin-books-title">
      <div class="admin-section__header">
        <h2 class="admin-section__title" id="admin-books-title">
          Livres
        </h2>
        <a class="admin-section__link" href="index.php?page=books">
          Voir la page publique
        </a>
      </div>

      <div class="admin-table-wrapper">
        <table class="admin-table admin-table--books">
          <thead>
            <tr>
              <th scope="col">Titre</th>
              <th scope="col">Auteur</th>
              <th scope="col">Propriétaire</th>
              <th scope="col">Statut</th>
              <th scope="col">Ajout</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($books)): ?>
              <tr>
                <td class="admin-table__empty" colspan="5">
                  Aucun livre pour le moment.
                </td>
              </tr>
            <?php endif; ?>

            <?php foreach ($books as $book): ?>
              <tr>
                <td>
                  <a class="admin-table__book-link" href="index.php?page=book&amp;uuid=<?= htmlspecialchars($book->getUuid()) ?>">
                    <?= htmlspecialchars($book->getTitle()) ?>
                  </a>
                </td>
                <td><?= htmlspecialchars($book->getAuthor()) ?></td>
                <td><?= htmlspecialchars($book->getOwnerPseudo()) ?></td>
                <td>
                  <span class="admin-table__badge admin-table__badge--<?= htmlspecialchars($book->getStatusCssModifier()) ?>">
                    <?= htmlspecialchars($book->getStatusLabel()) ?>
                  </span>
                </td>
                <td><?= htmlspecialchars(substr($book->getCreatedAt(), 0, 10)) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</section>
