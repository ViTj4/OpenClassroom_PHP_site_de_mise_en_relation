<?php

class BookController extends AbstractController
{
    public function __construct(
        private readonly BookManager $bookManager,
        private readonly UserManager $userManager,
        private readonly CsrfTokenManager $csrfTokenManager,
        private readonly BookImageUploader $bookImageUploader
    ) {
    }

    public function showCreate(?FormState $formState = null): void
    {
        if ($this->getAuthenticatedUser() === null) {
            $this->redirect('login');
        }

        $formState ??= new FormState([
            'title' => '',
            'author' => '',
            'description' => '',
        ]);

        $this->render('views/book/create.php', 'Ajouter un livre - Tom Troc', 'account', [
            'mode' => 'create',
            'errors' => $formState->getErrors(),
            'formData' => $formState->getValues(),
            'csrfToken' => $this->csrfTokenManager->getToken(),
        ]);
    }

    public function create(): void
    {
        $user = $this->getAuthenticatedUser();

        if ($user === null) {
            $this->redirect('login');
        }

        $formState = FormState::fromArray($_POST, ['title', 'author', 'description']);

        $this->validateBookForm($formState);

        if ($formState->hasErrors()) {
            $this->showCreate($formState);
            return;
        }

        $book = null;

        try {
            $book = $this->bookManager->create(
                $formState->getValue('title'),
                $formState->getValue('author'),
                $formState->getValue('description'),
                BookManager::DEFAULT_BOOK_IMAGE,
                $user->getUuid(),
                'available'
            );

            if (($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $image = $this->bookImageUploader->upload($_FILES['image'], $book->getUuid());
                $this->bookManager->updateImage($book->getUuid(), $user->getUuid(), $image);
            }
        } catch (Throwable $exception) {
            if ($book instanceof Book) {
                $this->bookManager->delete($book->getUuid(), $user->getUuid());
            }

            $formState->addError($exception->getMessage());
            $this->showCreate($formState);
            return;
        }

        $this->redirect('account');
    }

    public function showEdit(?FormState $formState = null): void
    {
        $user = $this->getAuthenticatedUser();

        if ($user === null) {
            $this->redirect('login');
        }

        $book = $this->getOwnedBook($user);

        if ($book === null) {
            $this->redirect('account');
        }

        $formState ??= new FormState([
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'description' => $book->getDescription(),
            'status' => $book->getStatus(),
        ]);

        $this->render('views/book/edit.php', 'Modifier un livre - Tom Troc', 'account', [
            'mode' => 'edit',
            'book' => $book,
            'errors' => $formState->getErrors(),
            'formData' => $formState->getValues(),
            'csrfToken' => $this->csrfTokenManager->getToken(),
        ]);
    }

    public function update(): void
    {
        $user = $this->getAuthenticatedUser();

        if ($user === null) {
            $this->redirect('login');
        }

        $book = $this->getOwnedBook($user);

        if ($book === null) {
            $this->redirect('account');
        }

        $formState = FormState::fromArray($_POST, ['title', 'author', 'description', 'status']);
        $this->validateBookForm($formState);

        if ($formState->hasErrors()) {
            $this->showEdit($formState);
            return;
        }

        try {
            $this->bookManager->update(
                $book->getUuid(),
                $user->getUuid(),
                $formState->getValue('title'),
                $formState->getValue('author'),
                $formState->getValue('description'),
                $book->getImage(),
                $this->getNormalizedStatus($formState)
            );

            if (($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $image = $this->bookImageUploader->upload($_FILES['image'], $book->getUuid(), $book->getImage());
                $this->bookManager->updateImage($book->getUuid(), $user->getUuid(), $image);
            }
        } catch (Throwable $exception) {
            $formState->addError($exception->getMessage());
            $this->showEdit($formState);
            return;
        }

        $this->redirect('account');
    }

    private function getAuthenticatedUser(): ?User
    {
        if (empty($_SESSION['user']['uuid'])) {
            return null;
        }

        return $this->userManager->findByUuid($_SESSION['user']['uuid']);
    }

    private function getOwnedBook(User $user): ?Book
    {
        $uuid = $_GET['uuid'] ?? $_POST['uuid'] ?? null;

        if (!is_string($uuid) || $uuid === '') {
            return null;
        }

        return $this->bookManager->findByUuidAndOwnerUuid($uuid, $user->getUuid());
    }

    private function validateBookForm(FormState $formState): void
    {
        if (!$this->csrfTokenManager->isValid($_POST['csrf_token'] ?? null)) {
            $formState->addError('La session du formulaire a expiré. Veuillez réessayer.');
        }

        if ($formState->getValue('title') === '') {
            $formState->addError('Le titre du livre est obligatoire.');
        } elseif (!FormValidator::hasMaxLength($formState->getValue('title'), FormValidator::BOOK_TITLE_MAX_LENGTH)) {
            $formState->addError('Le titre du livre ne doit pas dépasser 190 caractères.');
        }

        if ($formState->getValue('author') === '') {
            $formState->addError('L\'auteur du livre est obligatoire.');
        } elseif (!FormValidator::hasMaxLength($formState->getValue('author'), FormValidator::BOOK_AUTHOR_MAX_LENGTH)) {
            $formState->addError('L\'auteur du livre ne doit pas dépasser 190 caractères.');
        }

        if ($formState->getValue('description') === '') {
            $formState->addError('La description du livre est obligatoire.');
        } elseif (!FormValidator::hasMaxLength($formState->getValue('description'), FormValidator::BOOK_DESCRIPTION_MAX_LENGTH)) {
            $formState->addError('La description du livre ne doit pas dépasser 2000 caractères.');
        }

        if (!in_array($this->getNormalizedStatus($formState), ['available', 'reserved', 'exchanged', 'removed'], true)) {
            $formState->addError('La disponibilité sélectionnée est invalide.');
        }
    }

    private function getNormalizedStatus(FormState $formState): string
    {
        return $formState->getValue('status') !== '' ? $formState->getValue('status') : 'available';
    }

}
