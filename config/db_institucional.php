<?php
// ══════════════════════════════════════════
// Conexión a la BD Institucional (solo lectura)
// ✅ Campos conocidos:
//    aluctr  — número de control
//    aluapp  — apellido paterno
//    aluapm  — apellido materno
//    alunom  — nombre
//    alumai  — correo institucional
//    carrera — nombre del campo pendiente confirmar
// ══════════════════════════════════════════

$host_inst = 'localhost';
$db_inst   = 'PENDIENTE';  // ← reemplaza con el nombre real de la BD institucional
$user_inst = 'PENDIENTE';  // ← reemplaza con el usuario
$pass_inst = 'PENDIENTE';  // ← reemplaza con la contraseña
$charset   = 'utf8mb4';

$dsn_inst = "mysql:host=$host_inst;dbname=$db_inst;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo_inst = new PDO($dsn_inst, $user_inst, $pass_inst, $options);
} catch (\PDOException $e) {
    // Mientras no esté configurada, $pdo_inst será null y no tronará el sistema
    $pdo_inst = null;
}
?>