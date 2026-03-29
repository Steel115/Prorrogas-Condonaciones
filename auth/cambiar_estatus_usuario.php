<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth_check.php';
permitirAcceso(['admin']);

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Obtener estatus actual
        $stmt = $pdo->prepare("SELECT estatus FROM usuarios WHERE num_trabajador = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            header("Location: ../vistas/admin_usuarios.php?error=Usuario no encontrado");
            exit;
        }

        // Toggle: si está activo lo desactiva y viceversa
        $nuevo_estatus = ($usuario['estatus'] == 1) ? 0 : 1;

        $upd = $pdo->prepare("UPDATE usuarios SET estatus = ? WHERE num_trabajador = ?");
        $upd->execute([$nuevo_estatus, $id]);

        $msg = ($nuevo_estatus == 1) ? 'Usuario activado' : 'Usuario desactivado';
        header("Location: ../vistas/admin_usuarios.php?msg=" . urlencode($msg));

    } catch (PDOException $e) {
        header("Location: ../vistas/admin_usuarios.php?error=" . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../vistas/admin_usuarios.php");
}
exit;