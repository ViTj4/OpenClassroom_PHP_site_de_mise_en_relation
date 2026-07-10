<?php

function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key           = trim($key);
        $value         = trim($value);
        $value         = trim($value, "\"'");

        if ($key === '') {
            continue;
        }

        $_ENV[$key] = $value;
        putenv($key . '=' . $value);
    }
}

function env(string $key, ?string $default = null): ?string
{
    $value = $_ENV[$key] ?? getenv($key);

    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return $value;
}

function requiredEnv(string $key): string
{
    $value = env($key);

    if ($value === null) {
        throw new RuntimeException(sprintf('La variable d\'environnement %s est manquante.', $key));
    }

    return $value;
}

loadEnv(dirname(__DIR__) . '/.env');

if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    $sessionSavePath = env('SESSION_SAVE_PATH');

    if ($sessionSavePath !== null) {
        // SESSION_SAVE_PATH peut être absolu ou relatif à la racine du projet.
        if (!preg_match('/^([A-Za-z]:[\/\\\\]|\/)/', $sessionSavePath)) {
            $sessionSavePath = dirname(__DIR__) . '/' . $sessionSavePath;
        }

        // Le dossier de sessions est créé automatiquement pour simplifier l'installation locale.
        if (!is_dir($sessionSavePath)) {
            // Le mode 0775 permet à l'utilisateur et au groupe d'écrire dans le dossier.
            mkdir($sessionSavePath, 0775, true);
        }
        // Le chemin de sauvegarde des sessions est configuré pour que PHP puisse y écrire.
        session_save_path($sessionSavePath);
    }

    // session_start() rend $_SESSION disponible pour l'authentification, les CSRF tokens et les toasts.
    session_start();
}
