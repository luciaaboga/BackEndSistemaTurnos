<?php
require_once __DIR__ . '/../config/database.php';

// ---- Datos del admin: cambialos antes de correr el script ----
$nombre   = 'Admin';
$apellido = 'Principal';
$dni      = '00000000';
$email    = 'admin@sistematurnos.com';
$passwordPlano = '123';
// ----------------------------------------------------------------

$db = Database::getInstance()->getConnection();

// Evita duplicar si lo corrés más de una vez
$check = $db->prepare("SELECT id FROM admins WHERE email = ? OR dni = ?");
$check->execute([$email, $dni]);

if ($check->fetch()) {
    die("Ya existe un admin con ese email o dni.\n");
}

$passwordHash = password_hash($passwordPlano, PASSWORD_BCRYPT);

$stmt = $db->prepare("
    INSERT INTO admins (nombre, apellido, dni, email, password)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->execute([$nombre, $apellido, $dni, $email, $passwordHash]);

echo "Admin creado correctamente.\n";
echo "Email: $email\n";
echo "Password: $passwordPlano\n";