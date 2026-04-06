<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_control = $_POST['num_control'];
    $nombre = $_POST['nombre_completo'];
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password']; // Recibimos el nuevo campo de confirmación

    // --- 1. VALIDACIONES DE SEGURIDAD (SERVIDOR) ---

    // Verificar que las contraseñas coincidan
    if ($pass !== $confirm_pass) {
        header("Location: ../vistas/registro.php?error=" . urlencode("Las contraseñas no coinciden."));
        exit;
    }

    // Verificar longitud mínima
    if (strlen($pass) < 8) {
        header("Location: ../vistas/registro.php?error=" . urlencode("La contraseña debe tener al menos 8 caracteres."));
        exit;
    }

    // --- 2. VALIDACIONES DE BASE DE DATOS ---

    // Revisa si el alumno está en la base de datos del Tec (bd_estudiantes)
    $checkEstudiante = $pdo->prepare("SELECT num_control FROM bd_estudiantes WHERE num_control = ?");
    $checkEstudiante->execute([$num_control]);

    if ($checkEstudiante->rowCount() === 0) {
        header("Location: ../vistas/registro.php?error=" . urlencode("El número de control no existe en el sistema escolar."));
        exit;
    }

    // Revisa si ya existe una cuenta creada para ese número (alumnos)
    $checkRegistro = $pdo->prepare("SELECT num_control FROM alumnos WHERE num_control = ?");
    $checkRegistro->execute([$num_control]);

    if ($checkRegistro->rowCount() > 0) {
        header("Location: ../vistas/registro.php?error=" . urlencode("Este número de control ya tiene una cuenta creada."));
        exit;
    }

    // --- 3. PROCESO DE REGISTRO ---

    // Encriptar contraseña (solo después de validar que todo está correcto)
    $passwordHash = password_hash($pass, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO alumnos (num_control, nombre_completo, password) VALUES (?, ?, ?)");
        $stmt->execute([$num_control, $nombre, $passwordHash]);

        // ✅ Redirigir al login con mensaje de éxito
        header("Location: ../vistas/login.php?msg=" . urlencode("¡Registro exitoso! Ya puedes iniciar sesión."));
        exit;

    } catch (PDOException $e) {
        // Log del error para el desarrollador, pero mensaje genérico para el usuario
        header("Location: ../vistas/registro.php?error=" . urlencode("Error técnico al registrar. Intenta de nuevo."));
        exit;
    }
}