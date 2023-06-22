<?php

    class MiddlewareAuth
    {
        public function validateSession(Database $db)
        {
            $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
            $credentials = explode(' ', $authorizationHeader);
            $decodedCredentials = base64_decode($credentials[1]);
            list($email, $password) = explode(':', $decodedCredentials);

            if ($email != '' && $password != '') {
                
                try {
                    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
                    $stmt_validate = $db->getConnection()->prepare($sql);

                    $stmt_validate->bindParam( 1 , $email);
                    $stmt_validate->bindParam( 2 , $password);

                    $stmt_validate->execute();

                    $result = $stmt_validate->fetchAll(PDO::FETCH_ASSOC);

                    if ($stmt_validate->rowCount() != 0) {
                        return $result[0]['id'];
                    } else {
                        return false;
                    }
                } catch (PDOException $e) {
                   return true;
                }
                
            } else {
                return false;
            }
        }
    }
