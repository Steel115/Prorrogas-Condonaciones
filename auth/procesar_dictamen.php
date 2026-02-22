<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_solicitud = $_POST['id_solicitud'];
    $comentarios = $_POST['comentarios'];
    $accion = $_POST['accion'];
    $id_revisor = $_SESSION['id'];

    // Determinar nuevo estatus
    $nuevo_estatus = ($accion === 'aceptar') ? 'Pago pendiente' : 'Rechazada';

    $sql = "UPDATE solicitudes SET 
            estatus = ?, 
            comentarios = ?, 
            id_revisor_actual = ?, 
            ultima_modificacion = NOW() 
            WHERE id_solicitud = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nuevo_estatus, $comentarios, $id_revisor, $id_solicitud]);

    header("Location: ../vistas/admin_solicitudes.php?res=Dictamen guardado");
}