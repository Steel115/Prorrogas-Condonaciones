<?php
session_start();
require_once '../config/db.php';
include '../includes/auth_check.php';

// Verificamos que solo personal autorizado entre aquí
permitirAcceso(['admin', 'contribuyente']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validamos que el ID de solicitud exista para evitar errores
    if (!isset($_POST['id_solicitud'])) {
        header("Location: ../vistas/admin_solicitudes.php");
        exit;
    }

    $id_solicitud = $_POST['id_solicitud'];
    $comentario = trim($_POST['comentario']);

    try {
        // Actualizamos el campo comentarios y la fecha de modificación
        $stmt = $pdo->prepare("UPDATE solicitudes SET comentarios = ?, ultima_modificacion = NOW() WHERE id_solicitud = ?");
        $stmt->execute([$comentario, $id_solicitud]);

        // CORRECCIÓN: Redirigir al nombre de archivo correcto (revisar_solicitud.php)
        header("Location: ../vistas/revisar_solicitud.php?id=$id_solicitud&msg=Observación guardada correctamente");
        exit;

    } catch (PDOException $e) {
        // Es mejor mostrar un error genérico o registrarlo en log
        die("Error crítico al guardar en la base de datos: " . $e->getMessage());
    }
} else {
    // Si alguien intenta entrar a este archivo por URL sin enviar el formulario
    header("Location: ../vistas/admin_solicitudes.php");
    exit;
}