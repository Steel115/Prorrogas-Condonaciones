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
                <div class="card p-4 mb-4 border-warning">
                    
                    <h5>Términos y Condiciones</h5>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($t['terminos'])); ?></p>
                    <div class="d-flex gap-3">
                        <button type="button" id="btnAcepto" class="btn btn-outline-success">Sí, acepto los términos</button>
                        <button type="button" id="btnNoAcepto" class="btn btn-outline-danger">No acepto</button>
                    </div>
                </div>

                <div class="card p-4 shadow-sm">
                    <h5>Subir Documentación</h5>
                    <p>Solo archivos PDF (Máx. 5MB)</p>
                    <form action="../auth/subir_archivos.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_asignacion" value="<?php echo $id_asignacion; ?>">
                        <input type="file" name="documentos[]" class="form-control mb-3" multiple id="inputArchivos" disabled>
                        <button type="submit" id="btnEnviar" class="btn btn-primary w-100" disabled>Enviar Solicitud</button>
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
                <div class="alert alert-warning p-4 text-center">
                    <h4><i class="bi bi-clock-history"></i> Solicitud en Proceso</h4>
                    <p>Tus documentos han sido recibidos y están siendo revisados por el personal administrativo.</p>
                    <span class="badge bg-dark text-white p-2">Estatus actual: <?php echo $t_status['estatus']; ?></span>
                </div>

            <?php elseif ($t_status['estatus'] == 'Pago pendiente'): ?>
                <div class="card p-4 shadow-sm border-success">
                    <h5 class="text-success">✅ Solicitud Aceptada</h5>
                    <p>Tu documentación ha sido aprobada. Por favor, sube tu <strong>comprobante de pago</strong>.</p>
                    <?php if (!empty($t_status['comentarios'])): ?>
                        <div class="alert alert-info small">
                            <strong>Nota del revisor:</strong> <?php echo htmlspecialchars($t_status['comentarios']); ?>
                        </div>
                    <?php endif; ?>
                    <form action="../auth/subir_pago.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_solicitud" value="<?php echo $solicitud_id; ?>">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Voucher de Pago (PDF/Imagen)</label>
                            <input type="file" name="pago" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Enviar Comprobante</button>
                    </form>
                </div>

            <?php elseif ($t_status['estatus'] == 'Validando pago'): ?>
                <div class="alert alert-info p-4 text-center">
                    <h4>💳 Pago Recibido</h4>
                    <p>Estamos validando tu comprobante de pago. Pronto recibirás el dictamen final.</p>
                </div>

            <?php elseif ($t_status['estatus'] == 'Finalizada'): ?>
                <div class="alert alert-success p-5 text-center shadow">
                    <h2>✨ ¡Trámite Finalizado!</h2>
                    <p>Tu prórroga ha sido aprobada y el proceso se ha completado correctamente.</p>
                    <a href="alumno_tramites.php" class="btn btn-outline-success">Volver al inicio</a>
                </div>
            <?php endif; ?>
        </main>

        <footer class="mt-auto py-3 bg-white border-top fixed-bottom">
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
    const btnAcepto = document.getElementById('btnAcepto');
    const btnNoAcepto = document.getElementById('btnNoAcepto');
    const inputArchivos = document.getElementById('inputArchivos');
    const btnEnviar = document.getElementById('btnEnviar');
    btnAcepto.addEventListener('click', function() {
        btnAcepto.classList.replace('btn-outline-success', 'btn-success');
        btnNoAcepto.classList.replace('btn-danger', 'btn-outline-danger');
        inputArchivos.disabled = false;
        btnEnviar.disabled = false;
        
        alert("¡Términos aceptados! Ahora puedes seleccionar tus archivos.");
    });
    btnNoAcepto.addEventListener('click', function() {
        btnAcepto.classList.replace('btn-success', 'btn-outline-success');
        btnNoAcepto.classList.replace('btn-outline-danger', 'btn-danger');
        inputArchivos.disabled = true;
        btnEnviar.disabled = true;
        inputArchivos.value = "";
    });
    btnEnviar.addEventListener('click', function() {
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';
});
});
</script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>