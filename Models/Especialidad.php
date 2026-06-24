<?php
require_once __DIR__ . '/../config/database.php';

class Especialidad {
    private $pdo;
    private $table = 'especialidades';

    public function __construct() {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (nombre) VALUES (?)");
        $stmt->execute([$data['nombre']]);
        return $this->getById($this->pdo->lastInsertId());
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET nombre = ? WHERE id = ?");
        $stmt->execute([$data['nombre'], $id]);
        return $this->getById($id);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>