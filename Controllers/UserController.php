<?php
require_once __DIR__ . '/../Models/Users.php';

class UserController {
    private $model;

    public function __construct() {
        $this->model = new User();
    }

    public function login($data) {
        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            return ['status' => 'error', 'message' => 'Email y contraseña son requeridos'];
        }

        $user = $this->model->getByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            http_response_code(401);
            return ['status' => 'error', 'message' => 'Credenciales inválidas'];
        }

        if ((int)$user['estado'] !== 1) {
            http_response_code(403);
            return ['status' => 'error', 'message' => 'Usuario inactivo. Contacte al administrador'];
        }

        unset($user['password']);

        return [
            'status' => 'success',
            'data' => $user
        ];
    }
}
?>