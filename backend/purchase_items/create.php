<?php

    require_once '../src/connection.php';
    require_once '../src/middleware-auth.php';
    
    class CreatePurchaseItemController
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

            $sale_id = $data['sale_id'];
            $item_id = $data['item_id'];
            $qty = $data['qty'];
            $unit_price = $data['unit_price'];

            $middlewareAuth = new MiddlewareAuth();
            $validating_session = $middlewareAuth->validateSession($db);

            if (!$validating_session) {
                http_response_code(401);                
                $response = [
                    'success' => false,
                    'message' => 'Not authorized.',
                ];
            } else {
                if ($sale_id === '' || $sale_id <= 0) {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Sale ID invalid.'
                    ];
                } elseif ($item_id === '' || $item_id <= 0) {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Item ID invalid.'
                    ];
                } elseif ($qty === '' || $qty <= 0) {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Quantity is invalid.'
                    ];
                } elseif ($unit_price === '' || $unit_price <= 0) {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Unit price is invalid.'
                    ];
                } else {
                    try {
                        //$user = $validating_session;

                        $sql = "INSERT INTO purchase_items (sale_id, item_id, qty, unit_price) VALUES (?, ?, ?, ?)";
                        $stmt = $db->getConnection()->prepare($sql);
        
                        $stmt->bindParam( 1, $sale_id);
                        $stmt->bindParam( 2, $item_id);
                        $stmt->bindParam( 3, $qty);
                        $stmt->bindParam( 4, $unit_price);
        
                        $stmt->execute();
        
                        if ($stmt->rowCount() > 0) {
                            http_response_code(200);
                            $response = [
                                'success' => true,
                                'message' => 'Purchase item successfully registered!'
                            ];
                        } else {
                            http_response_code(401);
                            $response = [
                                'success' => false,
                                'message' => 'Failed to register the purchase item.'
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
    
    $createPurchaseItemController = new CreatePurchaseItemController();
    $createPurchaseItemController->processRequest();