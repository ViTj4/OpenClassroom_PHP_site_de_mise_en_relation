<?php

class Book
{
    public function __construct(
        private readonly string $uuid,
        private readonly string $title,
        private readonly string $author,
        private readonly string $description,
        private readonly string $image,
        private readonly string $ownerUuid,
        private readonly string $status,
        private readonly string $createdAt,
        private readonly ?string $updatedAt,
        private readonly ?string $exchangedAt,
        private readonly string $ownerPseudo,
        private readonly string $ownerProfilePicture
    ) {
    }

    public static function fromArray(array $data): Book
    {
        return new Book(
            $data['uuid'],
            $data['title'],
            $data['author'],
            $data['description'],
            $data['image'],
            $data['owner_uuid'],
            $data['status'],
            $data['created_at'],
            $data['updated_at'] ?? null,
            $data['exchanged_at'] ?? null,
            $data['owner_pseudo'] ?? '',
            $data['owner_profile_picture'] ?? UserManager::DEFAULT_PROFILE_PICTURE
        );
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getOwnerUuid(): string
    {
        return $this->ownerUuid;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function getExchangedAt(): ?string
    {
        return $this->exchangedAt;
    }

    public function getOwnerPseudo(): string
    {
        return $this->ownerPseudo;
    }

    public function getOwnerProfilePicture(): string
    {
        return $this->ownerProfilePicture;
    }

    public function getAltText(): string
    {
        return 'Livre ' . $this->title . ' de ' . $this->author;
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'available' => 'disponible',
            'reserved' => 'non dispo.',
            'exchanged' => 'échangé',
            'removed' => 'retiré',
            default => 'indisponible',
        };
    }

    public function getStatusCssModifier(): string
    {
        return match ($this->status) {
            'available' => 'available',
            'reserved' => 'reserved',
            'exchanged' => 'exchanged',
            'removed' => 'removed',
            default => 'unavailable',
        };
    }
}
