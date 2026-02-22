<?php
session_start();
// Lo renvia a Login si no ha iniciado sesión
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}
?>