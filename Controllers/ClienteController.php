<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private $model;

    public function __construct() {
        $this->model = new Cliente();
    }

    // Obtener todos los clientes (GET)
    public function getAll() {
        $clientes = $this->model->getAll();
        return [
            'status' => 'success',
            'data' => $clientes
        ];
    }

    // Obtener un cliente por ID (GET)
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
}
?>