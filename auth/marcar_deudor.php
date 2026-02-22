<?php
session_start();
require_once '../config/db.php';

if (isset($_GET['id'])) {
    $id_solicitud = $_GET['id'];
    
    try {
        $pdo->beginTransaction();
        $stmtAlu = $pdo->prepare("SELECT num_control_alumno FROM solicitudes WHERE id_solicitud = ?");
        $stmtAlu->execute([$id_solicitud]);
        $num_control = $stmtAlu->fetchColumn();
        $stmt1 = $pdo->prepare("UPDATE solicitudes SET estatus = 'Deudor' WHERE id_solicitud = ?");
        $stmt1->execute([$id_solicitud]);
        $stmt2 = $pdo->prepare("UPDATE alumnos SET es_deudor = 1 WHERE num_control = ?");
        $stmt2->execute([$num_control]);

        $pdo->commit();
        header("Location: ../vistas/admin_solicitudes.php?msg=Alumno bloqueado como deudor");
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}