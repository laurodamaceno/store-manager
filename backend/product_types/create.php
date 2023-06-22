<?php

    require_once '../src/connection.php';
    require_once '../src/middleware-auth.php';
    require_once '../src/slug-generator.php';
    
    class CreateProductTypeController
    {
        public function processRequest()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handlePostRequest();
            } else {
                $this->handleInvalidRequest();
            }
        }
    
        private function handlePostRequest()
        {
            $db = new Database();

            // Receive data by body
            $data = json_decode(file_get_contents('php://input'), true);

            $title = $data['title'];
            $slug = SlugGenerator::generateSlug($title);
            $tax = $data['tax'];
            $description = $data['description'];

            $middlewareAuth = new MiddlewareAuth();
            $validating_session = $middlewareAuth->validateSession($db);

            if (!$validating_session) {
                http_response_code(401);
                $response = [
                    'success' => false,
                    'message' => 'Not authorized.'
                ];
            } else {
                if ($title === '') {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Title is required.'
                    ];
                } elseif ($tax === '' || $tax === '' <= 0) {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Tax is required.'
                    ];
                } else {
                    try {
        
                        $sql = "INSERT INTO product_types (title, slug, tax, description) VALUES (:title, :slug, :tax, :description)";
                        $stmt = $db->getConnection()->prepare($sql);
        
                        $stmt->bindParam(':title', $title);
                        $stmt->bindParam(':slug', $slug);
                        $stmt->bindParam(':tax', $tax);
                        $stmt->bindParam(':description', $description);
        
                        $stmt->execute();
        
                        if ($stmt->rowCount() > 0) {
                            http_response_code(200);
                            $response = [
                                'success' => true,
                                'message' => 'Product type successfully registered!'
                            ];
                        } else {
                            http_response_code(401);
                            $response = [
                                'success' => false,
                                'message' => 'Failed to register the product type.'
                            ];
                        }
                    } catch (PDOException $e) {
                        http_response_code(403);
                        $response = [
                            'success' => false,
                            'message' => 'Error: ' . $e->getMessage()
                        ];
                    }
                }
            }
    
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    
        private function handleInvalidRequest()
        {
            http_response_code(403);
            $response = [
                'success' => false,
                'message' => 'Access denied'
            ];
    
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }
    
    $createProductTypeController = new CreateProductTypeController();
    $createProductTypeController->processRequest();