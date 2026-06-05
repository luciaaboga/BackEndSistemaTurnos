<?php
class Database {
    //usamos el patron singleton para evitar multiples instancias a la db, y optimizar el rendimiento
    private static $instance = null;

  
    private $pdo;

    // Constructor privado para evitar instanciación directa
    private function __construct() {
        $host = 'localhost';
        $dbname = 'sistematurnos'; 
        $username = 'root'; 
        $password = ''; 

        try {
            $this->pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }


    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }


    public function getConnection() {
        return $this->pdo;
    }


    private function __clone() {}

  
    private function __wakeup() {}
}
?>