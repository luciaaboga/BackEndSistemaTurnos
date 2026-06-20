<?php
require_once __DIR__ . '/../config/database.php';

class Admin {
    private $pdo;
    private $table = 'admins';

    public function __construct() {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }

    // Buscar admin por email (para el login)
    public function getByEmail($email) {
        $stmt = $this->pdo->prepare("
            SELECT id, nombre, apellido, dni, email, password
            FROM {$this->table}
            WHERE email = ?
        ");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
}
?>