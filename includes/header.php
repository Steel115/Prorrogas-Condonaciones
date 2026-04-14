<?php
$paginaActual = basename($_SERVER['PHP_SELF']);
$rol = $_SESSION['rol'] ?? null;
?>
<!-- Barra principal -->
<header class="hdr">
    <div class="hdr-inner">
        <span class="hdr-titulo">Sistema de Prórrogas y Condonaciones</span>
        <div class="hdr-logo my-2">
            <!--<img src="../assets/img/tecnm.ico" alt="Logo" class="img-thumbnail" style="height: 60px;">-->
            <img src="../assets/img/tecnm.png" alt="Logo" style="height: 60px; filter: brightness(0) invert(1);">
        </div>
        <!-- Derecha: info usuario + cerrar sesión -->
        <?php if ($rol): ?>
        <div class="hdr-usuario">
            <div class="hdr-usuario-info">
                <span class="hdr-usuario-nombre"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <?php if ($rol === 'alumno'): ?>
                    <span class="hdr-usuario-rol">Control: <?php echo htmlspecialchars($_SESSION['id']); ?></span>
                <?php elseif ($rol === 'admin'): ?>
                    <span class="hdr-usuario-rol">Administrador</span>
                <?php elseif ($rol === 'contribuyente'): ?>
                    <span class="hdr-usuario-rol">Contribuyente</span>
                <?php endif; ?>
            </div>
            <a href="../auth/logout.php" class="hdr-btn-salir">Cerrar Sesión</a>
        </div>
        <?php endif; ?>

    </div>
</header>

<!-- Nav secundario (solo admin y contribuyente) -->
<?php if ($rol === 'admin' || $rol === 'contribuyente'): ?>
<nav class="hdr-nav">
    <ul>
        <?php if ($rol === 'admin'): ?>
            <li><a href="admin_dashboard.php" class="<?php echo $paginaActual === 'admin_dashboard.php' ? 'activo' : ''; ?>">📋 Asignaciones</a></li>
            <li><a href="admin_usuarios.php" class="<?php echo $paginaActual === 'admin_usuarios.php' ? 'activo' : ''; ?>">👥 Usuarios</a></li>
        <?php endif; ?>
        <li><a href="admin_solicitudes.php" class="<?php echo $paginaActual === 'admin_solicitudes.php' ? 'activo' : ''; ?>">📬 Solicitudes</a></li>
        <li><a href="admin_historial.php" class="<?php echo $paginaActual === 'admin_historial.php' ? 'activo' : ''; ?>">📁 Historial</a></li>
        <li><a href="admin_deudores.php" class="<?php echo $paginaActual === 'admin_deudores.php' ? 'activo' : ''; ?>">⚠️ Deudores</a></li>
    </ul>
</nav>
<?php endif; ?>