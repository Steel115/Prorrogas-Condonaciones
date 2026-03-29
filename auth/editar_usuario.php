<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth_check.php';
permitirAcceso(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_trabajador = $_POST['num_trabajador'];
    $nombre = $_POST['nombre_completo'];
    $area = $_POST['area_trabajo'];
    $rol = $_POST['rol'];
    $password = $_POST['password'];

    try {
        if (!empty($password)) {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET 
                    nombre_completo = ?, 
                    area_trabajo = ?, 
                    rol = ?, 
                    password = ? 
                    WHERE num_trabajador = ?";
            $params = [$nombre, $area, $rol, $pass_hash, $num_trabajador];
        } else {
            $sql = "UPDATE usuarios SET 
                    nombre_completo = ?, 
                    area_trabajo = ?, 
                    rol = ? 
                    WHERE num_trabajador = ?";
            $params = [$nombre, $area, $rol, $num_trabajador];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        header("Location: ../vistas/admin_usuarios.php?msg=Usuario actualizado con éxito");
    } catch (PDOException $e) {
        header("Location: ../vistas/admin_usuarios.php?error=" . urlencode($e->getMessage()));
    }
}
exit;