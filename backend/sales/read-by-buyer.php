<?php

    require_once '../src/connection.php';
    require_once '../src/middleware-auth.php';

    class ReadOneSalebyBuyerController
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

            // Receive data by body
            $data = json_decode(file_get_contents('php://input'), true);
            $buyer = $data['buyer'];

            $middlewareAuth = new MiddlewareAuth();
            $validating_session = $middlewareAuth->validateSession($db);

            if (!$validating_session) {
                http_response_code(401);
                $response = [
                    'success' => false,
                    'message' => 'Not authorized.'
                ];
            } elseif ($buyer === '') {
                http_response_code(401);
                $response = [
                    'success' => false,
                    'message' => 'Buyer ID not found.'
                ];
            } else {

                try {
                    $db = new Database();

                    $sql = "SELECT * FROM sales WHERE buyer = :buyer";
                    $stmt = $db->getConnection()->prepare($sql);

                    $stmt->bindParam(':buyer', $buyer);
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
                        http_response_code(403);
                        $response = [
                            'success' => false, 
                            'message' => 'No sales to show!'
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

    $readOneSalebyBuyerController = new ReadOneSalebyBuyerController();
    $readOneSalebyBuyerController->processRequest();