<?php

class AuthController extends AbstractController
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly CsrfTokenManager $csrfTokenManager,
        private readonly PasswordHasher $passwordHasher
    ) {
    }

    public function showRegister(?FormState $formState = null): void
    {
        $formState ??= new FormState();

        $this->render(
            'views/auth/register.php',
            'Inscription - Tom Troc',
            'register',
            [
                'errors'    => $formState->getErrors(),
                'formData'  => $formState->getValues(),
                'csrfToken' => $this->csrfTokenManager->getToken(),
            ]
        );
    }

    public function register(): void
    {
        $formState = FormState::fromArray($_POST, ['username', 'email']);

        if (!$this->csrfTokenManager->isValid($_POST['csrf_token'] ?? null)) {
            $formState->addError('La session du formulaire a expiré. Veuillez réessayer.');
        }

        if ($formState->getValue('username') === '') {
            $formState->addError('Le pseudo est obligatoire.');
        } elseif (strlen($formState->getValue('username')) < 2 || strlen($formState->getValue('username')) > 80) {
            $formState->addError('Le pseudo doit contenir entre 2 et 80 caractères.');
        }

        if ($formState->getValue('email') === '') {
            $formState->addError('L\'adresse email est obligatoire.');
        } elseif (!filter_var($formState->getValue('email'), FILTER_VALIDATE_EMAIL)) {
            $formState->addError('L\'adresse email n\'est pas valide.');
        }

        $password = $_POST['password'] ?? '';
        if ($password === '') {
            $formState->addError('Le mot de passe est obligatoire.');
        } elseif (!$this->isPasswordStrongEnough($password)) {
            $formState->addError('Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.');
        }

        if ($formState->hasErrors()) {
            $this->showRegister($formState);
            return;
        }

        if ($this->userManager->findByEmail($formState->getValue('email')) !== null) {
            $formState->addError('Un compte existe déjà avec cette adresse email.');
            $this->showRegister($formState);
            return;
        }

        try {
            $this->userManager->create(
                $formState->getValue('username'),
                $formState->getValue('email'),
                $this->passwordHasher->hash($password)
            );
        } catch (Throwable) {
            $formState->addError('Impossible de créer le compte pour le moment.');
            $this->showRegister($formState);
            return;
        }

        $_SESSION['flash_success'] = 'Votre compte a bien été créé. Vous pouvez maintenant vous connecter.';
        $this->redirect('login');
    }

    public function showLogin(?FormState $formState = null): void
    {
        $formState ??= new FormState();
        $successMessage = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);

        $this->render(
            'views/auth/login.php',
            'Connexion - Tom Troc',
            'login',
            [
                'errors'         => $formState->getErrors(),
                'formData'       => $formState->getValues(),
                'successMessage' => $successMessage,
                'csrfToken'      => $this->csrfTokenManager->getToken(),
            ]
        );
    }

    public function login(): void
    {
        $formState = FormState::fromArray($_POST, ['email']);

        if (!$this->csrfTokenManager->isValid($_POST['csrf_token'] ?? null)) {
            $formState->addError('La session du formulaire a expiré. Veuillez réessayer.');
        }

        if ($formState->getValue('email') === '') {
            $formState->addError('L\'adresse email est obligatoire.');
        }

        $password = $_POST['password'] ?? '';
        if ($password === '') {
            $formState->addError('Le mot de passe est obligatoire.');
        }

        if ($formState->hasErrors()) {
            $this->showLogin($formState);
            return;
        }

        try {
            $user = $this->userManager->findByEmail($formState->getValue('email'));
        } catch (Throwable) {
            $formState->addError('Impossible de se connecter pour le moment.');
            $this->showLogin($formState);
            return;
        }

        if ($user === null || !$this->passwordHasher->verify($password, $user->getPassword())) {
            $formState->addError('Les identifiants sont incorrects.');
            $this->showLogin($formState);
            return;
        }

        if ($this->passwordHasher->needsRehash($user->getPassword())) {
            $this->userManager->updatePasswordHash($user->getUuid(), $this->passwordHasher->hash($password));
        }

        session_regenerate_id(true);
        $_SESSION['user'] = $user->toSessionArray();

        $this->redirect('home');
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);

        $this->redirect('home');
    }

    private function isPasswordStrongEnough(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[a-z]/', $password)
            && preg_match('/[A-Z]/', $password)
            && preg_match('/\d/', $password)
            && preg_match('/[^a-zA-Z\d]/', $password);
    }
}
