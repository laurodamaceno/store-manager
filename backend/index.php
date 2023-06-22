<?php
    // index.php
    
    // Get URL
    $url = $_SERVER['REQUEST_URI'];

    // Remove .php extension in URL
    $full_url = ltrim($url, '/');
    $route = str_replace('.php', '', $full_url);

    // Check the indicated route and select the respective page
    if ($route === 'welcome' || $route === '') {
        include './welcome.php';
    } elseif ($route === 'login') {
        include './login.php';
    } elseif ($route === 'product_types/create') {
        include './product_types/create.php';
    } elseif ($route === 'product_taxes/create') {
        include './product_taxes/create.php';
    } else {
        // Página não encontrada
        include './404.php';
    }
