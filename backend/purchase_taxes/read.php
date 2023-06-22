<?php

    require_once '../src/connection.php';

    class ReadPurchaseTaxesController
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
            try {
                $db = new Database();

                $sql = "SELECT * FROM purchase_taxes";
                $stmt = $db->getConnection()->prepare($sql);

                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($stmt->rowCount() > 0) {
                    http_response_code(200);
                    $response = [
                        'success' => true,
                        'message' => 'Ready purchase taxes list!',
                        'results' => $result
                    ];
                } else {
                    http_response_code(200);
                    $response = [
                        'success' => true, 
                        'message' => 'No purchase taxes registered!'
                    ];
                }

            } catch (PDOException $e) {
                http_response_code(403);
                $response = [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ];
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

    $readPurchaseTaxesController = new ReadPurchaseTaxesController();
    $readPurchaseTaxesController->processRequest();