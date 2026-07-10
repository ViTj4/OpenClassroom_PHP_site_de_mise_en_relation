<?php

class BookManager
{
    public const DEFAULT_BOOK_IMAGE = 'assets/svg/picture.svg';

    public function __construct(
        private readonly DBManager $dbManager
    ) {
    }

    /**
     * @return Book[]
     */
    public function findAll(): array
    {
        $query = $this->dbManager->query(
            'SELECT books.*, users.pseudo AS owner_pseudo, users.profile_picture AS owner_profile_picture
             FROM books
             INNER JOIN users ON users.uuid = books.owner_uuid
             WHERE books.status IN (:available, :reserved)
             ORDER BY books.created_at DESC',
            [
                'available' => 'available',
                'reserved' => 'reserved',
            ]
        );

        return array_map(
            static fn (array $book): Book => Book::fromArray($book),
            $query->fetchAll()
        );
    }

    /**
     * @return Book[]
     */
    public function findLatest(int $limit = 4): array
    {
        $query = $this->dbManager->query(
            'SELECT books.*, users.pseudo AS owner_pseudo, users.profile_picture AS owner_profile_picture
             FROM books
             INNER JOIN users ON users.uuid = books.owner_uuid
             WHERE books.status IN (:available, :reserved)
             ORDER BY books.created_at DESC
             LIMIT ' . max(1, $limit),
            [
                'available' => 'available',
                'reserved' => 'reserved',
            ]
        );

        return array_map(
            static fn (array $book): Book => Book::fromArray($book),
            $query->fetchAll()
        );
    }

    /**
     * @return Book[]
     */
    public function findByOwnerUuid(string $ownerUuid): array
    {
        $query = $this->dbManager->query(
            'SELECT books.*, users.pseudo AS owner_pseudo, users.profile_picture AS owner_profile_picture
             FROM books
             INNER JOIN users ON users.uuid = books.owner_uuid
             WHERE books.owner_uuid = :owner_uuid
             ORDER BY books.created_at DESC',
            ['owner_uuid' => $ownerUuid]
        );

        return array_map(
            static fn (array $book): Book => Book::fromArray($book),
            $query->fetchAll()
        );
    }

    public function countByOwnerUuid(string $ownerUuid): int
    {
        $query = $this->dbManager->query(
            'SELECT COUNT(*) AS books_count
             FROM books
             WHERE owner_uuid = :owner_uuid',
            ['owner_uuid' => $ownerUuid]
        );

        $result = $query->fetch();

        return (int) ($result['books_count'] ?? 0);
    }

    public function findByUuid(string $uuid): ?Book
    {
        $query = $this->dbManager->query(
            'SELECT books.*, users.pseudo AS owner_pseudo, users.profile_picture AS owner_profile_picture
             FROM books
             INNER JOIN users ON users.uuid = books.owner_uuid
             WHERE books.uuid = :uuid
             LIMIT 1',
            ['uuid' => $uuid]
        );

        $book = $query->fetch();

        return $book ? Book::fromArray($book) : null;
    }

    public function findByUuidAndOwnerUuid(string $uuid, string $ownerUuid): ?Book
    {
        $query = $this->dbManager->query(
            'SELECT books.*, users.pseudo AS owner_pseudo, users.profile_picture AS owner_profile_picture
             FROM books
             INNER JOIN users ON users.uuid = books.owner_uuid
             WHERE books.uuid = :uuid
             AND books.owner_uuid = :owner_uuid
             LIMIT 1',
            [
                'uuid' => $uuid,
                'owner_uuid' => $ownerUuid,
            ]
        );

        $book = $query->fetch();

        return $book ? Book::fromArray($book) : null;
    }

    public function update(
        string $uuid,
        string $ownerUuid,
        string $title,
        string $author,
        string $description,
        string $image,
        string $status
    ): void {
        $this->dbManager->query(
            'UPDATE books
             SET title = :title,
                 author = :author,
                 description = :description,
                 image = :image,
                 status = :status,
                 updated_at = NOW(),
                 exchanged_at = CASE WHEN :status_for_exchange = :exchanged THEN COALESCE(exchanged_at, NOW()) ELSE NULL END
             WHERE uuid = :uuid
             AND owner_uuid = :owner_uuid',
            [
                'uuid' => $uuid,
                'owner_uuid' => $ownerUuid,
                'title' => $title,
                'author' => $author,
                'description' => $description,
                'image' => $image,
                'status' => $status,
                'status_for_exchange' => $status,
                'exchanged' => 'exchanged',
            ]
        );
    }

    public function updateImage(string $uuid, string $ownerUuid, string $image): void
    {
        $this->dbManager->query(
            'UPDATE books
             SET image = :image,
                 updated_at = NOW()
             WHERE uuid = :uuid
             AND owner_uuid = :owner_uuid',
            [
                'uuid' => $uuid,
                'owner_uuid' => $ownerUuid,
                'image' => $image,
            ]
        );
    }

    public function delete(string $uuid, string $ownerUuid): void
    {
        $this->dbManager->query(
            'DELETE FROM books
             WHERE uuid = :uuid
             AND owner_uuid = :owner_uuid',
            [
                'uuid' => $uuid,
                'owner_uuid' => $ownerUuid,
            ]
        );
    }

    public function create(
        string $title,
        string $author,
        string $description,
        string $image,
        string $ownerUuid,
        string $status = 'available'
    ): Book {
        $uuid = self::generateUuid();

        $this->dbManager->query(
            'INSERT INTO books (uuid, title, author, description, image, owner_uuid, status)
             VALUES (:uuid, :title, :author, :description, :image, :owner_uuid, :status)',
            [
                'uuid'        => $uuid,
                'title'       => $title,
                'author'      => $author,
                'description' => $description,
                'image'       => $image,
                'owner_uuid'  => $ownerUuid,
                'status'      => $status,
            ]
        );

        $book = $this->findByUuid($uuid);

        if ($book === null) {
            throw new RuntimeException('Livre introuvable apres creation.');
        }

        return $book;
    }

    private static function generateUuid(): string
    {
        $data    = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
