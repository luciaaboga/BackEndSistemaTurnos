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
            SELECT p.id, u.nombre, u.apellido, u.email, u.dni, p.especialidad, p.descripcion
            FROM {$this->table} p
            INNER JOIN users u ON p.id = u.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un profesional por ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT p.id, u.nombre, u.apellido, u.email, u.dni, p.especialidad, p.descripcion
            FROM {$this->table} p
            INNER JOIN users u ON p.id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear profesional (inserta en users y luego en profesionales)
    public function create($data) {
        try {
            $this->pdo->beginTransaction();

            // Insertar en users
            $stmt = $this->pdo->prepare("
                INSERT INTO users (nombre, apellido, dni, email, password, rol)
                VALUES (?, ?, ?, ?, ?, 'profesional')
            ");
            $stmt->execute([
                $data['nombre'],
                $data['apellido'],
                $data['dni'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT)
            ]);

            $userId = $this->pdo->lastInsertId();

            // Insertar en profesionales
            $stmt = $this->pdo->prepare("
                INSERT INTO profesionales (id, especialidad, descripcion)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $data['especialidad'],
                $data['descripcion'] ?? null
            ]);

            $this->pdo->commit();
            return $this->getById($userId);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // Actualizar profesional
    public function update($id, $data) {
        try {
            $this->pdo->beginTransaction();

            // Actualizar users
            $stmt = $this->pdo->prepare("
                UPDATE users
                SET nombre = ?, apellido = ?, dni = ?, email = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['nombre'],
                $data['apellido'],
                $data['dni'],
                $data['email'],
                $id
            ]);

            // Actualizar profesionales
            $stmt = $this->pdo->prepare("
                UPDATE profesionales
                SET especialidad = ?, descripcion = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['especialidad'],
                $data['descripcion'] ?? null,
                $id
            ]);

            $this->pdo->commit();
            return $this->getById($id);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // Eliminar profesional (cascade elimina profesionales también)
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>