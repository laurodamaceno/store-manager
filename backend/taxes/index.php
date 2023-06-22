<?php
    // index.php
    
    // Get URL
    $url = $_SERVER['REQUEST_URI'];

    // Remove .php extension in URL
    $full_url = ltrim($url, '/');
    $route = str_replace('.php', '', $full_url);

    // Check the indicated route and select the respective page
    if ($route === 'create') {
        include './create.php';
    } elseif ($route === 'read') {
        include './read.php';
    } elseif ($route === 'update') {
        include './update.php';
    } elseif ($route === 'delete') {
        include './delete.php';
    } else {
        // Página não encontrada
        include '..404.php';
    }
