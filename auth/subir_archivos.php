<?php
session_start();
require_once '../config/db.php';
require_once '../includes/validar_archivo.php'; // ✅ Validación centralizada

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documentos'])) {
    $id_asignacion = $_POST['id_asignacion'];
    $num_control = $_SESSION['id'];
    $fecha_actual = date('Y-m-d_H-i-s');

    try {
        $pdo->beginTransaction();

        // ✅ Si ya existe solicitud la usa, si no crea una nueva
        if (!empty($_POST['id_solicitud_existente'])) {
            $id_solicitud = $_POST['id_solicitud_existente'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO solicitudes (id_asignacion, num_control_alumno, estatus) VALUES (?, ?, 'Pendiente')");
            $stmt->execute([$id_asignacion, $num_control]);
            $id_solicitud = $pdo->lastInsertId();
        }

        $directorio_destino = "../uploads/" . $num_control . "/";
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }

        foreach ($_FILES['documentos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['documentos']['error'][$key] !== UPLOAD_ERR_OK) {
                // ✅ Error de tamaño detectado por PHP antes de llegar al servidor
                if ($_FILES['documentos']['error'][$key] === UPLOAD_ERR_INI_SIZE || 
                    $_FILES['documentos']['error'][$key] === UPLOAD_ERR_FORM_SIZE) {
                    throw new Exception("Uno de los archivos excede el tamaño máximo permitido de 5MB.");
                }
                continue;
            }

            $nombre_original = $_FILES['documentos']['name'][$key];

            // ✅ Validación segura: MIME type real + tamaño + extensión
            $extension = validarArchivo($tmp_name, $nombre_original);

            $nombre_sistema = $num_control . "_Asig" . $id_asignacion . "_" . $key . "_" . $fecha_actual . "." . $extension;
            $ruta_final = $directorio_destino . $nombre_sistema;

            if (move_uploaded_file($tmp_name, $ruta_final)) {
                $stmtFile = $pdo->prepare("INSERT INTO expediente_archivos 
                    (id_solicitud, tipo_archivo, nombre_archivo_sistema, nombre_original, ruta_fisica, subido_por) 
                    VALUES (?, 'requisito', ?, ?, ?, ?)");
                $stmtFile->execute([$id_solicitud, $nombre_sistema, $nombre_original, $ruta_final, $num_control]);
            }
        }

        // ✅ Si es una solicitud existente, limpiar comentario
        if (!empty($_POST['id_solicitud_existente'])) {
            $pdo->prepare("UPDATE solicitudes SET comentarios = NULL WHERE id_solicitud = ?")->execute([$id_solicitud]);
        }

        $pdo->commit();
        header("Location: ../vistas/detalle_tramite.php?id=$id_asignacion&msg=Solicitud enviada con éxito");

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        // ✅ Redirigir con mensaje de error amigable en lugar de die()
        header("Location: ../vistas/detalle_tramite.php?id=$id_asignacion&error=" . urlencode($e->getMessage()));
        exit;
    }
}