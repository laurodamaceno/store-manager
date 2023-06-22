<?php

    // I'm assuming you're starting this project with:
    // php -S localhost:8080

    http_response_code(200);
    $response = [
        'success' => true, 
        'message' => 'Welcome to Store Manager API! 1.0',
        'author' => 'Lauro Damaceno',
        'created' => '06/2023',
        'my_website' => 'https://ldcoder.dev',
        'my_linkedin' => 'https://www.linkedin.com/in/laurodamaceno/',
        'info' => [
            "title" => 'NOTE!!!',
            "read-me" => "I'm assuming you're starting this project with: php -S localhost:8080"
        ],
        'routes' => [
            'products' => [
                'create' => 'http://localhost:8080/products/create',
                'read' => 'http://localhost:8080/products/read',
                'update' => 'http://localhost:8080/products/update',
                'delete' => 'http://localhost:8080/products/delete',
            ],
            'product_types' => [
                'create' => 'http://localhost:8080/products_types/create',
                'read' => 'http://localhost:8080/products_types/read',
                'update' => 'http://localhost:8080/products_types/update',
                'delete' => 'http://localhost:8080/products_types/delete',
            ],
            'product_taxes' => [
                'create' => 'http://localhost:8080/product_taxes/create',
                'read' => 'http://localhost:8080/product_taxes/read',
                'update' => 'http://localhost:8080/product_taxes/update',
                'delete' => 'http://localhost:8080/product_taxes/delete',
            ],
            'taxes' => [
                'create' => 'http://localhost:8080/taxes/create',
                'read' => 'http://localhost:8080/taxes/read',
                'update' => 'http://localhost:8080/taxes/update',
                'delete' => 'http://localhost:8080/taxes/delete',
            ],
            'purchases_taxes' => [
                'create' => 'http://localhost:8080/purchases_taxes/create',
                'read' => 'http://localhost:8080/purchases_taxes/read',
                'update' => 'http://localhost:8080/purchases_taxes/update',
                'delete' => 'http://localhost:8080/purchases_taxes/delete',
            ],
            'purchases_types' => [
                'create' => 'http://localhost:8080/purchases_types/create',
                'read' => 'http://localhost:8080/purchases_types/read',
                'update' => 'http://localhost:8080/purchases_types/update',
                'delete' => 'http://localhost:8080/purchases_types/delete',
            ],
            'sales' => [
                'create' => 'http://localhost:8080/sales/create',
                'read' => 'http://localhost:8080/sales/read',
                'read-by-buyer' => 'http://localhost:8080/sales/read-by-buyer'
            ]
        ]        
    ];

    // Load response for API
    header('Content-Type: application/json');
    echo json_encode($response);