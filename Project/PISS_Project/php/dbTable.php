<?php
    class TableDatabase {
        private $connection;
        private $insertTable;
        private $truncateTable;
        private $selectTable;
        private $selectCellByID;

        public function __construct() {
            $config = parse_ini_file("../database/config.ini", true);
            $type = $config["db"]["type"];
            $host = $config["db"]["host"];
            $name = $config["db"]["name"];
            $user = $config["db"]["user"];
            $pass = $config["db"]["pass"];

            $this->init($type, $host, $name, $user, $pass);
        }

        private function init($type, $host, $name, $user, $password) {
            try {
                $this->connection = new PDO("$type:host=$host;dbname=$name", $user, $password,
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

                $this->prepareStatements();
            } catch(PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
        }

        private function prepareStatements() {
            $sql = "INSERT INTO documenttable (id, class, innervalue, owner, style) VALUES (:id, :class, :innervalue, :owner, :style)";
            $this->insertTable = $this->connection->prepare($sql);

            $sql = "TRUNCATE pissproject.documenttable";
            $this->truncateTable = $this->connection->prepare($sql);

            $sql = "SELECT * FROM documenttable";
            $this->selectTable = $this->connection->prepare($sql);

            $sql = "SELECT * FROM documenttable WHERE id = :id";
            $this->selectCellByID = $this->connection->prepare($sql);
        }

        public function saveTable($data) {

            try {
                $this->insertTable->execute($data);
                return ["success" => true];
            } catch(PDOException $e) {
                try {
                    $this->connection->rollBack();
                } catch (Exception $x) {
                    
                }

                return ["success" => false, "error" => "Connection failed: ". $e->getMessage()];
            }
        }

        public function getSavedTableInfo() {
            try {
                $this->selectTable->execute();
                return ["success" => true, "data" => $this->selectTable];
            } catch (PDOException $e) {
                return ["success" => false, "error" => "Connection failed while receiving table data: " . $e->getMessage()];
            }
        }


        public function emptyTable() {
            try {
                $this->truncateTable->execute();
                return ["success" => true];
            } catch (PDOException $e) {
                return ["success" => false, "error" => "Connection failed: " . $e->getMessage()];
            }
        }
        
        public function getTableByID($data) {
            try {
                $this->selectCellByID->execute($data);
                return ["success" => true, "data" => $this->selectCellByID];
            } catch (PDOException $e) {
                return ["success" => false, "error" => "Connection failed: " . $e->getMessage()];
            }
        }

        function __destruct() {
            $this->connection = null;
        }
    }
?>