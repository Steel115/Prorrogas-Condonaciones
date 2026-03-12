<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documentos'])) {
    $id_asignacion = $_POST['id_asignacion'];
    $num_control = $_SESSION['id'];
    $fecha_actual = date('Y-m-d_H-i-s');
    
    // archivos permitidos
    $permitidos = ['pdf', 'jpg', 'jpeg', 'png'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO solicitudes (id_asignacion, num_control_alumno, estatus) VALUES (?, ?, 'Pendiente')");
        $stmt->execute([$id_asignacion, $num_control]);
        $id_solicitud = $pdo->lastInsertId();

        $directorio_destino = "../uploads/" . $num_control . "/";
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }

        foreach ($_FILES['documentos']['tmp_name'] as $key => $tmp_name) {
            $nombre_original = $_FILES['documentos']['name'][$key];
            $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

            if (!in_array($extension, $permitidos)) {
                throw new Exception("El archivo '{$nombre_original}' no es válido. Solo se permiten: " . implode(", ", $permitidos));
            }

            $nombre_sistema = $num_control . "_Asig" . $id_asignacion . "_" . $key . "_" . $fecha_actual . "." . $extension;
            $ruta_final = $directorio_destino . $nombre_sistema;

            if (move_uploaded_file($tmp_name, $ruta_final)) {
                $stmtFile = $pdo->prepare("INSERT INTO expediente_archivos (id_solicitud, nombre_archivo_sistema, nombre_original, ruta_fisica, subido_por) VALUES (?, ?, ?, ?, ?)");
                $stmtFile->execute([$id_solicitud, $nombre_sistema, $nombre_original, $ruta_final, $num_control]);
            }
        }

        $pdo->commit();
        header("Location: ../vistas/alumno_tramites.php?msg=Solicitud enviada con éxito");

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        die("Error al subir archivos: " . $e->getMessage());
    }
}