<?php

class AdminController extends AbstractController
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly BookManager $bookManager
    ) {
    }

    public function dashboard(): void
    {
        $admin = $this->getAuthenticatedAdmin();

        $this->render('views/admin/dashboard.php', 'Administration - Tom Troc', 'admin', [
            'admin'               => $admin,
            'users'               => $this->userManager->findAll(),
            'books'               => $this->bookManager->findAllForAdmin(),
            'usersCount'          => $this->userManager->countAll(),
            'adminsCount'         => $this->userManager->countByType('admin'),
            'booksCount'          => $this->bookManager->countAll(),
            'availableBooksCount' => $this->bookManager->countByStatus('available'),
        ]);
    }

    private function getAuthenticatedAdmin(): User
    {
        if (empty($_SESSION['user']['uuid'])) {
            $this->redirect('login', null, 'Veuillez vous connecter pour accéder à cette page.');
        }

        $user = $this->userManager->findByUuid($_SESSION['user']['uuid']);

        if ($user === null) {
            $this->redirect('login', null, 'Votre session a expiré. Veuillez vous reconnecter.');
        }

        if ($user->getUserType() !== 'admin') {
            http_response_code(404);
            $this->render('views/errors/404.php', 'Page introuvable - Tom Troc', '404');
            exit;
        }

        return $user;
    }
}
