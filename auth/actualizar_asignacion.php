<?php
session_start();
require_once '../config/db.php';
date_default_timezone_set('America/Mexico_City'); // ✅ Zona horaria correcta

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['rol'] === 'admin') {
    $id = $_POST['id_asignacion'];
    $titulo = $_POST['titulo'];
    $ciclo = $_POST['ciclo_escolar'];
    $fecha = $_POST['fecha_limite'];
    $inst = $_POST['instrucciones'];
    $term = $_POST['terminos'];

    try {
        // ✅ Convertir formato datetime-local (2026-03-31T17:30) a formato MySQL (2026-03-31 17:30:00)
        $fecha_mysql = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $fecha)));

        // ✅ Comparar timestamp exacto incluyendo hora
        $nuevo_estatus = (strtotime($fecha_mysql) > time()) ? 0 : 2;

        $sql = "UPDATE asignaciones SET titulo=?, ciclo_escolar=?, fecha_limite=?, instrucciones=?, terminos=?, estatus=? WHERE id_asignacion=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titulo, $ciclo, $fecha_mysql, $inst, $term, $nuevo_estatus, $id]);

        header("Location: ../vistas/admin_dashboard.php?msg=Actualizado con éxito");
    } catch (PDOException $e) {
        die("Error al actualizar: " . $e->getMessage());
    }
}