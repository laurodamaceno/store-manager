<?php

    require_once '../src/connection.php';
    require_once '../src/middleware-auth.php';
    
    class CreateProductTaxController
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
            $percentage = $data['percentage'];
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
                if ($title == '') {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Title is required.'
                    ];
                } elseif ($percentage == '') {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Percentage is required.'
                    ];
                } elseif (!is_numeric($percentage)) {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Invalid value to percentage.'
                    ];
                } else {
                    try {
        
                        $sql = "INSERT INTO product_taxes (title, percentage, description) VALUES (:title, :percentage, :description)";
                        $stmt = $db->getConnection()->prepare($sql);
        
                        $stmt->bindParam(':title', $title);
                        $stmt->bindParam(':percentage', number_format($percentage, 2, '.', ''));
                        $stmt->bindParam(':description', $description);
        
                        $stmt->execute();
        
                        if ($stmt->rowCount() > 0) {
                            http_response_code(200);
                            $response = [
                                'success' => true,
                                'message' => 'Product tax successfully registered!'
                            ];
                        } else {
                            http_response_code(401);
                            $response = [
                                'success' => false,
                                'message' => 'Failed to register the product tax.'
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
    
    $createProductTaxController = new CreateProductTaxController();
    $createProductTaxController->processRequest();