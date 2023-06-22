<?php

    require_once '../src/connection.php';
    require_once '../src/middleware-auth.php';

    class UpdatePurchaseItemController
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
                    'message' => 'Not authorized.'
                ];
            } else {
                if ($id === '') {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Invalid ID to update.'
                    ];
                } elseif ($sale_id === '' || $sale_id <= 0) {
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

                        $sql0 = "SELECT * FROM purchase_items WHERE id = :id";
                        $stmt0 = $db->getConnection()->prepare($sql0);

                        $stmt0->bindParam(':id', $id);
                        $stmt0->execute();

                        if ($stmt0->rowCount() != 0) {
                            $sql = "UPDATE purchase_items SET sale_id = ?, item_id = ?, qty = ?, unit_price = ? WHERE id = ?";
                            $stmt = $db->getConnection()->prepare($sql);

                            $stmt->bindParam( 1, $sale_id);
                            $stmt->bindParam( 2, $item_id);
                            $stmt->bindParam( 3, $qty);
                            $stmt->bindParam( 4, $unit_price);
                            $stmt->bindParam( 5, $id);

                            $stmt->execute();

                            http_response_code(200);
                            $response = [
                                'success' => true,
                                'message' => 'Purchase item updated!'
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

    $updatePurchaseItemController = new UpdatePurchaseItemController();
    $updatePurchaseItemController->processRequest();