<?php
require_once '../config/db.php';
require_once '../config/db_institucional.php'; // ✅ Conexión BD institucional

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_control  = trim($_POST['num_control']);
    $pass         = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

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

    // ✅ Verificar que la conexión institucional esté disponible
    if (!$pdo_inst) {
        header("Location: ../vistas/registro.php?error=" . urlencode("La conexión institucional no está disponible. Contacta al administrador."));
        exit;
    }

    // ✅ Buscar al alumno en la BD institucional y obtener sus datos
    $checkInst = $pdo_inst->prepare("SELECT aluctr, aluapp, aluapm, alunom, alumai 
                                     FROM alumnos_inst 
                                     WHERE aluctr = ?");
    $checkInst->execute([$num_control]);
    $alumnoInst = $checkInst->fetch();

    if (!$alumnoInst) {
        header("Location: ../vistas/registro.php?error=" . urlencode("El número de control no existe en el sistema escolar."));
        exit;
    }

    // Verificar que no se haya registrado antes
    $checkRegistro = $pdo->prepare("SELECT num_control FROM alumnos WHERE num_control = ?");
    $checkRegistro->execute([$num_control]);

    if ($checkRegistro->rowCount() > 0) {
        header("Location: ../vistas/registro.php?error=" . urlencode("Este número de control ya tiene una cuenta creada."));
        exit;
    }

    // --- 3. PROCESO DE REGISTRO ---

    // ✅ Construir nombre completo desde la BD institucional (nombre + apellidos)
    $nombre_completo = trim(
        $alumnoInst['alunom'] . ' ' .
        $alumnoInst['aluapp'] . ' ' .
        $alumnoInst['aluapm']
    );

    $passwordHash = password_hash($pass, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO alumnos (num_control, nombre_completo, password) VALUES (?, ?, ?)");
        $stmt->execute([$num_control, $nombre_completo, $passwordHash]);

        header("Location: ../vistas/login.php?msg=" . urlencode("¡Registro exitoso! Ya puedes iniciar sesión."));
        exit;

    } catch (PDOException $e) {
        header("Location: ../vistas/registro.php?error=" . urlencode("Error técnico al registrar. Intenta de nuevo."));
        exit;
    }
}