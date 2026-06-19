<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Tom Troc') ?></title>

  <link rel="stylesheet" href="assets/css/main.css?v=<?= filemtime(__DIR__ . '/../../public/assets/css/main.css') ?>">
</head>

<body>
  <?php require __DIR__ . '/../partials/header.php' ?>

  <main>
    <?= $content ?? '' ?>
  </main>

  <?php require __DIR__ . '/../partials/footer.php' ?>

  <script src="assets/js/mobile-menu.js" defer></script>
  <script src="assets/js/books-filter.js" defer></script>
</body>

</html>