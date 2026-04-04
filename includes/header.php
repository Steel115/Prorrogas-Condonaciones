<?php
$paginaActual = basename($_SERVER['PHP_SELF']);
$rol = $_SESSION['rol'] ?? null;
?>

<style>
    /* HEADER */
    .hdr {
        background-color: #1c3c6c;
        min-height: 75px;
        display: flex;
        align-items: center;
        padding: 0 2rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    }
    .hdr-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    /* Título */
    .hdr-titulo {
        color: #ffffff;
        font-size: 1.2rem;
        font-weight: 700;
        letter-spacing: 0.2px;
        white-space: nowrap;
    }

    /* imagen */
    .hdr-logo {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* usuario y botón */
    .hdr-usuario {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .hdr-usuario-info {
        text-align: right;
        line-height: 1.4;
    }
    .hdr-usuario-nombre {
        color: #ffffff;
        font-weight: 600;
        font-size: 0.95rem;
        display: block;
    }
    .hdr-usuario-rol {
        color: rgba(255,255,255,0.55);
        font-size: 0.8rem;
    }

    /* Botón de cerrar sesión */
    .hdr-btn-salir {
        background-color: #c0392b;
        color: #ffffff;
        border: 1.5px solid #ffffff;
        border-radius: 5px;
        padding: 0.35rem 0.9rem;
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none;
        white-space: nowrap;
        transition: background-color 0.2s ease;
        cursor: pointer;
    }
    .hdr-btn-salir:hover {
        background-color: #a93226;
        color: #ffffff;
    }

    /* Nav para admin y contribuyente*/
    .hdr-nav {
        background-color: #15305a;
        border-bottom: 2px solid #0f2240;
        padding: 0 2rem;
        display: flex;
        align-items: center;
    }
    .hdr-nav ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        gap: 0.25rem;
    }
    .hdr-nav ul li a {
        display: block;
        color: rgba(255,255,255,0.6);
        text-decoration: none;
        font-size: 0.9rem;
        padding: 0.65rem 1rem;
        border-radius: 4px;
        transition: color 0.2s, background-color 0.2s;
    }
    .hdr-nav ul li a:hover {
        color: #ffffff;
        background-color: rgba(255,255,255,0.1);
    }
    .hdr-nav ul li a.activo {
        color: #ffffff;
        background-color: rgba(255,255,255,0.18);
        font-weight: 600;
    }
</style>

<!-- Barra principal -->
<header class="hdr">
    <div class="hdr-inner">

        <!-- Izquierda: nombre del sistema -->
        <span class="hdr-titulo">Sistema de Prórrogas y Condonaciones</span>

        <!-- Centro: logo institucional -->
        <div class="hdr-logo">
            <!-- ✅ Descomenta cuando tengas el logo -->
            <!-- <img src="../assets/img/logo.png" alt="Logo institucional" style="height:48px;"> -->
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