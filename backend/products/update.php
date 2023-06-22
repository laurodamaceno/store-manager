<?php

    require_once '../src/connection.php';
    require_once '../src/middleware-auth.php';
    require_once '../src/slug-generator.php';

    class UpdateProductController
    {
        public function processRequest()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
                $this->handlePatchRequest();
            } else {
                $this->handleInvalidRequest();
            }
        }

        private function handlePatchRequest()
        {
            $db = new Database();

            // Receive data by URL request
            $url = $_SERVER['REQUEST_URI'];
            $segments = explode('/', $url); 
            $id = $segments[count($segments) - 1]; 

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
                    'message' => 'Not authorized.'
                ];
            } else {
                if ($id === '') {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Invalid ID to update.'
                    ];
                } elseif ($title === '') {
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

                        $sql0 = "SELECT * FROM products WHERE id = :id";
                        $stmt0 = $db->getConnection()->prepare($sql0);

                        $stmt0->bindParam(':id', $id);
                        $stmt0->execute();

                        if ($stmt0->rowCount() != 0) {
                            $sql = "UPDATE products SET title = ?, image  = ?, slug = ?, description = ?, price = ?, stock_qty = ?, type = ? WHERE id = ?";
                            $stmt = $db->getConnection()->prepare($sql);

                            $stmt->bindParam( 1, $title);
                            $stmt->bindParam( 2, $image);
                            $stmt->bindParam( 3, $slug);
                            $stmt->bindParam( 4, $description);
                            $stmt->bindParam( 5, $price);
                            $stmt->bindParam( 6, $stock_qty);
                            $stmt->bindParam( 7, $type);
                            $stmt->bindParam( 8, $id);

                            $stmt->execute();

                            http_response_code(200);
                            $response = [
                                'success' => true,
                                'message' => 'Product type updated!'
                            ];
                        } else {
                            http_response_code(403);
                            $response = [
                                'success' => false, 
                                'message' => 'The specified ID does not exist.'
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

    $updateProductController = new UpdateProductController();
    $updateProductController->processRequest();