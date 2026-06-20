<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $pdo;
    private $table = 'users';

    public function __construct() {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }

    // Buscar usuario por email (cliente o profesional)
    public function getByEmail($email) {
        $stmt = $this->pdo->prepare("
            SELECT id, nombre, apellido, dni, email, password, rol, estado
            FROM {$this->table}
            WHERE email = ?
        ");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
}
?>