<?php
require_once __DIR__ . '/../models/Profesional.php';

class ProfesionalController {
    private $model;

    public function __construct() {
        $this->model = new Profesional();
    }

    // Obtener todos los profesionales (GET)
    public function getAll() {
        $profesionales = $this->model->getAll();
        return [
            'status' => 'success',
            'data' => $profesionales
        ];
    }

    // Obtener un profesional por ID (GET)
    public function getById($id) {
        $profesional = $this->model->getById($id);
        if (!$profesional) {
            return [
                'status' => 'error',
                'message' => 'Profesional no encontrado'
            ];
        }
        return [
            'status' => 'success',
            'data' => $profesional
        ];
    }
}
?>