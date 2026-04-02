<?php 
require_once '../config/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/verificar_vencimientos.php'; // ✅ Auto-cierra asignaciones vencidas

permitirAcceso(['admin']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nav-tabs .nav-link.active {
            border-bottom: 3px solid #007bff;
            color: #007bff;
        }
    </style>
</head>
<body class="bg-light">

    <header class="p-3 bg-white border-bottom shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <span><strong>ADMINISTRADOR:</strong> <?php echo $_SESSION['nombre']; ?></span>
            <a href="../auth/logout.php" class="btn btn-sm btn-outline-danger ms-2">Cerrar Sesión</a>
        </div>
    </header>

    <div class="container mt-4">
        <ul class="nav nav-tabs mb-4">
            <?php if ($_SESSION['rol'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php') ? 'active' : ''; ?>" href="admin_dashboard.php">Asignaciones</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_usuarios.php') ? 'active' : ''; ?>" href="admin_usuarios.php">Usuarios</a>
                </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_solicitudes.php') ? 'active' : ''; ?>" href="admin_solicitudes.php">Solicitudes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_historial.php') ? 'active' : ''; ?>" href="admin_historial.php">Historial</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_deudores.php') ? 'active' : ''; ?>" href="admin_deudores.php">Deudores</a>
            </li>
        </ul>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Asignaciones Activas</h4>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevaAsignacion">
                + Nueva Asignación
            </button>
        </div>
        
        <div class="row">
            <?php
            // ✅ Traer activas (0) y vencidas (2)
            $stmt = $pdo->prepare("SELECT * FROM asignaciones WHERE estatus IN (0, 2) ORDER BY fecha_creacion DESC");
            $stmt->execute();
            $asignaciones = $stmt->fetchAll();

            foreach ($asignaciones as $asig):
                $vencida = ($asig['estatus'] == 2);
            ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm <?php echo $vencida ? 'border-danger opacity-75' : 'border-primary'; ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($asig['titulo']); ?></h5>
                                <?php if ($vencida): ?>
                                    <span class="badge bg-danger ms-2">⏰ Vencida</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-muted mb-1"><strong>Ciclo:</strong> <?php echo $asig['ciclo_escolar']; ?></p>
                            <p class="<?php echo $vencida ? 'text-danger fw-bold' : 'text-danger'; ?> small">
                                <strong>Fecha Límite:</strong> <?php echo date('d/m/Y H:i', strtotime($asig['fecha_limite'])); ?>
                            </p>
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button class="btn btn-outline-primary btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditarAsignacion"
                                        data-id="<?php echo $asig['id_asignacion']; ?>"
                                        data-titulo="<?php echo htmlspecialchars($asig['titulo']); ?>"
                                        data-ciclo="<?php echo htmlspecialchars($asig['ciclo_escolar']); ?>"
                                        data-fecha="<?php echo date('Y-m-d\TH:i', strtotime($asig['fecha_limite'])); ?>"
                                        data-instrucciones="<?php echo htmlspecialchars($asig['instrucciones']); ?>"
                                        data-terminos="<?php echo htmlspecialchars($asig['terminos']); ?>"
                                        onclick="llenarModalEditar(this)">
                                    ✏️ Editar
                                </button>
                                <a href="../auth/archivar_asignacion.php?id=<?php echo $asig['id_asignacion']; ?>" 
                                class="btn btn-warning btn-sm" 
                                onclick="return confirm('¿Deseas archivar este trámite? Dejará de ser visible para los alumnos.');">
                                📁 Archivar
                                </a>
                                <a href="../auth/eliminar_asignacion.php?id=<?php echo $asig['id_asignacion']; ?>" 
                                class="btn btn-outline-danger btn-sm" 
                                onclick="return confirm('¡ADVERTENCIA! Esta acción borrará el trámite PERMANENTEMENTE de la base de datos. ¿Deseas continuar?');">
                                ❌
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <hr class="my-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-muted"><i class="bi bi-archive"></i>Asignaciones Archivadas</h4>
        </div>

        <div class="row">
            <?php
            $stmtArch = $pdo->prepare("SELECT * FROM asignaciones WHERE estatus = 1 ORDER BY fecha_creacion DESC");
            $stmtArch->execute();
            $archivadas = $stmtArch->fetchAll();

            if (count($archivadas) > 0):
                foreach ($archivadas as $arch): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-secondary bg-light opacity-75">
                            <div class="card-body">
                                <h5 class="card-title text-muted"><?php echo htmlspecialchars($arch['titulo']); ?></h5>
                                <p class="text-muted mb-1 small"><strong>Ciclo:</strong> <?php echo $arch['ciclo_escolar']; ?></p>
                                <p class="text-muted small"><strong>Finalizó:</strong> <?php echo date('d/m/Y', strtotime($arch['fecha_limite'])); ?></p>
                                
                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <a href="../auth/restaurar_asignacion.php?id=<?php echo $arch['id_asignacion']; ?>" 
                                    class="btn btn-sm btn-outline-success" title="Reactivar">
                                        🔄 Restaurar
                                    </a>
                                    <a href="../auth/eliminar_asignacion.php?id=<?php echo $arch['id_asignacion']; ?>" 
                                    class="btn btn-sm btn-outline-danger" 
                                    onclick="return confirm('¿Eliminar permanentemente de la base de datos?');">
                                        ❌
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; 
            else: ?>
                <div class="col-12">
                    <p class="text-center text-muted py-3 italic">No hay trámites archivados en este momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'modales/nueva_asignacion.php'; ?>
    <?php include 'modales/editar_asignacion.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function llenarModalEditar(boton) {
    const id = boton.getAttribute('data-id');
    const titulo = boton.getAttribute('data-titulo');
    const ciclo = boton.getAttribute('data-ciclo');
    const fecha = boton.getAttribute('data-fecha');
    const instrucciones = boton.getAttribute('data-instrucciones');
    const terminos = boton.getAttribute('data-terminos');

    document.getElementById('edit_id').value = id;
    document.getElementById('edit_titulo').value = titulo;
    document.getElementById('edit_ciclo').value = ciclo;
    document.getElementById('edit_fecha').value = fecha;
    document.getElementById('edit_instrucciones').value = instrucciones;
    document.getElementById('edit_terminos').value = terminos;
}
</script>
</body>
</html>