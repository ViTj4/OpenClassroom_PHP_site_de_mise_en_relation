<?php

class UserManager
{
    public function __construct(
        private readonly DBManager $dbManager
    ) {
    }

    public function create(string $pseudo, string $email, string $passwordHash): User
    {
        $uuid = self::generateUuid();

        $this->dbManager->query(
            'INSERT INTO users (uuid, pseudo, email, password, profile_picture, user_type)
             VALUES (:uuid, :pseudo, :email, :password, :profile_picture, :user_type)',
            [
                'uuid' => $uuid,
                'pseudo' => $pseudo,
                'email' => strtolower($email),
                'password' => $passwordHash,
                'profile_picture' => null,
                'user_type' => 'user',
            ]
        );

        $user = $this->findByUuid($uuid);

        if ($user === null) {
            throw new RuntimeException('Utilisateur introuvable apres creation.');
        }

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        $query = $this->dbManager->query(
            'SELECT * FROM users WHERE email = :email LIMIT 1',
            ['email' => strtolower($email)]
        );

        $user = $query->fetch();

        return $user ? User::fromArray($user) : null;
    }

    public function findByUuid(string $uuid): ?User
    {
        $query = $this->dbManager->query(
            'SELECT * FROM users WHERE uuid = :uuid LIMIT 1',
            ['uuid' => $uuid]
        );

        $user = $query->fetch();

        return $user ? User::fromArray($user) : null;
    }

    public function updatePasswordHash(string $uuid, string $passwordHash): void
    {
        $this->dbManager->query(
            'UPDATE users SET password = :password WHERE uuid = :uuid',
            [
                'uuid' => $uuid,
                'password' => $passwordHash,
            ]
        );
    }

    private static function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
