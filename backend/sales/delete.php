<?php

    require_once '../src/connection.php';
    require_once '../src/middleware-auth.php';

    class ProductTypeDeleteController
    {
        public function processRequest()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                $this->handleDeleteRequest();
            } else {
                $this->handleInvalidRequest();
            }
        }

        private function handleDeleteRequest()
        {
            // Receive data by URL request
            $url = $_SERVER['REQUEST_URI'];
            $segments = explode('/', $url); 
            $id = $segments[count($segments) - 1]; 

            $data = json_decode(file_get_contents('php://input'), true);

            $session = $data['session'];

            $middlewareAuth = new MiddlewareAuth();
            $validating_session = $middlewareAuth->validateSession($session);

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
                        'message' => 'Invalid ID to delete.'
                    ];
                } else {
                    try {
                        $db = new Database();

                        $sql0 = "SELECT id FROM product_types WHERE id = :id";
                        $stmt0 = $db->getConnection()->prepare($sql0);

                        $stmt0->bindParam(':id', $id);
                        $stmt0->execute();

                        if ($stmt0->rowCount() != 0) {
                            $sql = "DELETE FROM product_types WHERE id = :id";
                            $stmt = $db->getConnection()->prepare($sql);

                            $stmt->bindParam(':id', $id);
                            $stmt->execute();

                            http_response_code(200);
                            $response = [
                                'success' => true,
                                'message' => 'Product type deleted!'
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

    $productTypeDeleteController = new ProductTypeDeleteController();
    $productTypeDeleteController->processRequest();