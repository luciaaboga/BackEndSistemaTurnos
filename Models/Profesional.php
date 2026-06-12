<?php
require_once __DIR__ . '/../config/database.php';

class Profesional {
    private $pdo;
    private $table = 'profesionales';

    public function __construct() {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }

    // Obtener todos los profesionales
    public function getAll() {
    $stmt = $this->pdo->query("
        SELECT p.id, u.nombre, u.apellido, u.email, p.especialidad, p.descripcion
        FROM {$this->table} p
        INNER JOIN users u ON p.id = u.id
    ");
    return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT p.id, u.nombre, u.apellido, u.email, p.especialidad, p.descripcion
            FROM {$this->table} p
            INNER JOIN users u ON p.id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
?>