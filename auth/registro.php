<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_control = $_POST['num_control'];
    $nombre = $_POST['nombre_completo'];
    $pass = $_POST['password'];

    // Revisa si el alumno esta en la base de datos de Tec
    $checkEstudiante = $pdo->prepare("SELECT num_control FROM bd_estudiantes WHERE num_control = ?");
    $checkEstudiante->execute([$num_control]);
    
    if ($checkEstudiante->rowCount() === 0) {
        die("Error: El número de control no existe en el sistema escolar.");
    }

    // Revisa si no se ha registrado antes
    $checkRegistro = $pdo->prepare("SELECT num_control FROM alumnos WHERE num_control = ?");
    $checkRegistro->execute([$num_control]);

    if ($checkRegistro->rowCount() > 0) {
        die("Error: Este alumno ya tiene una cuenta creada.");
    }

    // Se encripta la contraseña
    $passwordHash = password_hash($pass, PASSWORD_BCRYPT);

    // Los registra en la tabla de alumnos de la base de datos del sistema
    try {
        $stmt = $pdo->prepare("INSERT INTO alumnos (num_control, nombre_completo, password) VALUES (?, ?, ?)");
        $stmt->execute([$num_control, $nombre, $passwordHash]);
        
        echo "¡Registro exitoso! Ahora puedes iniciar sesión.";
    } catch (PDOException $e) {
        echo "Error al registrar: " . $e->getMessage();
    }
}