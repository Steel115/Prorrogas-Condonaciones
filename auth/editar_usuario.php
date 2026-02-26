<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['rol'] === 'admin') {
    $id = $_POST['id_usuario'];
    $nombre = $_POST['nombre_completo'];
    $num_trabajador = $_POST['num_trabajador'];
    $area = $_POST['area_trabajo'];
    $rol = $_POST['rol'];
    $password = $_POST['password'];

    try {
        if (!empty($password)) {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET 
                    nombre_completo = ?, 
                    num_trabajador = ?, 
                    area_trabajo = ?, 
                    rol = ?, 
                    password = ? 
                    WHERE id_usuario = ?";
            $params = [$nombre, $num_trabajador, $area, $rol, $pass_hash, $id];
        } else {
            $sql = "UPDATE usuarios SET 
                    nombre_completo = ?, 
                    num_trabajador = ?, 
                    area_trabajo = ?, 
                    rol = ? 
                    WHERE id_usuario = ?";
            $params = [$nombre, $num_trabajador, $area, $rol, $id];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        header("Location: ../vistas/admin_usuarios.php?msg=Usuario actualizado con éxito");
    } catch (PDOException $e) {
        header("Location: ../vistas/admin_usuarios.php?error=" . urlencode($e->getMessage()));
    }
}