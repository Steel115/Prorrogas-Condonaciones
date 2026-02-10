<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['num_control'] ?? $_POST['num_trabajador'];
    $password = $_POST['password'];
    $acepto_terminos = isset($_POST['terminos']); // El checkbox de aceptar los terminos y condiciones

    if (!$acepto_terminos) {
        header("Location: ../vistas/login.php?error=Debes aceptar los términos");
        exit;
    }
    // Se busca si hay un usuario en la tabla de alumnos
    $stmt = $pdo->prepare("SELECT * FROM alumnos WHERE num_control = ?");
    $stmt->execute([$identifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['num_control'];
        $_SESSION['nombre'] = $user['nombre_completo'];
        $_SESSION['rol'] = 'alumno';
        header("Location: ../vistas/alumno/inicio.php");
        exit;
    }

    // Se busca si hay un susario en la tabal de usuarios
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE num_trabajador = ? AND estatus = 1");
    $stmt->execute([$identifier]);
    $staff = $stmt->fetch();

    if ($staff && password_verify($password, $staff['password'])) {
        $_SESSION['user_id'] = $staff['num_trabajador'];
        $_SESSION['nombre'] = $staff['nombre_completo'];
        $_SESSION['rol'] = ($staff['rol'] == 1) ? 'admin' : 'contribuyente';
        
        // segun ru rol que tenga lo manda a una vista u otra 
        if ($_SESSION['rol'] === 'admin') {
            header("Location: ../vistas/admin/dashboard.php");
        } else {
            header("Location: ../vistas/contribuyente/solicitudes.php");
        }
        exit;
    }
    // Si no hay usuarios en las dos tablas mandara error 
    header("Location: ../vistas/login.php?error=Credenciales incorrectas");
}