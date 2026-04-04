<?php 
require_once '../config/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/verificar_vencimientos.php'; // Auto-cierra asignaciones vencidas
permitirAcceso(['admin']);
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        
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
                                        data-titulo="<?php echo htmlspecialchars($asig['titulo'], ENT_QUOTES); ?>"
                                        data-ciclo="<?php echo htmlspecialchars($asig['ciclo_escolar'], ENT_QUOTES); ?>"
                                        data-fecha="<?php echo date('Y-m-d\TH:i', strtotime($asig['fecha_limite'])); ?>"
                                        data-instrucciones="<?php echo htmlspecialchars($asig['instrucciones'], ENT_QUOTES); ?>"
                                        data-terminos="<?php echo htmlspecialchars($asig['terminos'], ENT_QUOTES); ?>"
                                        onclick="llenarModalEditar(this)">
                                    ✏️ Editar
                                </button>
                                <button type="button"
                                    class="btn btn-warning btn-sm btn-archivar"
                                    data-url="../auth/archivar_asignacion.php?id=<?php echo $asig['id_asignacion']; ?>"
                                    data-titulo="<?php echo htmlspecialchars($asig['titulo'], ENT_QUOTES); ?>">
                                    📁 Archivar
                                </button>
                                <button type="button"
                                    class="btn btn-outline-danger btn-sm btn-eliminar"
                                    data-url="../auth/eliminar_asignacion.php?id=<?php echo $asig['id_asignacion']; ?>"
                                    data-titulo="<?php echo htmlspecialchars($asig['titulo'], ENT_QUOTES); ?>">
                                    ❌
                                </button>
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
                                    <button type="button"
                                        class="btn btn-sm btn-outline-success btn-restaurar"
                                        data-url="../auth/restaurar_asignacion.php?id=<?php echo $arch['id_asignacion']; ?>"
                                        data-titulo="<?php echo htmlspecialchars($arch['titulo'], ENT_QUOTES); ?>">
                                        🔄 Restaurar
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger btn-eliminar"
                                        data-url="../auth/eliminar_asignacion.php?id=<?php echo $arch['id_asignacion']; ?>"
                                        data-titulo="<?php echo htmlspecialchars($arch['titulo'], ENT_QUOTES); ?>">
                                        ❌
                                    </button>
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
    <?php include 'modales/modal_confirmacion.php'; ?>
    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function llenarModalEditar(boton) {
        document.getElementById('edit_id').value = boton.getAttribute('data-id');
        document.getElementById('edit_titulo').value = boton.getAttribute('data-titulo');
        document.getElementById('edit_ciclo').value = boton.getAttribute('data-ciclo');
        document.getElementById('edit_fecha').value = boton.getAttribute('data-fecha');
        document.getElementById('edit_instrucciones').value = boton.getAttribute('data-instrucciones');
        document.getElementById('edit_terminos').value = boton.getAttribute('data-terminos');
    }

    // Botones Archivar
    document.querySelectorAll('.btn-archivar').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const titulo = this.getAttribute('data-titulo');
            confirmarAccion(
                '¿Deseas archivar "' + titulo + '"? Dejará de ser visible para los alumnos.',
                function() { window.location.href = url; },
                'warning'
            );
        });
    });

    // Botones Eliminar
    document.querySelectorAll('.btn-eliminar').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const titulo = this.getAttribute('data-titulo');
            confirmarAccion(
                '⚠️ ¿Eliminar "' + titulo + '" permanentemente? Esta acción no se puede deshacer.',
                function() { window.location.href = url; },
                'danger'
            );
        });
    });

    // Botones Restaurar
    document.querySelectorAll('.btn-restaurar').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const titulo = this.getAttribute('data-titulo');
            confirmarAccion(
                '¿Deseas restaurar "' + titulo + '"? Volverá a ser visible para los alumnos.',
                function() { window.location.href = url; },
                'success'
            );
        });
    });
    </script>
</body>
</html>