<?php

abstract class AbstractController
{
    protected function render(string $view, string $title, string $page, array $data = []): void
    {
        $data = $this->withFlashMessages($data);

        // extract() transforme les clés du tableau en variables disponibles dans la vue.
        // Exemple : ['book' => $book] devient directement $book dans le fichier PHP inclus.
        extract($data);

        // ob_start() démarre un buffer de sortie : la vue est exécutée mais son HTML est gardé en mémoire.
        ob_start();
        require dirname(__DIR__, 2) . '/' . $view;

        // ob_get_clean() récupère le HTML généré par la vue puis ferme le buffer.
        // Le layout peut ensuite injecter ce HTML dans sa variable $content.
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

        // Après une redirection, les messages flash restent en session jusqu'au prochain rendu.
        header('Location: index.php?page=' . urlencode($page));
        exit;
    }

    private function withFlashMessages(array $data): array
    {
        $flashErrors = $_SESSION['flash_errors'] ?? [];
        $flashSuccess = $_SESSION['flash_success'] ?? null;

        unset($_SESSION['flash_errors'], $_SESSION['flash_success']);

        // Les messages flash sont consommés une seule fois pour éviter de les réafficher à chaque page.
        if ($flashErrors !== []) {
            $data['errors'] = array_merge($flashErrors, $data['errors'] ?? []);
        }

        if (!isset($data['successMessage']) && $flashSuccess !== null) {
            $data['successMessage'] = $flashSuccess;
        }

        return $data;
    }
}
