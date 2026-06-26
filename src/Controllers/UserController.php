<?php

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly BookManager $bookManager,
        private readonly CsrfTokenManager $csrfTokenManager,
        private readonly PasswordHasher $passwordHasher,
        private readonly ProfilePictureUploader $profilePictureUploader
    ) {
    }

    public function account(?FormState $formState = null, ?string $successMessage = null): void
    {
        $user = $this->getAuthenticatedUser();

        if ($user === null) {
            $this->redirect('login');
        }

        $formState ??= new FormState([
            'email' => $user->getEmail(),
            'pseudo' => $user->getPseudo(),
        ]);

        $this->render(
            'views/user/account.php',
            'Mon compte - Tom Troc',
            'account',
            [
                'user' => $user,
                'errors' => $formState->getErrors(),
                'formData' => $formState->getValues(),
                'successMessage' => $successMessage,
                'csrfToken' => $this->csrfTokenManager->getToken(),
                'booksCount' => $this->bookManager->countByOwnerUuid($user->getUuid()),
                'books' => $this->bookManager->findByOwnerUuid($user->getUuid()),
                'memberSince' => $this->getMemberSinceLabel($user),
            ]
        );
    }

    public function updateAccount(): void
    {
        $user = $this->getAuthenticatedUser();

        if ($user === null) {
            $this->redirect('login');
        }

        $formState = FormState::fromArray($_POST, ['email', 'pseudo']);

        if (!$this->csrfTokenManager->isValid($_POST['csrf_token'] ?? null)) {
            $formState->addError('La session du formulaire a expiré. Veuillez réessayer.');
        }

        if ($formState->getValue('email') === '') {
            $formState->addError('L\'adresse email est obligatoire.');
        } elseif (!filter_var($formState->getValue('email'), FILTER_VALIDATE_EMAIL)) {
            $formState->addError('L\'adresse email n\'est pas valide.');
        }

        if ($formState->getValue('pseudo') === '') {
            $formState->addError('Le pseudo est obligatoire.');
        } elseif (strlen($formState->getValue('pseudo')) < 2 || strlen($formState->getValue('pseudo')) > 80) {
            $formState->addError('Le pseudo doit contenir entre 2 et 80 caractères.');
        }

        $password = $_POST['password'] ?? '';
        if ($password !== '' && !$this->passwordHasher->isStrongEnough($password)) {
            $formState->addError('Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.');
        }

        if ($formState->hasErrors()) {
            $this->account($formState);
            return;
        }

        if ($this->userManager->findByEmailExceptUuid($formState->getValue('email'), $user->getUuid()) !== null) {
            $formState->addError('Un compte existe déjà avec cette adresse email.');
            $this->account($formState);
            return;
        }

        $passwordHash = $password !== '' ? $this->passwordHasher->hash($password) : null;

        try {
            $this->userManager->updateProfile(
                $user->getUuid(),
                $formState->getValue('pseudo'),
                $formState->getValue('email'),
                $passwordHash
            );
        } catch (Throwable) {
            $formState->addError('Impossible de modifier le profil pour le moment.');
            $this->account($formState);
            return;
        }

        $updatedUser = $this->userManager->findByUuid($user->getUuid());

        if ($updatedUser !== null) {
            $_SESSION['user'] = $updatedUser->toSessionArray();
        }

        $this->account(null, 'Vos informations ont bien été mises à jour.');
    }

    public function updateProfilePicture(): void
    {
        $user = $this->getAuthenticatedUser();

        if ($user === null) {
            $this->redirect('login');
        }

        if (!$this->csrfTokenManager->isValid($_POST['csrf_token'] ?? null)) {
            $formState = new FormState([
                'email' => $user->getEmail(),
                'pseudo' => $user->getPseudo(),
            ]);
            $formState->addError('La session du formulaire a expiré. Veuillez réessayer.');
            $this->account($formState);
            return;
        }

        try {
            $profilePicture = $this->profilePictureUploader->upload(
                $_FILES['profile_picture'] ?? [],
                $user->getUuid(),
                $user->getProfilePicture()
            );

            $this->userManager->updateProfilePicture($user->getUuid(), $profilePicture);
        } catch (Throwable $exception) {
            $formState = new FormState([
                'email' => $user->getEmail(),
                'pseudo' => $user->getPseudo(),
            ]);
            $formState->addError($exception->getMessage());
            $this->account($formState);
            return;
        }

        $updatedUser = $this->userManager->findByUuid($user->getUuid());

        if ($updatedUser !== null) {
            $_SESSION['user'] = $updatedUser->toSessionArray();
        }

        $this->account(null, 'Votre photo de profil a bien été mise à jour.');
    }

    private function getAuthenticatedUser(): ?User
    {
        if (empty($_SESSION['user']['uuid'])) {
            return null;
        }

        return $this->userManager->findByUuid($_SESSION['user']['uuid']);
    }

    private function getMemberSinceLabel(User $user): string
    {
        $registerDate = new DateTimeImmutable($user->getRegisterDate());
        $now = new DateTimeImmutable();
        $diff = $registerDate->diff($now);

        if ($diff->y > 0) {
            return 'Membre depuis ' . $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
        }

        if ($diff->m > 0) {
            return 'Membre depuis ' . $diff->m . ' mois';
        }

        return 'Membre depuis moins d\'un mois';
    }
}
