<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/autoload.php';

// Front controller : toutes les requêtes HTTP passent par ce fichier.
// Le paramètre `page` permet ensuite de choisir l'action à exécuter dans le routeur.
$page   = $_GET['page'] ?? 'home';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Les dépendances principales sont instanciées ici puis injectées dans les contrôleurs.
// Cela garde les contrôleurs testables et évite d'appeler les singletons partout dans le code.
$dbManager              = DBManager::getInstance();
$userManager            = new UserManager($dbManager);
$bookManager            = new BookManager($dbManager);
$messageManager         = new MessageManager($dbManager);
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

$pageController    = new PageController($bookManager);
$authController    = new AuthController($userManager, $csrfTokenManager, $passwordHasher);
$userController    = new UserController($userManager, $bookManager, $csrfTokenManager, $passwordHasher, $profilePictureUploader);
$bookController    = new BookController($bookManager, $userManager, $csrfTokenManager, $bookImageUploader);
$messageController = new MessageController($messageManager, $userManager, $csrfTokenManager);

// Routeur minimaliste : la méthode HTTP et la page demandée pointent vers une méthode de contrôleur.
// Si aucune route ne correspond, on affiche la page 404.
$routes = [
    'GET' => [
        'home'          => [$pageController, 'home'],
        'books'         => [$pageController, 'books'],
        'book'          => [$pageController, 'book'],
        'register'      => [$authController, 'showRegister'],
        'login'         => [$authController, 'showLogin'],
        'logout'        => [$authController, 'logout'],
        'account'       => [$userController, 'account'],
        'user'          => [$userController, 'publicProfile'],
        'messages'      => [$messageController, 'index'],
        'messages-poll' => [$messageController, 'poll'],
        'message-start' => [$messageController, 'start'],
        'book-create'   => [$bookController, 'showCreate'],
        'book-edit'     => [$bookController, 'showEdit'],
        'book-delete'   => [$bookController, 'delete'],
    ],
    'POST' => [
        'register'        => [$authController, 'register'],
        'login'           => [$authController, 'login'],
        'account'         => [$userController, 'updateAccount'],
        'account-picture' => [$userController, 'updateProfilePicture'],
        'messages'        => [$messageController, 'send'],
        'book-create'     => [$bookController, 'create'],
        'book-edit'       => [$bookController, 'update'],
    ],
];

$action            = $routes[$method][$page] ?? [$pageController, 'notFound'];
$jsonMessageRoutes = ['messages-poll'];
$expectsJson       = in_array($page, $jsonMessageRoutes, true)
    || ($page === 'messages' && str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'));

if ($expectsJson) {
    // ob_start() active une mémoire tampon de sortie.
    // Si une erreur PHP génère du HTML avant notre JSON, on pourra nettoyer ce HTML avec ob_clean()
    // afin que le navigateur reçoive toujours une vraie réponse JSON exploitable par JavaScript.
    ob_start();

    // Les warnings/notices PHP sont transformés en exceptions pour centraliser la réponse d'erreur JSON.
    set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
        throw new ErrorException($message, 0, $severity, $file, $line);
    });

    try {
        $action();
        restore_error_handler();
        // ob_end_flush() ferme le buffer et envoie son contenu au navigateur.
        ob_end_flush();
    } catch (Throwable $exception) {
        restore_error_handler();

        if (ob_get_level() > 0) {
            // ob_clean() vide le buffer sans le fermer : on supprime une éventuelle sortie HTML invalide.
            ob_clean();
        }

        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error'  => 'Erreur serveur pendant la messagerie.',
            'detail' => env('APP_DEBUG', 'false') === 'true' ? $exception->getMessage() : null,
        ], JSON_THROW_ON_ERROR);

        if (ob_get_level() > 0) {
            // On ferme proprement le buffer après avoir écrit notre réponse JSON.
            ob_end_flush();
        }
    }

    return;
}

$action();
