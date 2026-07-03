<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/autoload.php';

$page   = $_GET['page'] ?? 'home';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$dbManager              = DBManager::getInstance();
$userManager            = new UserManager($dbManager);
$bookManager            = new BookManager($dbManager);
$csrfTokenManager       = new CsrfTokenManager();
$passwordHasher         = new PasswordHasher();
$profilePictureUploader = new ProfilePictureUploader(
    dirname(__DIR__) . '/public/uploads/profile-pictures',
    'uploads/profile-pictures'
);
$bookImageUploader = new BookImageUploader(
    dirname(__DIR__) . '/public/uploads/books',
    'uploads/books'
);

$pageController = new PageController($bookManager);
$authController = new AuthController($userManager, $csrfTokenManager, $passwordHasher);
$userController = new UserController($userManager, $bookManager, $csrfTokenManager, $passwordHasher, $profilePictureUploader);
$bookController = new BookController($bookManager, $userManager, $csrfTokenManager, $bookImageUploader);

$routes = [
    'GET' => [
        'home'        => [$pageController, 'home'],
        'books'       => [$pageController, 'books'],
        'book'        => [$pageController, 'book'],
        'register'    => [$authController, 'showRegister'],
        'login'       => [$authController, 'showLogin'],
        'logout'      => [$authController, 'logout'],
        'account'     => [$userController, 'account'],
        'user'        => [$userController, 'publicProfile'],
        'book-create' => [$bookController, 'showCreate'],
        'book-edit'   => [$bookController, 'showEdit'],
        'book-delete' => [$bookController, 'delete'],
    ],
    'POST' => [
        'register'        => [$authController, 'register'],
        'login'           => [$authController, 'login'],
        'account'         => [$userController, 'updateAccount'],
        'account-picture' => [$userController, 'updateProfilePicture'],
        'book-create'     => [$bookController, 'create'],
        'book-edit'       => [$bookController, 'update'],
    ],
];

$action = $routes[$method][$page] ?? [$pageController, 'notFound'];
$action();
