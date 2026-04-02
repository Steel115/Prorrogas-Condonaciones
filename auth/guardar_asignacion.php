<?php
session_start();
require_once '../config/db.php';
date_default_timezone_set('America/Mexico_City'); // ✅ Zona horaria correcta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['rol'] !== 'admin') {
        die("Acceso denegado.");
    }

    $titulo = $_POST['titulo'];
    $instrucciones = $_POST['instrucciones'];
    $terminos = $_POST['terminos'];
    $ciclo = $_POST['ciclo_escolar'];
    // ✅ Convertir formato datetime-local a MySQL
    $fecha_limite = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $_POST['fecha_limite'])));
    $creado_por = $_SESSION['id'];

    try {
        $sql = "INSERT INTO asignaciones (titulo, instrucciones, terminos, ciclo_escolar, fecha_limite, creado_por) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titulo, $instrucciones, $terminos, $ciclo, $fecha_limite, $creado_por]);

        header("Location: ../vistas/admin_dashboard.php?success=Asignación creada");
    } catch (PDOException $e) {
        die("Error al guardar: " . $e->getMessage());
    }
}