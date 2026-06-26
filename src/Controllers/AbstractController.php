<?php

abstract class AbstractController
{
    protected function render(string $view, string $title, string $page, array $data = []): void
    {
        $data = $this->withFlashMessages($data);

        extract($data);

        ob_start();
        require dirname(__DIR__, 2) . '/' . $view;

        $content = ob_get_clean();
        require dirname(__DIR__, 2) . '/views/layouts/layout.php';
    }

    protected function redirect(string $page, ?string $successMessage = null, ?string $errorMessage = null): void
    {
        if ($successMessage !== null) {
            $_SESSION['flash_success'] = $successMessage;
        }

        if ($errorMessage !== null) {
            $_SESSION['flash_errors'] ??= [];
            $_SESSION['flash_errors'][] = $errorMessage;
        }

        header('Location: index.php?page=' . urlencode($page));
        exit;
    }

    private function withFlashMessages(array $data): array
    {
        $flashErrors = $_SESSION['flash_errors'] ?? [];
        $flashSuccess = $_SESSION['flash_success'] ?? null;

        unset($_SESSION['flash_errors'], $_SESSION['flash_success']);

        if ($flashErrors !== []) {
            $data['errors'] = array_merge($flashErrors, $data['errors'] ?? []);
        }

        if (!isset($data['successMessage']) && $flashSuccess !== null) {
            $data['successMessage'] = $flashSuccess;
        }

        return $data;
    }
}
