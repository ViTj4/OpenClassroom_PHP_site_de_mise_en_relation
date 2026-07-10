<?php

class MessageController extends AbstractController
{
    public function __construct(
        private readonly MessageManager $messageManager,
        private readonly UserManager $userManager,
        private readonly CsrfTokenManager $csrfTokenManager
    ) {
    }

    public function index(): void
    {
        $user = $this->getAuthenticatedUser();

        if ($user === null) {
            $this->redirect('login', null, 'Veuillez vous connecter pour accéder à la messagerie.');
        }

        $conversationRows = $this->messageManager->findConversationsForUser($user->getUuid());
        $selectedConversationUuid = $_GET['conversation'] ?? null;
        $isThreadOpen = is_string($selectedConversationUuid) && $selectedConversationUuid !== '';

        // En desktop, la première conversation est ouverte automatiquement comme sur la maquette.
        // En mobile, on garde d'abord la liste pour naviguer vers un fil ensuite.
        if (!$isThreadOpen && $conversationRows !== [] && !$this->isMobileRequest()) {
            $selectedConversationUuid = $conversationRows[0]['uuid'];
        }

        $activeConversation = null;
        $conversationMessages = [];

        if (is_string($selectedConversationUuid) && $selectedConversationUuid !== '') {
            $activeConversation = $this->messageManager->findConversationForUser($selectedConversationUuid, $user->getUuid());

            if ($activeConversation !== null) {
                $conversationMessages = $this->formatMessages(
                    $this->messageManager->findMessagesForConversation($selectedConversationUuid, $user->getUuid()),
                    $user->getUuid()
                );
                $this->messageManager->markAsRead($selectedConversationUuid, $user->getUuid());
            }
        }

        $conversations = $this->formatConversations($conversationRows, $activeConversation['uuid'] ?? null);

        $this->render('views/message/index.php', 'Messagerie - Tom Troc', 'messages', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation !== null ? $this->formatConversationHeader($activeConversation) : null,
            'messages' => $conversationMessages,
            'isThreadOpen' => $isThreadOpen && $activeConversation !== null,
            'csrfToken' => $this->csrfTokenManager->getToken(),
        ]);
    }

    public function start(): void
    {
        $user = $this->getAuthenticatedUser();

        if ($user === null) {
            if ($this->expectsJson()) {
                $this->jsonResponse(['error' => 'Unauthorized'], 401);
                return;
            }

            $this->redirect('login', null, 'Veuillez vous connecter pour envoyer un message.');
        }

        $recipientUuid = $_GET['recipient'] ?? null;
        $recipient = is_string($recipientUuid) && $recipientUuid !== ''
            ? $this->userManager->findByUuid($recipientUuid)
            : null;

        if ($recipient === null) {
            $this->redirect('messages', null, 'Cet utilisateur est introuvable.');
        }

        if ($recipient->getUuid() === $user->getUuid()) {
            $this->redirect('messages', null, 'Vous ne pouvez pas démarrer une conversation avec vous-même.');
        }

        $conversation = $this->messageManager->findConversationBetweenUsers($user->getUuid(), $recipient->getUuid());
        $conversationUuid = $conversation['uuid'] ?? $this->messageManager->createConversation($user->getUuid(), $recipient->getUuid());

        $this->redirectToConversation($conversationUuid);
    }

    public function send(): void
    {
        $user = $this->getAuthenticatedUser();

        if ($user === null) {
            $this->redirect('login', null, 'Veuillez vous connecter pour envoyer un message.');
        }

        $conversationUuid = $_POST['conversation_uuid'] ?? '';
        $content = trim((string) ($_POST['content'] ?? ''));

        // Cette action sert à la fois au formulaire HTML classique et à l'envoi AJAX.
        // expectsJson() permet donc de répondre soit par redirection, soit par JSON.
        if (!is_string($conversationUuid) || $conversationUuid === '') {
            if ($this->expectsJson()) {
                $this->jsonResponse(['error' => 'Conversation introuvable.'], 400);
                return;
            }

            $this->redirect('messages', null, 'Conversation introuvable.');
        }

        if (!$this->csrfTokenManager->isValid($_POST['csrf_token'] ?? null)) {
            if ($this->expectsJson()) {
                $this->jsonResponse(['error' => 'La session du formulaire a expiré. Veuillez réessayer.'], 403);
                return;
            }

            $this->redirectToConversation($conversationUuid, 'La session du formulaire a expiré. Veuillez réessayer.');
        }

        if ($this->messageManager->findConversationForUser($conversationUuid, $user->getUuid()) === null) {
            if ($this->expectsJson()) {
                $this->jsonResponse(['error' => 'Vous ne pouvez pas envoyer de message dans cette conversation.'], 403);
                return;
            }

            $this->redirect('messages', null, 'Vous ne pouvez pas envoyer de message dans cette conversation.');
        }

        if ($content === '') {
            if ($this->expectsJson()) {
                $this->jsonResponse(['error' => 'Le message ne peut pas être vide.'], 422);
                return;
            }

            $this->redirectToConversation($conversationUuid, 'Le message ne peut pas être vide.');
        }

        if ($this->stringLength($content) > 1000) {
            if ($this->expectsJson()) {
                $this->jsonResponse(['error' => 'Le message ne peut pas dépasser 1000 caractères.'], 422);
                return;
            }

            $this->redirectToConversation($conversationUuid, 'Le message ne peut pas dépasser 1000 caractères.');
        }

        $this->messageManager->createMessage(
            $conversationUuid,
            $user->getUuid(),
            $content
        );

        if ($this->expectsJson()) {
            $this->jsonResponse(['success' => true]);
            return;
        }

        $this->redirectToConversation($conversationUuid);
    }

    public function poll(): void
    {
        $user = $this->getAuthenticatedUser();

        if ($user === null) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        $conversationUuid = $_GET['conversation'] ?? null;

        if (!is_string($conversationUuid) || $conversationUuid === '') {
            $this->jsonResponse(['error' => 'Conversation introuvable.'], 400);
            return;
        }

        if ($this->messageManager->findConversationForUser($conversationUuid, $user->getUuid()) === null) {
            $this->jsonResponse(['error' => 'Conversation introuvable.'], 404);
            return;
        }

        $messages = $this->formatMessages(
            $this->messageManager->findMessagesForConversation($conversationUuid, $user->getUuid()),
            $user->getUuid()
        );

        // Le polling est appelé régulièrement par JavaScript pour rafraîchir le fil sans recharger la page.
        $this->messageManager->markAsRead($conversationUuid, $user->getUuid());

        $this->jsonResponse([
            'messages' => $messages,
        ]);
    }

    private function getAuthenticatedUser(): ?User
    {
        if (empty($_SESSION['user']['uuid'])) {
            return null;
        }

        // On relit l'utilisateur en base pour éviter d'utiliser des données de session obsolètes.
        return $this->userManager->findByUuid($_SESSION['user']['uuid']);
    }

    private function redirectToConversation(string $conversationUuid, ?string $errorMessage = null): void
    {
        if ($errorMessage !== null) {
            $_SESSION['flash_errors'] ??= [];
            $_SESSION['flash_errors'][] = $errorMessage;
        }

        header('Location: index.php?page=messages&conversation=' . urlencode($conversationUuid));
        exit;
    }

    /**
     * @param array<int, array<string, mixed>> $conversationRows
     * @return array<int, array<string, mixed>>
     */
    private function formatConversations(array $conversationRows, ?string $activeConversationUuid): array
    {
        return array_map(function (array $conversation) use ($activeConversationUuid): array {
            $preview = '';

            if (!empty($conversation['last_message_content'])) {
                $preview = $conversation['last_message_content'];
            }

            return [
                'uuid' => $conversation['uuid'],
                'pseudo' => $conversation['other_user_pseudo'],
                'picture' => $conversation['other_user_profile_picture'] ?: UserManager::DEFAULT_PROFILE_PICTURE,
                'time' => $this->formatConversationTime($conversation['last_message_created_at'] ?? $conversation['last_message_at']),
                'preview' => $this->truncate($preview !== '' ? $preview : 'Aucun message pour le moment.'),
                'active' => $conversation['uuid'] === $activeConversationUuid,
            ];
        }, $conversationRows);
    }

    private function formatConversationHeader(array $conversation): array
    {
        return [
            'uuid' => $conversation['uuid'],
            'pseudo' => $conversation['other_user_pseudo'],
            'picture' => $conversation['other_user_profile_picture'] ?: UserManager::DEFAULT_PROFILE_PICTURE,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $messages
     * @return array<int, array<string, mixed>>
     */
    private function formatMessages(array $messages, string $currentUserUuid): array
    {
        return array_map(function (array $message) use ($currentUserUuid): array {
            $createdAt = new DateTimeImmutable($message['created_at']);

            return [
                'uuid' => $message['uuid'],
                // La vue et le JavaScript s'appuient sur `sent` / `received` pour aligner les bulles.
                'direction' => $message['sender_uuid'] === $currentUserUuid ? 'sent' : 'received',
                'date' => $createdAt->format('d.m'),
                'time' => $createdAt->format('H:i'),
                'content' => $message['content'],
                'picture' => $message['sender_profile_picture'] ?: UserManager::DEFAULT_PROFILE_PICTURE,
            ];
        }, $messages);
    }

    private function formatConversationTime(?string $date): string
    {
        if ($date === null || $date === '') {
            return '';
        }

        $messageDate = new DateTimeImmutable($date);
        $today = new DateTimeImmutable('today');

        if ($messageDate >= $today) {
            return $messageDate->format('H:i');
        }

        return $messageDate->format('d.m');
    }

    private function truncate(string $text, int $length = 34): string
    {
        if ($this->stringLength($text) <= $length) {
            return $text;
        }

        return $this->substring($text, 0, $length - 3) . '...';
    }

    private function isMobileRequest(): bool
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        return preg_match('/Android|iPhone|iPad|iPod|Mobile/i', $userAgent) === 1;
    }

    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_THROW_ON_ERROR);
    }

    private function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

        return str_contains($accept, 'application/json') || $requestedWith === 'XMLHttpRequest';
    }

    private function stringLength(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }

    private function substring(string $value, int $start, int $length): string
    {
        return function_exists('mb_substr') ? mb_substr($value, $start, $length) : substr($value, $start, $length);
    }
}
