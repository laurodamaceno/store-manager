<?php

    // Send response 404
    http_response_code(404);
    $response = [
        'success' => false, 
        'message' => 'Page not found'
    ];

    // Load response for API
    header('Content-Type: application/json');
    echo json_encode($response);