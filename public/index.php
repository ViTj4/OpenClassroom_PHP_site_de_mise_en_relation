<?php
  $title = 'Tom Troc';
  ob_start();
?>



<?php
  $content = ob_get_clean();
  require __DIR__ . '/../views/layouts/layout.php';
