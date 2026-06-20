<?php
require_once __DIR__ . '/../config/database.php';

class Cliente {
    private $pdo;
    private $table = 'clientes';

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

    // Buscar cliente por email (para login). Trae el password para validar.
    public function getByEmail($email) {
        $stmt = $this->pdo->prepare("
            SELECT c.id, u.nombre, u.apellido, u.email, u.dni, u.password, u.rol, u.estado, c.telefono
            FROM {$this->table} c
            INNER JOIN users u ON c.id = u.id
            WHERE u.email = ?
        ");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    // Verifica si el email o dni ya existen en users (cualquier rol)
    public function existeEmailODni($email, $dni) {
        $stmt = $this->pdo->prepare("
            SELECT email, dni FROM users WHERE email = ? OR dni = ?
        ");
        $stmt->execute([$email, $dni]);
        return $stmt->fetch();
    }

    // Crear cliente (inserta en users y luego en clientes)
    public function create($data) {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                INSERT INTO users (nombre, apellido, dni, email, password, rol)
                VALUES (?, ?, ?, ?, ?, 'cliente')
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
                INSERT INTO clientes (id, telefono)
                VALUES (?, ?)
            ");
            $stmt->execute([
                $userId,
                $data['telefono'] ?? null
            ]);

            $this->pdo->commit();
            return $this->getById($userId);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
?>