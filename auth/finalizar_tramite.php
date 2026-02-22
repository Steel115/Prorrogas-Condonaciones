<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_solicitud = $_POST['id_solicitud'];
    $id_revisor = $_SESSION['id'];

    try {
        $sql = "UPDATE solicitudes SET 
                estatus = 'Finalizada', 
                ultima_modificacion = NOW(),
                id_revisor_actual = ?
                WHERE id_solicitud = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_revisor, $id_solicitud]);

        header("Location: ../vistas/admin_solicitudes.php?finalizado=true");
    } catch (PDOException $e) {
        die("Error al finalizar: " . $e->getMessage());
    }
}