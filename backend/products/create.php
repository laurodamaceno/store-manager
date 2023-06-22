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

            $image = $data['image'] == '' || $data['image'] == null ? 'placeholder.svg' : $data['image'] ;
            $title = $data['title'];
            $slug = SlugGenerator::generateSlug($title);
            $description = $data['description'] == '' || $data['description'] == null ? 'No description' : $data['description'];
            $price = $data['price'];
            $stock_qty = $data['stock_qty'];
            $type = $data['type'];

            $middlewareAuth = new MiddlewareAuth();
            $validating_session = $middlewareAuth->validateSession($db);

            if (!$validating_session) {
                http_response_code(401);                
                $response = [
                    'success' => false,
                    'message' => 'Not authorized.',
                ];
            } else {
                if ($title === '') {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Title is required.'
                    ];
                } elseif ($price === '' || $price <= 0) {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Invalid price.'
                    ];
                } elseif ($stock_qty === '' || $stock_qty <= 0) {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Stock quantity invalid.'
                    ];
                } elseif ($type === '') {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Product type is invalid.'
                    ];
                } else {
                    try {
                        $user = $validating_session;
        
                        $sql = "INSERT INTO products (title, image, slug, description, price, stock_qty, type, author) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $db->getConnection()->prepare($sql);
        
                        $stmt->bindParam( 1, $title);
                        $stmt->bindParam( 2, $image);
                        $stmt->bindParam( 3, $slug);
                        $stmt->bindParam( 4, $description);
                        $stmt->bindParam( 5, $price);
                        $stmt->bindParam( 6, $stock_qty);
                        $stmt->bindParam( 7, $type);
                        $stmt->bindParam( 9, $user);
        
                        $stmt->execute();
        
                        if ($stmt->rowCount() > 0) {
                            http_response_code(200);
                            $response = [
                                'success' => true,
                                'message' => 'Product successfully registered!'
                            ];
                        } else {
                            http_response_code(401);
                            $response = [
                                'success' => false,
                                'message' => 'Failed to register the product.'
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