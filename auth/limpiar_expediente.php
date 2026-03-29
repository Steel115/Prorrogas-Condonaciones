<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['rol'] === 'alumno') {
    $id_solicitud = $_POST['id_solicitud'];
    $id_asignacion = $_POST['id_asignacion'];

    try {
        $pdo->beginTransaction();

        // 1. Obtener rutas de archivos para borrarlos del servidor
        $stmtFiles = $pdo->prepare("SELECT ruta_fisica FROM expediente_archivos WHERE id_solicitud = ?");
        $stmtFiles->execute([$id_solicitud]);
        $archivos = $stmtFiles->fetchAll();

        foreach ($archivos as $archivo) {
            if (file_exists($archivo['ruta_fisica'])) {
                unlink($archivo['ruta_fisica']);
            }
        }

        // 1. Eliminar los registros de los archivos
        $pdo->prepare("DELETE FROM expediente_archivos WHERE id_solicitud = ?")->execute([$id_solicitud]);

        // 2. ELIMINAR LA SOLICITUD COMPLETA
        // Esto hace que $t_status sea falso en la siguiente carga
        $pdo->prepare("DELETE FROM solicitudes WHERE id_solicitud = ?")->execute([$id_solicitud]);
        $pdo->commit();   
        // Redirigir a la misma página de detalle
        header("Location: ../vistas/detalle_tramite.php?id=$id_asignacion&msg=Expediente listo para corregir");
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        die("Error al limpiar expediente: " . $e->getMessage());
    }
}