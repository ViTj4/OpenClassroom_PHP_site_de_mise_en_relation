<?php

/**
 * Systeme d'autoload du projet.
 * Quand PHP a besoin d'une classe, on cherche un fichier portant son nom dans
 * les dossiers applicatifs connus.
 */
spl_autoload_register(function ($className) {
  $basePath = dirname(__DIR__);

  $folders = [
    $basePath . '/src/Models',
    $basePath . '/src/Models/Entities',
    $basePath . '/src/Models/Managers',
    $basePath . '/src/Controllers',
    $basePath . '/src/Services',
    $basePath . '/src/Utils',
  ];

  foreach ($folders as $folder) {
    $filePath = $folder . '/' . $className . '.php';

    if (file_exists($filePath)) {
      require_once $filePath;
      return;
    }
  }
});
