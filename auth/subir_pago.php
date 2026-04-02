<?php
session_start();
require_once '../config/db.php';
require_once '../includes/validar_archivo.php'; // ✅ Validación centralizada

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pagos'])) {
    $id_solicitud = $_POST['id_solicitud'];
    $num_control = $_SESSION['id'];

    try {
        $pdo->beginTransaction();

        $directorio_destino = "../uploads/" . $num_control . "/";
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }

        // ✅ Borrar comprobantes anteriores
        $stmtViejos = $pdo->prepare("SELECT ruta_fisica FROM expediente_archivos WHERE id_solicitud = ? AND tipo_archivo = 'pago'");
        $stmtViejos->execute([$id_solicitud]);
        foreach ($stmtViejos->fetchAll() as $viejo) {
            if (file_exists($viejo['ruta_fisica'])) unlink($viejo['ruta_fisica']);
        }
        $pdo->prepare("DELETE FROM expediente_archivos WHERE id_solicitud = ? AND tipo_archivo = 'pago'")->execute([$id_solicitud]);

        foreach ($_FILES['pagos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['pagos']['error'][$key] !== UPLOAD_ERR_OK) {
                if ($_FILES['pagos']['error'][$key] === UPLOAD_ERR_INI_SIZE ||
                    $_FILES['pagos']['error'][$key] === UPLOAD_ERR_FORM_SIZE) {
                    throw new Exception("Uno de los archivos excede el tamaño máximo permitido de 5MB.");
                }
                continue;
            }

            $nombre_original = $_FILES['pagos']['name'][$key];

            // ✅ Validación segura: MIME type real + tamaño + extensión
            $ext = validarArchivo($tmp_name, $nombre_original);

            $nombre_pago = "PAGO_" . $num_control . "_" . time() . "_" . $key . "." . $ext;
            $ruta_pago = $directorio_destino . $nombre_pago;

            if (move_uploaded_file($tmp_name, $ruta_pago)) {
                $stmt = $pdo->prepare("INSERT INTO expediente_archivos 
                    (id_solicitud, tipo_archivo, nombre_archivo_sistema, nombre_original, ruta_fisica, subido_por) 
                    VALUES (?, 'pago', ?, ?, ?, ?)");
                $stmt->execute([$id_solicitud, $nombre_pago, $nombre_original, $ruta_pago, $num_control]);
            }
        }

        // ✅ Actualizar estatus y limpiar comentario
        $pdo->prepare("UPDATE solicitudes SET estatus = 'Validando pago', comentarios = NULL WHERE id_solicitud = ?")->execute([$id_solicitud]);

        $stmtAsig = $pdo->prepare("SELECT id_asignacion FROM solicitudes WHERE id_solicitud = ?");
        $stmtAsig->execute([$id_solicitud]);
        $id_asignacion = $stmtAsig->fetchColumn();

        $pdo->commit();
        header("Location: ../vistas/detalle_tramite.php?id=$id_asignacion&msg=Comprobante enviado");

    } catch (Exception $e) {
        $pdo->rollBack();
        $stmtAsig = $pdo->prepare("SELECT id_asignacion FROM solicitudes WHERE id_solicitud = ?");
        $stmtAsig->execute([$id_solicitud]);
        $id_asignacion = $stmtAsig->fetchColumn();
        // ✅ Redirigir con error amigable en lugar de die()
        header("Location: ../vistas/detalle_tramite.php?id=$id_asignacion&error=" . urlencode($e->getMessage()));
        exit;
    }
}