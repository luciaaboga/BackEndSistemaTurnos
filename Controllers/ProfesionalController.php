<?php
require_once __DIR__ . '/../models/Profesional.php';

class ProfesionalController {
    private $model;

    public function __construct() {
        $this->model = new Profesional();
    }

    public function getAll() {
        return [
            'status' => 'success',
            'data' => $this->model->getAll()
        ];
    }

    public function getById($id) {
        $profesional = $this->model->getById($id);
        if (!$profesional) {
            return ['status' => 'error', 'message' => 'Profesional no encontrado'];
        }
        return ['status' => 'success', 'data' => $profesional];
    }

    public function create($data) {
        // especialidad_ids es opcional (puede no tener especialidades al crear)
        $requeridos = ['nombre', 'apellido', 'dni', 'email', 'password'];
        foreach ($requeridos as $campo) {
            if (empty($data[$campo])) {
                http_response_code(400);
                return ['status' => 'error', 'message' => "El campo '$campo' es requerido"];
            }
        }

        try {
            $profesional = $this->model->create($data);
            http_response_code(201);
            return ['status' => 'success', 'data' => $profesional];
        } catch (Exception $e) {
            http_response_code(500);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        $existente = $this->model->getById($id);
        if (!$existente) {
            http_response_code(404);
            return ['status' => 'error', 'message' => 'Profesional no encontrado'];
        }

        $requeridos = ['nombre', 'apellido', 'dni', 'email'];
        foreach ($requeridos as $campo) {
            if (empty($data[$campo])) {
                http_response_code(400);
                return ['status' => 'error', 'message' => "El campo '$campo' es requerido"];
            }
        }

        try {
            $profesional = $this->model->update($id, $data);
            return ['status' => 'success', 'data' => $profesional];
        } catch (Exception $e) {
            http_response_code(500);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id) {
        $existente = $this->model->getById($id);
        if (!$existente) {
            http_response_code(404);
            return ['status' => 'error', 'message' => 'Profesional no encontrado'];
        }

        try {
            $this->model->delete($id);
            return ['status' => 'success', 'message' => 'Profesional eliminado correctamente'];
        } catch (Exception $e) {
            http_response_code(500);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
?>