<?php

abstract class AbstractController
{
    protected function render(string $view, string $title, string $page, array $data = []): void
    {
        extract($data);

        ob_start();
        require dirname(__DIR__, 2) . '/' . $view;

        $content = ob_get_clean();
        require dirname(__DIR__, 2) . '/views/layouts/layout.php';
    }

    protected function redirect(string $page): void
    {
        header('Location: index.php?page=' . urlencode($page));
        exit;
    }
}
