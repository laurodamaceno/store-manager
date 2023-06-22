<?php

    require_once '../src/connection.php';
    require_once '../src/middleware-auth.php';

    class ReadOneSaleController
    {
        public function processRequest()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $this->handleGetRequest();
            } else {
                $this->handleInvalidRequest();
            }
        }

        private function handleGetRequest()
        {
            $db = new Database();

            // Receive data by URL request
            $url = $_SERVER['REQUEST_URI'];
            $segments = explode('/', $url); 
            $id = $segments[count($segments) - 1];

            // Receive data by body
            $data = json_decode(file_get_contents('php://input'), true);
            $type = $data['type'];
            $buyer = $data['buyer'];

            $middlewareAuth = new MiddlewareAuth();
            $validating_session = $middlewareAuth->validateSession($db);

            if (!$validating_session) {
                http_response_code(401);
                $response = [
                    'success' => false,
                    'message' => 'Not authorized.'
                ];
            } else {

                try {

                    // Check is admin
                    if ($type == 'admin') {
                        $sql = "SELECT * FROM sales WHERE id = :id";
                        $stmt = $db->getConnection()->prepare($sql);
                    } else {

                        // Show only sales by buyer if type is not admin
                        $sql = "SELECT * FROM sales WHERE buyer = :buyer AND id = :id";
                        $stmt = $db->getConnection()->prepare($sql);
                        $stmt->bindParam(':buyer', $buyer);
                    }

                    $stmt->bindParam(':id', $id);
                    $stmt->execute();

                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($stmt->rowCount() > 0) {
                        http_response_code(200);
                        $response = [
                            'success' => true,
                            'message' => 'Ready sale to show!',
                            'results' => $result
                        ];
                    } else {
                        http_response_code(200);
                        $response = [
                            'success' => true, 
                            'message' => 'No sale to show!'
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

    $readOneSaleController = new ReadOneSaleController();
    $readOneSaleController->processRequest();