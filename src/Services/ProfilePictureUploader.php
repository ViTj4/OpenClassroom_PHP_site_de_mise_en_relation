<?php

class ProfilePictureUploader
{
    private const MAX_FILE_SIZE = 2097152;

    private const ALLOWED_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    public function __construct(
        private readonly string $uploadDirectory,
        private readonly string $publicPath
    ) {}

    public function upload(array $file, string $userUuid, ?string $currentPicture = null): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            throw new RuntimeException('Veuillez choisir une image.');
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Impossible de téléverser cette image.');
        }

        if (($file['size'] ?? 0) > self::MAX_FILE_SIZE) {
            throw new RuntimeException('L\'image ne doit pas dépasser 2 Mo.');
        }

        $mimeType = mime_content_type($file['tmp_name']);

        if (!isset(self::ALLOWED_MIME_TYPES[$mimeType])) {
            throw new RuntimeException('Le format de l\'image doit être JPG, PNG ou WebP.');
        }

        if (!is_dir($this->uploadDirectory)) {
            mkdir($this->uploadDirectory, 0775, true);
        }

        $extension = self::ALLOWED_MIME_TYPES[$mimeType];
        $fileName = $userUuid . '-' . bin2hex(random_bytes(8)) . '.' . $extension;
        $destination = $this->uploadDirectory . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('Impossible d\'enregistrer cette image.');
        }

        $this->deletePreviousPicture($currentPicture);

        return $this->publicPath . '/' . $fileName;
    }

    private function deletePreviousPicture(?string $currentPicture): void
    {
        if ($currentPicture === null || !str_starts_with($currentPicture, $this->publicPath . '/')) {
            return;
        }

        $path = $this->uploadDirectory . '/' . basename($currentPicture);

        if (is_file($path)) {
            unlink($path);
        }
    }
}
