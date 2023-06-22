<?php

    require_once './src/connection.php';

    class LoginController
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
            // Receive data by body
            $data = json_decode(file_get_contents('php://input'), true);

            $email = $data['email'];
            $password = $data['password'];
            
            // Just to make the process safer, but no big deal
            $my_basic_encript = 'secret';            
            
            try {
                if ($email == '' || $password == '') {
                    http_response_code(401);
                    $response = [
                        'success' => false,
                        'message' => 'Email and password required.'
                    ];
                } else {
                    $db = new Database();

                    $sql = "SELECT * FROM users WHERE email = :email AND password = :password";
                    $stmt = $db->getConnection()->prepare($sql);

                    $stmt->bindValue(':email', $email);
                    $stmt->bindValue(':password', $password);

                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($stmt->rowCount() > 0) {
                        // Here we can record the session data to turn the process safer
                        // For example: Save the session and the datetime to expires to validate in a middleware

                        http_response_code(200);
                        $response = [
                            'success' => true,
                            'message' => 'Login successful.',
                            'session' => base64_encode($email.':'.$password)                         
                        ];
                    } else {
                        http_response_code(401);
                        $response = [
                            'success' => false,
                            'message' => 'Invalid email or password.'
                        ];
                    }
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

    $loginController = new LoginController();
    $loginController->processRequest();