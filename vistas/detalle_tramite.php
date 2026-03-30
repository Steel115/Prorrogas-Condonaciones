<?php 
require_once '../config/db.php'; 
include '../includes/auth_check.php'; 

if ($_SESSION['rol'] !== 'alumno') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_asignacion = $_GET['id'];
    $num_control = $_SESSION['id'];
    $stmt = $pdo->prepare("SELECT * FROM asignaciones WHERE id_asignacion = ?");
    $stmt->execute([$id_asignacion]);
    $t = $stmt->fetch();

    if (!$t) {
        header("Location: alumno_tramites.php");
        exit;
    }
    $stmtStatus = $pdo->prepare("SELECT id_solicitud, estatus, comentarios FROM solicitudes WHERE id_asignacion = ? AND num_control_alumno = ?");
    $stmtStatus->execute([$id_asignacion, $num_control]);
    $t_status = $stmtStatus->fetch();
    $solicitud_id = ($t_status) ? $t_status['id_solicitud'] : null;

} else {
    header("Location: alumno_tramites.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Trámites</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <header class="p-3 bg-white border-bottom shadow-sm">
            <div class="container d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h4 mb-0 text-primary">Sistema de Prorrogas y Condonaciones</h1>
                </div>
                <div class="text-end">
                    <span class="fw-bold d-block"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                    <small class="text-muted">Control: <?php echo $_SESSION['id']; ?></small>
                    <a href="../auth/logout.php" class="btn btn-sm btn-outline-danger ms-2">Cerrar Sesión</a>
                </div>
            </div>
        </header>
    <main class="container mt-5">
        <h2 class="mb-4"><?php echo htmlspecialchars($t['titulo']); ?></h2>
        <?php if (!$t_status): ?>
            <div class="card p-4 mb-4 shadow-sm">
                <h5 class="text-info"><i class="bi bi-info-circle"></i> Instrucciones del Proceso</h5>
                <div class="p-3 bg-white border rounded">
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($t['instrucciones'])); ?></p>
                </div>
            </div>

            <div id="seccionTerminos" class="alert alert-warning p-4 mb-4 shadow-sm border-2">
                <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Términos y Condiciones</h5>
                <p><?php echo nl2br(htmlspecialchars($t['terminos'])); ?></p>
                <hr>
                <div class="d-flex gap-3">
                    <button type="button" id="btnAcepto" class="btn btn-success">Sí, acepto los términos</button>
                    <button type="button" id="btnNoAcepto" class="btn btn-danger">No acepto</button>
                </div>
            </div>

            <div id="seccionSubida" class="card p-4 shadow-sm d-none">
                <h5>Subir Documentación</h5>
                <p class="text-muted small">Solo archivos PDF, JPG, PNG (Máx. 5MB)</p>
                <form action="../auth/subir_archivos.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_asignacion" value="<?php echo $id_asignacion; ?>">
                    <input type="file" name="documentos[]" class="form-control mb-3" multiple id="inputArchivos" 
                        accept=".pdf, .jpg, .jpeg, .png" required>
                    <button type="submit" id="btnEnviar" class="btn btn-primary w-100">Enviar Solicitud</button>
                </form>
            </div>

        <?php elseif ($t_status['estatus'] == 'Deudor'): ?>
            <div class="alert alert-danger p-5 text-center shadow">
                <h2>⚠️ Estatus: DEUDOR</h2>
                <p class="lead">Se ha detectado una irregularidad en tu proceso.</p>
                <p>Por favor, acude a las oficinas de Recursos Financieros para aclarar tu situación y poder continuar con tu trámite.</p>
                <hr>
                <a href="alumno_tramites.php" class="btn btn-danger">Regresar a mis trámites</a>
            </div>

    <?php elseif ($t_status['estatus'] == 'Pendiente' || $t_status['estatus'] == 'En revisión'): ?>
    <div class="alert alert-light border p-4 shadow-sm">
        <h4 class="text-center mb-4"><i class="bi bi-clock-history text-primary"></i> Estado del Trámite</h4>
        
        <?php if (!empty($t_status['comentarios'])): ?>
            <div class="alert alert-danger border-start border-4 mb-4">
                <h6 class="fw-bold"><i class="bi bi-exclamation-circle"></i> Observación para corregir:</h6>
                <p class="mb-0"><?php echo htmlspecialchars($t_status['comentarios']); ?></p>
            </div>

            <div class="row g-4">
                <div class="col-md-7">
                    <div class="p-3 bg-white border rounded text-center">
                        <h5><i class="bi bi-pencil-square"></i> Editar Documentación</h5>
                        <p class="small text-muted">Haz clic abajo para eliminar los archivos anteriores y subir los nuevos.</p>
                        
                        <form action="../auth/limpiar_expediente.php" method="POST" onsubmit="return confirm('¿Deseas quitar tus archivos actuales para reemplazarlos?');">
                            <input type="hidden" name="id_solicitud" value="<?php echo $solicitud_id; ?>">
                            <input type="hidden" name="id_asignacion" value="<?php echo $id_asignacion; ?>">
                            <button type="submit" class="btn btn-danger w-100 py-2">
                                <i class="bi bi-trash"></i> Limpiar y Volver a Subir
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="col-md-5">
                    <div class="p-3 bg-light border rounded">
                        <h6>Tus archivos actuales:</h6>
                        <ul class="list-group list-group-flush shadow-sm">
                            <?php
                            $stmtActuales = $pdo->prepare("SELECT nombre_original FROM expediente_archivos WHERE id_solicitud = ?");
                            $stmtActuales->execute([$solicitud_id]);
                            while($f = $stmtActuales->fetch()): ?>
                                <li class="list-group-item bg-transparent small">📄 <?php echo htmlspecialchars($f['nombre_original']); ?></li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="text-center py-4">
                <div class="mb-3">
                    <span class="badge rounded-pill bg-primary px-4 py-2 fs-6">
                        <i class="bi bi-search"></i> Documentación en Revisión
                    </span>
                </div>
                <p class="lead mb-1">¡Hemos recibido tus documentos!</p>
                <p class="text-muted">El personal administrativo está validando tu información. Te notificaremos por este medio si hay algún cambio en el estatus.</p>
                <hr class="mx-auto" style="max-width: 400px;">
                <a href="alumno_tramites.php" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a mis trámites
                </a>
            </div>
        <?php endif; ?>
    </div>

        <?php elseif ($t_status['estatus'] == 'Pago pendiente'): ?>
    <div class="card p-4 shadow-sm border-success">
        <h5 class="text-success fw-bold"><i class="bi bi-check2-circle"></i> ✅ Solicitud Aceptada</h5>
        <p>Tu documentación ha sido aprobada. Por favor, sube tu <strong>comprobante de pago</strong> para finalizar.</p>
        
        <?php if (!empty($t_status['comentarios'])): ?>
            <div class="alert alert-info small">
                <strong>Nota del revisor:</strong> <?php echo htmlspecialchars($t_status['comentarios']); ?>
            </div>
        <?php endif; ?>

        <form action="../auth/subir_pago.php" method="POST" enctype="multipart/form-data" id="formPago">
            <input type="hidden" name="id_solicitud" value="<?php echo $solicitud_id; ?>">
            
            <div class="mb-3">
                <label class="form-label fw-bold">Voucher de Pago</label>
                <input type="file" name="pago" class="form-control" 
                       accept=".pdf, .jpg, .jpeg, .png" required id="inputPago">
                <div class="form-text">Formatos permitidos: PDF, JPG, PNG (Máx. 5MB).</div>
            </div>
            
            <button type="submit" class="btn btn-success w-100" id="btnEnviarPago">
                <i class="bi bi-cloud-upload"></i> Enviar Comprobante
            </button>
        </form>
    </div>

        <?php elseif ($t_status['estatus'] == 'Validando pago'): ?>
            <div class="alert alert-info p-4 text-center">
                <h4>💳 Pago Recibido</h4>
                <p>Estamos validando tu comprobante de pago. Pronto recibirás el dictamen final.</p>
            </div>

        <?php elseif ($t_status['estatus'] == 'Rechazada'): ?>
            <div class="alert alert-danger p-4 text-center shadow">
                <h4>❌ Solicitud Rechazada</h4>
                <p class="lead">Tu solicitud ha sido revisada y no fue aprobada.</p>
                <?php if (!empty($t_status['comentarios'])): ?>
                    <div class="alert alert-light border-start border-4 border-danger mt-3 text-start">
                        <h6 class="fw-bold">Motivo:</h6>
                        <p class="mb-0"><?php echo htmlspecialchars($t_status['comentarios']); ?></p>
                    </div>
                <?php endif; ?>
                <hr>
                <p class="text-muted small">Si tienes dudas, acude a las oficinas administrativas.</p>
                <a href="alumno_tramites.php" class="btn btn-outline-danger">Regresar a mis trámites</a>
            </div>

            <?php elseif ($t_status['estatus'] == 'Finalizada'): ?>
            <div class="alert alert-success p-5 text-center shadow">
                <h2>✨ ¡Trámite Finalizado!</h2>
                <p>Tu prórroga ha sido aprobada y el proceso se ha completado correctamente.</p>
                <?php if (!empty($t_status['comentarios'])): ?>
                    <div class="mt-3 text-start p-3 border border-success rounded">
                        <h6 class="fw-bold">💬 Mensaje del área administrativa:</h6>
                        <p class="mb-0"><?php echo htmlspecialchars($t_status['comentarios']); ?></p>
                    </div>
                <?php endif; ?>
                <a href="alumno_tramites.php" class="btn btn-outline-success mt-3">Volver al inicio</a>
            </div>
        <?php endif; ?>
    </main>

        <footer class="mt-auto py-3 bg-white border-top">
            <div class="container text-center">
                <div class="dropup">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Recursos Útiles
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="https://www.ilovepdf.com/es" target="_blank">I Love PDF (Comprimir/Unir)</a></li>
                        <li><a class="dropdown-item" href="#">Manual de Usuario</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><span class="dropdown-item-text small text-muted">Versión 1.0</span></li>
                    </ul>
                </div>
            </div>
        </footer>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const seccionTerminos = document.getElementById('seccionTerminos');
            const seccionSubida = document.getElementById('seccionSubida');
            const btnAcepto = document.getElementById('btnAcepto');
            const btnNoAcepto = document.getElementById('btnNoAcepto');
            if (btnAcepto && btnNoAcepto) {
                btnAcepto.addEventListener('click', function() {
                    seccionTerminos.classList.add('d-none');
                    seccionSubida.classList.remove('d-none');
                    alert("Términos y condiciones aceptados. Ya puedes subir tus documentos");
                });

                btnNoAcepto.addEventListener('click', function() {
                    window.location.href = 'alumno_tramites.php';
                });
            }
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function() {
                    const btn = document.getElementById('btnEnviar');
                    if (btn) {
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';
                        btn.disabled = true;
                    }
                });
            }
        });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>