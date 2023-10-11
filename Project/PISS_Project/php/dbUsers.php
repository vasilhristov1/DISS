<?php
    class Database {
        private $connection;
        private $insertUser;
        private $selectAllUsers;
        private $selectUserByUsername;
        private $selectUserTypeByID;

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
            $sql = "INSERT INTO users(id, username, password, email) VALUES (NULL, :username, :password, :email)";
            $this->insertUser = $this->connection->prepare($sql);

            $sql = "SELECT * FROM users";
            $this->selectAllUsers = $this->connection->prepare($sql);

            $sql = "SELECT * FROM users WHERE username = :username";
            $this->selectUserByUsername = $this->connection->prepare($sql);

            $sql = "SELECT type FROM users WHERE id = :id";
            $this->selectUserTypeByID = $this->connection->prepare($sql);
        }

        public function registerUser($data) {
            try {
                $this->insertUser->execute($data);

                return ["success" => true, "data" => $data];
            } catch(PDOException $e) {
                $this->connection->rollBack();

                return ["success" => false, "error" => "Connection failed: " . $e->getMessage()];
            }
        }

        public function getAllUsers() {
            try {
                $this->selectAllUsers->execute();
                return ["success" => true, "data" => $this->selectAllUsers];
            } catch (PDOException $e) {
                return ["success" => false, "error" => "Connection failed: " . $e->getMessage()];
            }
        }

        public function getUserByUsername($data) {
            try {
                $this->selectUserByUsername->execute($data);

                return ["success" => true, "data" => $this->selectUserByUsername];
            } catch (PDOException $e) {
                return ["success" => false, "error" => "Connection failed: " . $e->getMessage()];
            }
        }

        public function getTypeByID($data) {
            try {
                $this->selectUserTypeByID->execute($data);

                return ["success" => true, "data" => $this->selectUserTypeByID];
            } catch (PDOException $e) {
                return ["success" => false, "error" => "Connection failed: " . $e->getMessage()];
            }
        }

        function __destruct() {
            $this->connection = null;
        }
    }
?>