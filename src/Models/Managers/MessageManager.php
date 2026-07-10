<?php

class MessageManager
{
    public function __construct(
        private readonly DBManager $dbManager
    ) {
    }

      /**
     * @return array<int, array<string, mixed>>
     */
    public function findConversationsForUser(string $userUuid): array
    {
          // La liste affiche toujours "l'autre" participant et un aperçu du dernier message.
          // La sous-requête LEFT JOIN récupère uniquement le dernier message de chaque conversation.
        $query = $this->dbManager->query(
            'SELECT
                conversations.uuid,
                conversations.last_message_at,
                other_user.uuid AS other_user_uuid,
                other_user.pseudo AS other_user_pseudo,
                other_user.profile_picture AS other_user_profile_picture,
                last_message.uuid AS last_message_uuid,
                last_message.content AS last_message_content,
                last_message.created_at AS last_message_created_at
             FROM conversation_participants current_participant
             INNER JOIN conversations ON conversations.uuid = current_participant.conversation_uuid
             INNER JOIN conversation_participants other_participant
                ON other_participant.conversation_uuid = conversations.uuid
                AND other_participant.user_uuid <> current_participant.user_uuid
                AND other_participant.deleted_at IS NULL
             INNER JOIN users other_user ON other_user.uuid        = other_participant.user_uuid
             LEFT  JOIN messages last_message ON last_message.uuid = (
                SELECT messages.uuid
                FROM messages
                WHERE messages.conversation_uuid = conversations.uuid
                ORDER BY messages.created_at DESC
                LIMIT 1
             )
             WHERE current_participant.user_uuid = :user_uuid
             AND current_participant.deleted_at IS NULL
             ORDER BY conversations.last_message_at DESC, conversations.created_at DESC',
            ['user_uuid' => $userUuid]
        );

        return $query->fetchAll();
    }

    public function findConversationForUser(string $conversationUuid, string $userUuid): ?array
    {
        $query = $this->dbManager->query(
            'SELECT
                conversations.uuid,
                other_user.uuid AS other_user_uuid,
                other_user.pseudo AS other_user_pseudo,
                other_user.profile_picture AS other_user_profile_picture
             FROM conversations
             INNER JOIN conversation_participants current_participant
                ON  current_participant.conversation_uuid = conversations.uuid
                AND current_participant.user_uuid         = :user_uuid
                AND current_participant.deleted_at IS NULL
             INNER JOIN conversation_participants other_participant
                ON other_participant.conversation_uuid = conversations.uuid
                AND other_participant.user_uuid <>: user_uuid_for_other
                AND other_participant.deleted_at IS NULL
             INNER JOIN users other_user ON other_user.uuid = other_participant.user_uuid
             WHERE conversations.uuid                       = :conversation_uuid
             LIMIT 1',
            [
                'conversation_uuid'   => $conversationUuid,
                'user_uuid'           => $userUuid,
                'user_uuid_for_other' => $userUuid,
            ]
        );

        $conversation = $query->fetch();

        return $conversation?: null;
    }

    public function findConversationBetweenUsers(string $firstUserUuid, string $secondUserUuid): ?array
    {
        $query = $this->dbManager->query(
            'SELECT conversations.uuid
             FROM conversations
             INNER JOIN conversation_participants first_participant
                ON  first_participant.conversation_uuid = conversations.uuid
                AND first_participant.user_uuid         = :first_user_uuid
                AND first_participant.deleted_at IS NULL
             INNER JOIN conversation_participants second_participant
                ON  second_participant.conversation_uuid = conversations.uuid
                AND second_participant.user_uuid         = :second_user_uuid
                AND second_participant.deleted_at IS NULL
             LIMIT 1',
            [
                'first_user_uuid'  => $firstUserUuid,
                'second_user_uuid' => $secondUserUuid,
            ]
        );

        $conversation = $query->fetch();

        return $conversation?: null;
    }

    public function createConversation(string $firstUserUuid, string $secondUserUuid): string
    {
        $conversationUuid = self::generateUuid();

        $this->dbManager->query(
            'INSERT INTO conversations (uuid, created_at, updated_at)
             VALUES (:uuid, NOW(), NOW())',
            ['uuid' => $conversationUuid]
        );

          // Une conversation est créée avec exactement deux lignes de participation.
          // Cela permettra plus tard d'ajouter des métadonnées par utilisateur si besoin.
        foreach ([$firstUserUuid, $secondUserUuid] as $userUuid) {
            $this->dbManager->query(
                'INSERT INTO conversation_participants (
                    conversation_uuid,
                    user_uuid
                 ) VALUES (
                    : conversation_uuid,
                    : user_uuid
                 )',
                [
                    'conversation_uuid' => $conversationUuid,
                    'user_uuid'         => $userUuid,
                ]
            );
        }

        return $conversationUuid;
    }

      /**
     * @return array<int, array<string, mixed>>
     */
    public function findMessagesForConversation(string $conversationUuid, string $userUuid): array
    {
          // Sécurité : on vérifie d'abord que l'utilisateur appartient bien à la conversation.
        $conversation = $this->findConversationForUser($conversationUuid, $userUuid);

        if ($conversation === null) {
            return [];
        }

        $query = $this->dbManager->query(
            'SELECT messages.*, users.profile_picture AS sender_profile_picture
             FROM messages
             INNER JOIN users ON users.uuid   = messages.sender_uuid
             WHERE messages.conversation_uuid = :conversation_uuid
             ORDER BY messages.created_at ASC',
            ['conversation_uuid' => $conversationUuid]
        );

        return $query->fetchAll();
    }

    public function createMessage(
        string $conversationUuid,
        string $senderUuid,
        string $content
    ): void {
        $this->dbManager->query(
            'INSERT INTO messages (
                uuid,
                conversation_uuid,
                sender_uuid,
                content
             ) VALUES (
                : uuid,
                : conversation_uuid,
                : sender_uuid,
                : content
             )',
            [
                'uuid'              => self::generateUuid(),
                'conversation_uuid' => $conversationUuid,
                'sender_uuid'       => $senderUuid,
                'content'           => $content,
            ]
        );

        $this->dbManager->query(
            'UPDATE conversations
             SET   updated_at = NOW(), last_message_at = NOW()
             WHERE uuid       = :conversation_uuid',
            ['conversation_uuid' => $conversationUuid]
        );
    }

    public function markAsRead(string $conversationUuid, string $userUuid): void
    {
          // last_read_at servira notamment à calculer des messages non lus.
        $this->dbManager->query(
            'UPDATE conversation_participants
             SET   last_read_at      = NOW()
             WHERE conversation_uuid = :conversation_uuid
             AND   user_uuid         = :user_uuid',
            [
                'conversation_uuid' => $conversationUuid,
                'user_uuid'         => $userUuid,
            ]
        );
    }

    private static function generateUuid(): string
    {
        $data    = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
