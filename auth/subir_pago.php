<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pagos'])) {
    $id_solicitud = $_POST['id_solicitud'];
    $num_control = $_SESSION['id'];
    $permitidos = ['pdf', 'jpg', 'jpeg', 'png'];

    try {
        $pdo->beginTransaction();

        $directorio_destino = "../uploads/" . $num_control . "/";
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }

        foreach ($_FILES['pagos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['pagos']['error'][$key] !== UPLOAD_ERR_OK) continue;

            $nombre_original = $_FILES['pagos']['name'][$key];
            $ext = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

            if (!in_array($ext, $permitidos)) {
                throw new Exception("Formato no permitido: $nombre_original");
            }

            $nombre_pago = "PAGO_" . $num_control . "_" . time() . "_" . $key . "." . $ext;
            $ruta_pago = $directorio_destino . $nombre_pago;

            if (move_uploaded_file($tmp_name, $ruta_pago)) {
                $stmt = $pdo->prepare("INSERT INTO expediente_archivos 
                    (id_solicitud, tipo_archivo, nombre_archivo_sistema, nombre_original, ruta_fisica, subido_por) 
                    VALUES (?, 'pago', ?, ?, ?, ?)");
                $stmt->execute([$id_solicitud, $nombre_pago, $nombre_original, $ruta_pago, $num_control]);
            }
        }

        // ✅ Actualizar estatus y limpiar comentario para que no aparezca como observación
        $update = $pdo->prepare("UPDATE solicitudes SET estatus = 'Validando pago', comentarios = NULL WHERE id_solicitud = ?");
        $update->execute([$id_solicitud]);

        $stmtAsig = $pdo->prepare("SELECT id_asignacion FROM solicitudes WHERE id_solicitud = ?");
        $stmtAsig->execute([$id_solicitud]);
        $id_asignacion = $stmtAsig->fetchColumn();

        $pdo->commit();
        header("Location: ../vistas/detalle_tramite.php?id=$id_asignacion&msg=Comprobante enviado");

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}