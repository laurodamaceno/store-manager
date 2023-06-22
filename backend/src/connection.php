<?php

    class Database {
        private $serverName = "localhost";
        private $database = "store_manager";
        private $uid = "SA";
        private $pwd = "910531#lci";
        private $connection;

        public function __construct() {
            try {
                $this->connection = new PDO("sqlsrv:server=$this->serverName;Database=$this->database;Encrypt=false", $this->uid, $this->pwd);
                //$this->connection = new PDO("sqlsrv:server={$this->serverName};Database={$this->database};Encrypt=false", $this->uid, $this->pwd);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failure with the database: Message - " . $e->getMessage());
            }
        }

        public function getConnection() {
            return $this->connection;
        }
    
        public function query($sql) {
            return $this->connection->query($sql);
        }
    }