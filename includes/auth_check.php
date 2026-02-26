<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

function permitirAcceso($rolesPermitidos) {
    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
        if ($_SESSION['rol'] === 'alumno') {
            header("Location: alumno_tramites.php");
        } else {
            header("Location: admin_dashboard.php?error=No tienes permisos");
        }
        exit;
    }
}
?>