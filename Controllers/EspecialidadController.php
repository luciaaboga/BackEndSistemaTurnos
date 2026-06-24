<?php
require_once __DIR__ . '/../models/Especialidad.php';

class EspecialidadController {
    private $model;

    public function __construct() {
        $this->model = new Especialidad();
    }

    public function getAll() {
        return [
            'status' => 'success',
            'data' => $this->model->getAll()
        ];
    }

    public function getById($id) {
        $esp = $this->model->getById($id);
        if (!$esp) {
            http_response_code(404);
            return ['status' => 'error', 'message' => 'Especialidad no encontrada'];
        }
        return ['status' => 'success', 'data' => $esp];
    }

    public function create($data) {
        if (empty($data['nombre'])) {
            http_response_code(400);
            return ['status' => 'error', 'message' => 'El nombre es requerido'];
        }
        try {
            $esp = $this->model->create($data);
            http_response_code(201);
            return ['status' => 'success', 'data' => $esp];
        } catch (Exception $e) {
            http_response_code(500);
            // Error de duplicado
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'Ya existe una especialidad con ese nombre'];
            }
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        $existente = $this->model->getById($id);
        if (!$existente) {
            http_response_code(404);
            return ['status' => 'error', 'message' => 'Especialidad no encontrada'];
        }
        if (empty($data['nombre'])) {
            http_response_code(400);
            return ['status' => 'error', 'message' => 'El nombre es requerido'];
        }
        try {
            $esp = $this->model->update($id, $data);
            return ['status' => 'success', 'data' => $esp];
        } catch (Exception $e) {
            http_response_code(500);
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'Ya existe una especialidad con ese nombre'];
            }
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id) {
        $existente = $this->model->getById($id);
        if (!$existente) {
            http_response_code(404);
            return ['status' => 'error', 'message' => 'Especialidad no encontrada'];
        }
        try {
            $this->model->delete($id);
            return ['status' => 'success', 'message' => 'Especialidad eliminada correctamente'];
        } catch (Exception $e) {
            http_response_code(500);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
?>