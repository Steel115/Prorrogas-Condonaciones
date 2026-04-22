<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $curp     = strtoupper(trim($_POST['curp']));
    $nombre   = trim($_POST['nombre']);
    $apellido_paterno = trim($_POST['apellido_paterno']);
    $apellido_materno = trim($_POST['apellido_materno']);
    $carrera  = trim($_POST['carrera']);
    $correo   = trim($_POST['correo']);
    $pass     = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if (!isset($_POST['es_nuevo_ingreso'])) {
        header("Location: ../vistas/registro.php?error=" . urlencode("Solicitud inválida."));
        exit;
    }

    if (strlen($curp) !== 18) {
        header("Location: ../vistas/registro.php?error=" . urlencode("El CURP debe tener 18 caracteres."));
        exit;
    }

    if (empty($nombre) || empty($apellido_paterno) || empty($carrera) || empty($correo)) {
        header("Location: ../vistas/registro.php?error=" . urlencode("Todos los campos son obligatorios."));
        exit;
    }

    if ($pass !== $confirm_pass) {
        header("Location: ../vistas/registro.php?error=" . urlencode("Las contraseñas no coinciden."));
        exit;
    }

    $regexPassword = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
    
    if (!preg_match($regexPassword, $pass)) {
        header("Location: ../vistas/registro.php?error=" . urlencode("La contraseña debe incluir letras, números y al menos un carácter especial."));
        exit;
    }

    $checkExisting = $pdo->prepare("SELECT num_control FROM alumnos WHERE num_control = ?");
    $checkExisting->execute([$curp]);
    if ($checkExisting->rowCount() > 0) {
        header("Location: ../vistas/registro.php?error=" . urlencode("Ya existe un registro con ese CURP."));
        exit;
    }

    $nombre_completo = trim($nombre . ' ' . $apellido_paterno . ' ' . $apellido_materno);

    $passwordHash = password_hash($pass, PASSWORD_ARGON2ID);

    try {
        $stmt = $pdo->prepare("INSERT INTO alumnos (num_control, nombre_completo, password, carrera, correo) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$curp, $nombre_completo, $passwordHash, $carrera, $correo]);

        header("Location: ../vistas/login.php?msg=" . urlencode("¡Registro exitoso! Tu número de control es tu CURP."));
        exit;

    } catch (PDOException $e) {
        error_log("Error en registro nuevo: " . $e->getMessage());
        header("Location: ../vistas/registro.php?error=" . urlencode("Error interno al procesar el registro."));
        exit;
    }
} else {
    header("Location: ../vistas/registro.php");
    exit;
}