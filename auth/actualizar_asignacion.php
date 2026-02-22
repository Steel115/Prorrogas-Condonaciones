<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['rol'] === 'admin') {
    $id = $_POST['id_asignacion'];
    $titulo = $_POST['titulo'];
    $ciclo = $_POST['ciclo_escolar'];
    $fecha = $_POST['fecha_limite'];
    $inst = $_POST['instrucciones'];
    $term = $_POST['terminos'];

    try {
        $sql = "UPDATE asignaciones SET titulo=?, ciclo_escolar=?, fecha_limite=?, instrucciones=?, terminos=? WHERE id_asignacion=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titulo, $ciclo, $fecha, $inst, $term, $id]);

        header("Location: ../vistas/admin_dashboard.php?msg=Actualizado con éxito");
    } catch (PDOException $e) {
        die("Error al actualizar: " . $e->getMessage());
    }
}