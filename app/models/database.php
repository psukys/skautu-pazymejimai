<?php
class Database {
    private $_connection;
    private static $_instance;
    private $_host;
    private $_username;
    private $_password;
    private $_database;

    public static function getInstance() {
        if (!self::$_instance) { // if no instance, then make on
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        try {
            // read config
            $conf_file = file_get_contents("app/models/conf.json");
            $conf = json_decode($conf_file, true);
            $this->_host = $conf["host"];
            $this->_database = $conf["database"];
            $this->_username = $conf["username"];
            $this->_password = $conf["password"];
            $this->_connection = new PDO("mysql:host=$this->_host;dbname=$this->_database", $this->_username, $this->_password);
        } catch (PDOException $e) {
            echo "Database constructor error: " . $e->getMessage();
        }
    }

    private function __clone() {}

    public function getConnection() {
        return $this->_connection;
    }
}
?>