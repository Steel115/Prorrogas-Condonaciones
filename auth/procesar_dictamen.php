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

    try {
        // ✅ Solo cambia estatus, nunca toca el campo comentarios
        $sql = "UPDATE solicitudes SET 
                estatus = ?, 
                id_revisor_actual = ?, 
                ultima_modificacion = NOW() 
                WHERE id_solicitud = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nuevo_estatus, $id_revisor, $id_solicitud]);

        header("Location: ../vistas/revisar_solicitud.php?id=$id_solicitud&msg=Dictamen guardado");
    } catch (PDOException $e) {
        die("Error al procesar dictamen: " . $e->getMessage());
    }
}
exit;