<?php

    require_once '../src/connection.php';

    class ReadOneProductTypeController
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
            // Receive data by URL request
            $url = $_SERVER['REQUEST_URI'];
            $segments = explode('/', $url); 
            $id = $segments[count($segments) - 1];

            try {
                $db = new Database();

                $sql = is_numeric($id) ?  "SELECT * FROM product_types WHERE id = :id" : "SELECT * FROM product_types WHERE slug = :id" ;
                $stmt = $db->getConnection()->prepare($sql);

                $stmt->bindParam(':id', $id);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($stmt->rowCount() > 0) {
                    http_response_code(200);
                    $response = [
                        'success' => true,
                        'message' => 'Ready product types list!',
                        'results' => $result
                    ];
                } else {
                    http_response_code(403);
                    $response = [
                        'success' => false, 
                        'message' => 'No product types registered!'
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

    $readOneProductTypeController = new ReadOneProductTypeController();
    $readOneProductTypeController->processRequest();