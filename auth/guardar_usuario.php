<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['rol'] == 'admin') {
    $num_t = $_POST['num_trabajador'];
    $nombre = $_POST['nombre_completo'];
    $area = $_POST['area_trabajo'];
    $rol = $_POST['rol'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $sql = "INSERT INTO usuarios (num_trabajador, nombre_completo, area_trabajo, rol, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$num_t, $nombre, $area, $rol, $pass]);
        
        header("Location: ../vistas/admin_usuarios.php?success=usuario_creado");
    } catch (PDOException $e) {
        die("Error al crear usuario: " . $e->getMessage());
    }
}