<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private $model;

    public function __construct() {
        $this->model = new Cliente();
    }

    public function getAll() {
        $clientes = $this->model->getAll();
        return [
            'status' => 'success',
            'data' => $clientes
        ];
    }

    public function getById($id) {
        $cliente = $this->model->getById($id);
        if (!$cliente) {
            return [
                'status' => 'error',
                'message' => 'Cliente no encontrado'
            ];
        }
        return [
            'status' => 'success',
            'data' => $cliente
        ];
    }

    // Registro público de cliente
    public function create($data) {
        $requeridos = ['nombre', 'apellido', 'dni', 'email', 'password'];
        foreach ($requeridos as $campo) {
            if (empty($data[$campo])) {
                http_response_code(400);
                return ['status' => 'error', 'message' => "El campo '$campo' es requerido"];
            }
        }

        $existente = $this->model->existeEmailODni($data['email'], $data['dni']);
        if ($existente) {
            http_response_code(409);
            $campo = $existente['email'] === $data['email'] ? 'email' : 'DNI';
            return ['status' => 'error', 'message' => "Ya existe un usuario registrado con ese $campo"];
        }

        try {
            $cliente = $this->model->create($data);
            http_response_code(201);
            return ['status' => 'success', 'data' => $cliente];
        } catch (Exception $e) {
            http_response_code(500);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
?>