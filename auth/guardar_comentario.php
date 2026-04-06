<?php
session_start();
require_once '../config/db.php';
include '../includes/auth_check.php';

permitirAcceso(['admin', 'contribuyente']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id_solicitud'])) {
        echo json_encode(['success' => false, 'message' => 'ID no encontrado']);
        exit;
    }

    $id_solicitud = $_POST['id_solicitud'];
    $comentario = trim($_POST['comentario']);

    try {
        $stmt = $pdo->prepare("UPDATE solicitudes SET comentarios = ?, comentario_leido = 0, ultima_modificacion = NOW() WHERE id_solicitud = ?");
        $stmt->execute([$comentario, $id_solicitud]);

        // ✅ Respuesta JSON para el AJAX
        echo json_encode(['success' => true, 'message' => 'Observación guardada correctamente']);
        exit;

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al guardar']);
        exit;
    }
} else {
    header("Location: ../vistas/admin_solicitudes.php");
    exit;
}