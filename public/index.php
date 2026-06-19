<?php
$page = $_GET['page'] ?? 'home';
$routes = [
  'home' => [
    'title' => 'Tom Troc',
    'view' => __DIR__ . '/../views/home/index.php',
  ],
  'books' => [
    'title' => 'Nos livres à l\'échange - Tom Troc',
    'view' => __DIR__ . '/../views/book/index.php',
  ],
  'book' => [
    'title' => 'The Kinkfolk Table - Tom Troc',
    'view' => __DIR__ . '/../views/book/show.php',
  ],
];

$route = $routes[$page] ?? $routes['home'];
$title = $route['title'];

ob_start();
require $route['view'];

$content = ob_get_clean();
require __DIR__ . '/../views/layouts/layout.php';
