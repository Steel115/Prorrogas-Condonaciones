<?php
require_once '../config/db.php';
require_once '../config/db_institucional.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_control  = trim($_POST['num_control']);
    $pass         = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    // --- 1. VALIDACIONES DE SEGURIDAD (SERVIDOR) ---

    // Verificar coincidencia
    if ($pass !== $confirm_pass) {
        header("Location: ../vistas/registro.php?error=" . urlencode("Las contraseñas no coinciden."));
        exit;
    }

    /**
     * ✅ VALIDACIÓN DE COMPLEJIDAD (REGEX)
     * Debe contener: al menos una letra, un número y un carácter especial (@$!%*?&)
     * Mínimo 8 caracteres.
     */
    $regexPassword = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
    
    if (!preg_match($regexPassword, $pass)) {
        header("Location: ../vistas/registro.php?error=" . urlencode("La contraseña debe incluir letras, números y al menos un carácter especial."));
        exit;
    }

    // --- 2. VALIDACIONES DE BASE DE DATOS ---

    if (!$pdo_inst) {
        header("Location: ../vistas/registro.php?error=" . urlencode("Error de conexión institucional."));
        exit;
    }

    // Buscar al alumno con los nombres de columna oficiales (aluctr, alunom, etc.)
    $checkInst = $pdo_inst->prepare("SELECT aluctr, aluapp, aluapm, alunom FROM alumnos_inst WHERE aluctr = ?");
    $checkInst->execute([$num_control]);
    $alumnoInst = $checkInst->fetch();

    if (!$alumnoInst) {
        header("Location: ../vistas/registro.php?error=" . urlencode("El número de control no pertenece al padrón del ITGAM."));
        exit;
    }

    // Verificar duplicados en el sistema local
    $checkRegistro = $pdo->prepare("SELECT num_control FROM alumnos WHERE num_control = ?");
    $checkRegistro->execute([$num_control]);

    if ($checkRegistro->rowCount() > 0) {
        header("Location: ../vistas/registro.php?error=" . urlencode("Este número de control ya está registrado."));
        exit;
    }

    // --- 3. PROCESO DE REGISTRO ---

    $nombre_completo = trim($alumnoInst['alunom'] . ' ' . $alumnoInst['aluapp'] . ' ' . $alumnoInst['aluapm']);

    /**
     * ✅ CAMBIO A ARGON2ID
     * Es más seguro que BCRYPT y es el que estás usando para los administrativos.
     */
    $passwordHash = password_hash($pass, PASSWORD_ARGON2ID);

    try {
        $stmt = $pdo->prepare("INSERT INTO alumnos (num_control, nombre_completo, password) VALUES (?, ?, ?)");
        $stmt->execute([$num_control, $nombre_completo, $passwordHash]);

        header("Location: ../vistas/login.php?msg=" . urlencode("¡Registro exitoso! Ya puedes entrar."));
        exit;

    } catch (PDOException $e) {
        error_log("Error en registro: " . $e->getMessage()); // Log para debug
        header("Location: ../vistas/registro.php?error=" . urlencode("Error interno al procesar el registro."));
        exit;
    }
}