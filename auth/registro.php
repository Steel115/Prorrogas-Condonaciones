<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_control = $_POST['num_control'];
    $nombre = $_POST['nombre_completo'];
    $pass = $_POST['password'];

    // Revisa si el alumno está en la base de datos del Tec
    $checkEstudiante = $pdo->prepare("SELECT num_control FROM bd_estudiantes WHERE num_control = ?");
    $checkEstudiante->execute([$num_control]);

    if ($checkEstudiante->rowCount() === 0) {
        header("Location: ../vistas/registro.php?error=" . urlencode("El número de control no existe en el sistema escolar."));
        exit;
    }

    // Revisa si no se ha registrado antes
    $checkRegistro = $pdo->prepare("SELECT num_control FROM alumnos WHERE num_control = ?");
    $checkRegistro->execute([$num_control]);

    if ($checkRegistro->rowCount() > 0) {
        header("Location: ../vistas/registro.php?error=" . urlencode("Este número de control ya tiene una cuenta creada."));
        exit;
    }

    // Encriptar contraseña y registrar
    $passwordHash = password_hash($pass, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO alumnos (num_control, nombre_completo, password) VALUES (?, ?, ?)");
        $stmt->execute([$num_control, $nombre, $passwordHash]);

        // ✅ Redirigir al login con mensaje de éxito
        header("Location: ../vistas/login.php?msg=" . urlencode("¡Registro exitoso! Ya puedes iniciar sesión."));
        exit;

    } catch (PDOException $e) {
        header("Location: ../vistas/registro.php?error=" . urlencode("Error al registrar. Intenta de nuevo."));
        exit;
    }
}