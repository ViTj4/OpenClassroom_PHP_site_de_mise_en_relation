<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/autoload.php';

$page   = $_GET['page'] ?? 'home';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$dbManager        = DBManager::getInstance();
$userManager      = new UserManager($dbManager);
$csrfTokenManager = new CsrfTokenManager();
$passwordHasher   = new PasswordHasher();

$pageController = new PageController();
$authController = new AuthController($userManager, $csrfTokenManager, $passwordHasher);

$routes = [
    'GET' => [
        'home'     => [$pageController, 'home'],
        'books'    => [$pageController, 'books'],
        'book'     => [$pageController, 'book'],
        'register' => [$authController, 'showRegister'],
        'login'    => [$authController, 'showLogin'],
        'logout'   => [$authController, 'logout'],
    ],
    'POST' => [
        'register' => [$authController, 'register'],
        'login'    => [$authController, 'login'],
    ],
];

$action = $routes[$method][$page] ?? $routes['GET']['home'];
$action();
