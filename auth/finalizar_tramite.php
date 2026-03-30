<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth_check.php';
permitirAcceso(['admin', 'contribuyente']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_solicitud = $_POST['id_solicitud'];
    $id_revisor = $_SESSION['id'];

    try {
        // ✅ Solo cambia estatus, nunca toca el campo comentarios
        $sql = "UPDATE solicitudes SET 
                estatus = 'Finalizada', 
                ultima_modificacion = NOW(),
                id_revisor_actual = ?
                WHERE id_solicitud = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_revisor, $id_solicitud]);

        header("Location: ../vistas/revisar_solicitud.php?id=$id_solicitud&msg=Trámite finalizado");
    } catch (PDOException $e) {
        die("Error al finalizar: " . $e->getMessage());
    }
}
exit;