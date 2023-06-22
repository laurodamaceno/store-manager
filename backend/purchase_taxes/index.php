<?php
    // index.php
    
    // Get URL
    $url = $_SERVER['REQUEST_URI'];

    // Remove .php extension in URL
    $full_url = ltrim($url, '/');

    $route = str_replace('.php', '', $full_url);
    $route_parts = explode('/', $route);
    $action = isset($route_parts[1]) ? $route_parts[1] : '';
    
    $module = 'purchase_taxes/';

    $id = isset($route_parts[2]) ? $route_parts[2] : '';

    // Check the indicated route and select the respective page
    if ($action === 'create') {
        include './create.php';
    } elseif ($action === 'read') {
        if ($id === '') {
            include './read.php';
        } else {
            include './read-one.php';
        }        
    } elseif ($action === 'update') {
        //if ($id !== '') {
            include './update.php';
        //}
    } elseif ($action === 'delete') {
        //if ($id !== '') {
            include './delete.php';
        //}
    } else {
        // Página não encontrada
        include '../404.php';
    }
