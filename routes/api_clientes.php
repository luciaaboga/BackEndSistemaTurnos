<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ClienteController.php';

// Instanciar controlador
$clienteController = new ClienteController();

// Manejar solicitudes
$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
    if ($id) {
        $response = $clienteController->getById($id);
    } else {
        $response = $clienteController->getAll();
    }
    echo json_encode($response);
    exit;
}

// Manejo de OPTIONS para CORS
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Método no permitido
http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
?>