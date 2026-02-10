<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <span class="navbar-brand">Sistema de Prórrogas</span>
        <div class="text-white">
            <?php echo $_SESSION['nombre']; ?> - <?php echo $_SESSION['user_id']; ?>
            <span class="badge bg-info text-dark"><?php echo ucfirst($_SESSION['rol']); ?></span>
            <a href="../../auth/logout.php" class="btn btn-sm btn-outline-light ms-3">Cerrar sesión</a>
        </div>
    </div>
</nav>