<?php
session_start();
require_once '../config/db.php';

if ($_SESSION['rol'] !== 'admin' || !isset($_GET['id'])) {
    header("Location: ../vistas/admin_dashboard.php");
    exit;
}

$id = $_GET['id'];

try {
    $checkSql = "SELECT COUNT(*) FROM solicitudes 
                 WHERE id_asignacion = ? 
                 AND (estatus = 'Pendiente' || estatus = 'Pago pendiente' || estatus = 'En revisión')";
    $stmtCheck = $pdo->prepare($checkSql);
    $stmtCheck->execute([$id]);
    $solicitudesActivas = $stmtCheck->fetchColumn();

    if ($solicitudesActivas > 0) {
        header("Location: ../vistas/admin_dashboard.php?error=No se puede eliminar: Hay alumnos con trámites activos (Pendientes o en Pago). Finaliza o rechaza sus solicitudes primero.");
        exit;
    }
    $pdo->beginTransaction();

    $delArchivos = $pdo->prepare("DELETE FROM expediente_archivos WHERE id_solicitud IN (SELECT id_solicitud FROM solicitudes WHERE id_asignacion = ?)");
    $delArchivos->execute([$id]);

    $delSolicitudes = $pdo->prepare("DELETE FROM solicitudes WHERE id_asignacion = ?");
    $delSolicitudes->execute([$id]);

    $delAsignacion = $pdo->prepare("DELETE FROM asignaciones WHERE id_asignacion = ?");
    $delAsignacion->execute([$id]);

    $pdo->commit();
    header("Location: ../vistas/admin_dashboard.php?msg=Asignación y registros relacionados eliminados con éxito.");

} catch (PDOException $e) {
    $pdo->rollBack();
    die("Error crítico al eliminar: " . $e->getMessage());
}