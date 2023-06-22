<?php

    require_once '../src/connection.php';
    require_once '../src/middleware-auth.php';
    
    class CreateSaleController
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

            // Set the time zone to Sao Paulo, Brazil
            date_default_timezone_set('America/Sao_Paulo');

            // Get the current date and time
            $currentDateTime = date('Y-m-d H:i:s.u');

            $buyer = $data['buyer'];
            $sale_date = $data['sale_date'] == '' ? $currentDateTime : $data['sale_date'];
            $purchase_total = $data['purchase_total'];
            $sale_status = $data['sale_status'] == '' ? "ordered" : $data['sale_status'];

            $middlewareAuth = new MiddlewareAuth();
            $validating_session = $middlewareAuth->validateSession($db);

            if (!$validating_session) {
                http_response_code(401);
                $response = [
                    'success' => false,
                    'message' => 'Not authorized.'
                ];
            } else {
                if ($buyer === '') {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Invalid buyer.'
                    ];
                } elseif ($purchase_total === '' || $purchase_total <= 0) {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Invalid parchase total.'
                    ];
                } else {
                    try {
        
                        $sql = "INSERT INTO sales (buyer, sale_date, purchase_total, sale_status) VALUES (?, ?, ?, ?)";
                        $stmt = $db->getConnection()->prepare($sql);
        
                        $stmt->bindParam(1, $buyer);
                        $stmt->bindParam(2, $sale_date);
                        $stmt->bindParam(3, $purchase_total);
                        $stmt->bindParam(4, $sale_status);
        
                        $stmt->execute();
        
                        if ($stmt->rowCount() > 0) {
                            http_response_code(200);
                            $response = [
                                'success' => true,
                                'message' => 'This sale was successfully registered!'
                            ];
                        } else {
                            http_response_code(401);
                            $response = [
                                'success' => false,
                                'message' => 'Failed to register this sale.'
                            ];
                        }
                    } catch (PDOException $e) {
                        http_response_code(403);
                        $response = [
                            'success' => false,
                            'message' => 'Error: ' . $e->getMessage(),
                            'data' => $data
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
    
    $createSaleController = new CreateSaleController();
    $createSaleController->processRequest();