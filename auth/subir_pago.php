<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pago'])) {
    $id_solicitud = $_POST['id_solicitud'];
    $num_control = $_SESSION['id'];
    
    $file = $_FILES['pago'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nombre_pago = "PAGO_" . $num_control . "_" . time() . "." . $ext;
    $ruta_pago = "../uploads/" . $num_control . "/" . $nombre_pago;

    if (move_uploaded_file($file['tmp_name'], $ruta_pago)) {
        try {
            $pdo->beginTransaction();

            // 1. Insertar el archivo de pago en la tabla de archivos
            $stmt = $pdo->prepare("INSERT INTO expediente_archivos (id_solicitud, tipo_archivo, nombre_archivo_sistema, nombre_original, ruta_fisica, subido_por) VALUES (?, 'pago', ?, ?, ?, ?)");
            $stmt->execute([$id_solicitud, $nombre_pago, $file['name'], $ruta_pago, $num_control]);

            // 2. Actualizar estatus de la solicitud
            $update = $pdo->prepare("UPDATE solicitudes SET estatus = 'Validando pago' WHERE id_solicitud = ?");
            $update->execute([$id_solicitud]);

            $pdo->commit();
            header("Location: ../vistas/alumno_tramites.php?msg=Pago enviado");
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Error: " . $e->getMessage());
        }
    }
}