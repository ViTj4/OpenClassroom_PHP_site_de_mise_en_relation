<?php

class User
{
    public function __construct(
        private readonly string $uuid,
        private readonly string $pseudo,
        private readonly string $email,
        private readonly string $password,
        private readonly ?string $profilePicture,
        private readonly string $userType,
        private readonly string $registerDate,
        private readonly ?string $updatedAt
    ) {
    }

    public static function fromArray(array $data): User
    {
        return new User(
            $data['uuid'],
            $data['pseudo'],
            $data['email'],
            $data['password'],
            $data['profile_picture'],
            $data['user_type'],
            $data['register_date'],
            $data['updated_at'] ?? null
        );
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function getRegisterDate(): string
    {
        return $this->registerDate;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function toSessionArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'pseudo' => $this->pseudo,
            'email' => $this->email,
            'userType' => $this->userType,
        ];
    }
}
