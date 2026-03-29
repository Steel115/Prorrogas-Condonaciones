<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth_check.php';
permitirAcceso(['admin', 'contribuyente']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_solicitud = $_POST['id_solicitud'];
    $accion = $_POST['accion'];
    $id_revisor = $_SESSION['id'];

    $nuevo_estatus = ($accion === 'aceptar') ? 'Pago pendiente' : 'Rechazada';

    // ✅ Ya NO se toca el campo comentarios
    $sql = "UPDATE solicitudes SET 
            estatus = ?, 
            id_revisor_actual = ?, 
            ultima_modificacion = NOW() 
            WHERE id_solicitud = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nuevo_estatus, $id_revisor, $id_solicitud]);

    header("Location: ../vistas/admin_solicitudes.php?res=Dictamen guardado");
}