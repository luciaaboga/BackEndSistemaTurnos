<?php
require_once __DIR__ . '/../models/Admin.php';

class AdminController {
    private $model;

    public function __construct() {
        $this->model = new Admin();
    }

    public function login($data) {
        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            return ['status' => 'error', 'message' => 'Email y contraseña son requeridos'];
        }

        $admin = $this->model->getByEmail($data['email']);

        if (!$admin || !password_verify($data['password'], $admin['password'])) {
            http_response_code(401);
            return ['status' => 'error', 'message' => 'Credenciales inválidas'];
        }

        // No devolver el hash de password al frontend
        unset($admin['password']);

        return [
            'status' => 'success',
            'data' => $admin
        ];
    }
}
?>