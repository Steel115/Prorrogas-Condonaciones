<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['identificador'];
    $pass = $_POST['password'];

    // Busca en la tabla de ALUMNOS
    $stmt = $pdo->prepare("SELECT * FROM alumnos WHERE num_control = ?");
    $stmt->execute([$id]);
    $alumno = $stmt->fetch();

    if ($alumno && password_verify($pass, $alumno['password'])) {
        $_SESSION['id'] = $alumno['num_control'];
        $_SESSION['nombre'] = $alumno['nombre_completo'];
        $_SESSION['rol'] = 'alumno';
        header("Location: ../vistas/alumno_tramites.php");
        exit;
    }

    // Si no es alumno, buscar en USUARIOS
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE num_trabajador = ? AND estatus = 1");
    $stmt->execute([$id]);
    $personal = $stmt->fetch();

    if ($personal && password_verify($pass, $personal['password'])) {
        $_SESSION['id'] = $personal['num_trabajador'];
        $_SESSION['nombre'] = $personal['nombre_completo'];
        // 1 es Admin, 0 es Contribuyente
        $_SESSION['rol'] = ($personal['rol'] == 1) ? 'admin' : 'contribuyente';

        if ($_SESSION['rol'] == 'admin') {
            header("Location: ../vistas/admin_dashboard.php");
        } else {
            header("Location: ../vistas/admin_solicitudes.php");
        }
        exit;
    }

    // Si no se encuentra en ninguna no existe el registro o son incorrecto sus datos
    header("Location: ../vistas/login.php?error=Datos incorrectos");
}