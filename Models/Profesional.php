<?php
require_once __DIR__ . '/../config/database.php';

class Profesional {
    private $pdo;
    private $table = 'profesionales';

    public function __construct() {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }

    // Obtener todos los profesionales con sus especialidades
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT p.id, u.nombre, u.apellido, u.email, u.dni, p.descripcion,
                   GROUP_CONCAT(e.id ORDER BY e.nombre SEPARATOR ',') AS especialidad_ids,
                   GROUP_CONCAT(e.nombre ORDER BY e.nombre SEPARATOR ',') AS especialidades
            FROM {$this->table} p
            INNER JOIN users u ON p.id = u.id
            LEFT JOIN profesional_especialidad pe ON p.id = pe.profesional_id
            LEFT JOIN especialidades e ON pe.especialidad_id = e.id
            GROUP BY p.id, u.nombre, u.apellido, u.email, u.dni, p.descripcion
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'formatRow'], $rows);
    }

    // Obtener un profesional por ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT p.id, u.nombre, u.apellido, u.email, u.dni, p.descripcion,
                   GROUP_CONCAT(e.id ORDER BY e.nombre SEPARATOR ',') AS especialidad_ids,
                   GROUP_CONCAT(e.nombre ORDER BY e.nombre SEPARATOR ',') AS especialidades
            FROM {$this->table} p
            INNER JOIN users u ON p.id = u.id
            LEFT JOIN profesional_especialidad pe ON p.id = pe.profesional_id
            LEFT JOIN especialidades e ON pe.especialidad_id = e.id
            WHERE p.id = ?
            GROUP BY p.id, u.nombre, u.apellido, u.email, u.dni, p.descripcion
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->formatRow($row) : null;
    }

    // Convierte las especialidades de string CSV a array
    private function formatRow($row) {
        $row['especialidad_ids'] = $row['especialidad_ids']
            ? array_map('intval', explode(',', $row['especialidad_ids']))
            : [];
        $row['especialidades'] = $row['especialidades']
            ? explode(',', $row['especialidades'])
            : [];
        return $row;
    }

    // Crear profesional
    public function create($data) {
        try {
            $this->pdo->beginTransaction();

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

            $stmt = $this->pdo->prepare("
                INSERT INTO profesionales (id, descripcion) VALUES (?, ?)
            ");
            $stmt->execute([$userId, $data['descripcion'] ?? null]);

            // Insertar especialidades en tabla puente
            if (!empty($data['especialidad_ids'])) {
                $this->syncEspecialidades($userId, $data['especialidad_ids']);
            }

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

            $stmt = $this->pdo->prepare("
                UPDATE users SET nombre = ?, apellido = ?, dni = ?, email = ? WHERE id = ?
            ");
            $stmt->execute([
                $data['nombre'],
                $data['apellido'],
                $data['dni'],
                $data['email'],
                $id
            ]);

            $stmt = $this->pdo->prepare("
                UPDATE profesionales SET descripcion = ? WHERE id = ?
            ");
            $stmt->execute([$data['descripcion'] ?? null, $id]);

            // Sincronizar especialidades
            $this->syncEspecialidades($id, $data['especialidad_ids'] ?? []);

            $this->pdo->commit();
            return $this->getById($id);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // Eliminar profesional
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Sincroniza la tabla puente: borra las viejas y pone las nuevas
    private function syncEspecialidades($profesionalId, $especialidadIds) {
        $stmt = $this->pdo->prepare("DELETE FROM profesional_especialidad WHERE profesional_id = ?");
        $stmt->execute([$profesionalId]);

        if (!empty($especialidadIds)) {
            $stmt = $this->pdo->prepare("
                INSERT INTO profesional_especialidad (profesional_id, especialidad_id) VALUES (?, ?)
            ");
            foreach ($especialidadIds as $espId) {
                $stmt->execute([$profesionalId, (int)$espId]);
            }
        }
    }
}
?>