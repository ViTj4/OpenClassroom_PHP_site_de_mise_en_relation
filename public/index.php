<?php
  $title = 'Tom Troc';
  ob_start();

  require __DIR__ . '/../views/home/index.php';
?>

<?php
  $content = ob_get_clean();
  require __DIR__ . '/../views/layouts/layout.php';
