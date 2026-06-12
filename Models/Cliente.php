<?php
require_once __DIR__ . '/../config/database.php';

class Cliente {
    private $pdo;
    private $table = 'clientes'; // Nombre de la tabla en la base de datos

    public function __construct() {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }

    // Obtener todos los clientes
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT c.id, u.nombre, u.apellido, u.email, u.dni, c.telefono
            FROM {$this->table} c
            INNER JOIN users u ON c.id = u.id
        ");
        return $stmt->fetchAll();
    }

    // Obtener un cliente por ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT c.id, u.nombre, u.apellido, u.email, u.dni, c.telefono
            FROM {$this->table} c
            INNER JOIN users u ON c.id = u.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
?>