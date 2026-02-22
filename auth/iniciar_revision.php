<?php
session_start();
require_once '../config/db.php';

$id_solicitud = $_GET['id'];
$id_revisor = $_SESSION['id'];

// Actualizar estatus y quién lo está revisando
$stmt = $pdo->prepare("UPDATE solicitudes SET estatus = 'En revisión', id_revisor_actual = ? WHERE id_solicitud = ?");
$stmt->execute([$id_revisor, $id_solicitud]);

// Redirigir a la vista de revisión (Imagen 8)
header("Location: ../vistas/revisar_solicitud.php?id=" . $id_solicitud);