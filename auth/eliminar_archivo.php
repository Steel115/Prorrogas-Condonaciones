<?php
// ✅ Evita que cualquier warning o notice rompa el JSON
ob_start();
session_start();
require_once '../config/db.php';
require_once '../includes/auth_check.php';

if ($_SESSION['rol'] !== 'alumno') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_archivo'])) {
    $id_archivo = $_POST['id_archivo'];
    $num_control = $_SESSION['id'];

    try {
        $stmt = $pdo->prepare("SELECT ea.*, s.num_control_alumno 
                               FROM expediente_archivos ea
                               JOIN solicitudes s ON ea.id_solicitud = s.id_solicitud
                               WHERE ea.id_archivo = ? AND s.num_control_alumno = ?");
        $stmt->execute([$id_archivo, $num_control]);
        $archivo = $stmt->fetch();

        if (!$archivo) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Archivo no encontrado']);
            exit;
        }

        if (file_exists($archivo['ruta_fisica'])) {
            unlink($archivo['ruta_fisica']);
        }

        $pdo->prepare("DELETE FROM expediente_archivos WHERE id_archivo = ?")->execute([$id_archivo]);

        ob_end_clean();
        echo json_encode(['success' => true]);

    } catch (PDOException $e) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
exit;