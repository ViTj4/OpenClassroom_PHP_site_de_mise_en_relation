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
        } elseif (!FormValidator::isValidPseudo($formState->getValue('username'))) {
            $formState->addError('Le pseudo doit contenir entre 2 et 30 caractères, sans espace ni caractère spécial autre que - ou _.');
        }

        if ($formState->getValue('email') === '') {
            $formState->addError('L\'adresse email est obligatoire.');
        } elseif (!FormValidator::isValidEmail($formState->getValue('email'))) {
            $formState->addError('L\'adresse email n\'est pas valide.');
        }

        $password = $_POST['password'] ?? '';
        if ($password === '') {
            $formState->addError('Le mot de passe est obligatoire.');
        } elseif (!$this->passwordHasher->isStrongEnough($password)) {
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
        } elseif (!FormValidator::isValidEmail($formState->getValue('email'))) {
            $formState->addError('L\'adresse email n\'est pas valide.');
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

}
