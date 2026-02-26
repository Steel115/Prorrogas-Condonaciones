<?php
session_start();
require_once '../config/db.php';

if (isset($_GET['id']) && $_SESSION['rol'] === 'admin') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("UPDATE asignaciones SET estatus = 0 WHERE id_asignacion = ?");
    $stmt->execute([$id]);
    header("Location: ../vistas/admin_dashboard.php?msg=Trámite reactivado");
}