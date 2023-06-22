<?php

    require_once '../src/connection.php';
    require_once '../src/middleware-auth.php';
    require_once '../src/slug-generator.php';

    class UpdateProductTypeController
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
                if ($id === '') {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Invalid ID to update.'
                    ];
                } elseif ($title == '' || $title === null) {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Title is required.',
                    ];
                } elseif ($tax === '' || $tax === '' <= 0) {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Tax is required.'
                    ];
                } else {
                    try {

                        $sql0 = "SELECT * FROM product_types WHERE id = :id";
                        $stmt0 = $db->getConnection()->prepare($sql0);

                        $stmt0->bindParam(':id', $id);
                        $stmt0->execute();

                        if ($stmt0->rowCount() != 0) {
                            $sql = "UPDATE product_types SET title = :title, slug = :slug, tax = :tax, description = :description WHERE id = :id";
                            $stmt = $db->getConnection()->prepare($sql);

                            $stmt->bindParam(':title', $title);
                            $stmt->bindParam(':slug', $slug);
                            $stmt->bindParam(':tax', $tax);
                            $stmt->bindParam(':description', $description);
                            $stmt->bindParam(':id', $id);
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

    $updateProductTypeController = new UpdateProductTypeController();
    $updateProductTypeController->processRequest();