<?php 
require_once '../config/db.php'; 
require_once '../includes/auth_check.php';
include '../includes/header.php'; 

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

    // Bloquear acceso directo a asignaciones vencidas o archivadas
    if ($t['estatus'] == 1 || $t['estatus'] == 2) {
        // Solo permitir si ya tiene una solicitud activa en proceso
        $stmtCheck = $pdo->prepare("SELECT estatus FROM solicitudes WHERE id_asignacion = ? AND num_control_alumno = ?");
        $stmtCheck->execute([$id_asignacion, $num_control]);
        $solicitudActiva = $stmtCheck->fetch();

        // Si no tiene solicitud o ya está finalizada/rechazada, redirigir
        if (!$solicitudActiva || in_array($solicitudActiva['estatus'], ['Finalizada', 'Rechazada', 'Deudor'])) {
            header("Location: alumno_tramites.php?error=Esta asignación ya no está disponible.");
            exit;
        }
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

<main class="container mt-5">
    <h2 class="mb-4"><?php echo htmlspecialchars($t['titulo']); ?></h2>

    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ⚠️ <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ <?php echo htmlspecialchars($_GET['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!$t_status): ?>
    <!-- ESTADO: Sin solicitud — Aceptar términos y subir -->
        <div class="card p-4 mb-4 shadow-sm">
            <h5 class="text-info">Instrucciones del Proceso</h5>
            <div class="p-3 bg-white border rounded">
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($t['instrucciones'])); ?></p>
            </div>
        </div>

        <div id="seccionTerminos" class="alert alert-warning p-4 mb-4 shadow-sm">
            <h5 class="alert-heading">⚠️ Términos y Condiciones</h5>
            <p><?php echo nl2br(htmlspecialchars($t['terminos'])); ?></p>
            <hr>
            <div class="d-flex gap-3">
                <button type="button" id="btnAcepto" class="btn btn-success">Sí, acepto los términos</button>
                <button type="button" id="btnNoAcepto" class="btn btn-danger">No acepto</button>
            </div>
        </div>

        <div id="seccionSubida" class="card p-4 shadow-sm d-none">
            <h5>📁 Subir Documentación</h5>
            <p class="text-muted small">Solo archivos PDF, JPG, PNG (Máx. 5MB)</p>
            <form action="../auth/subir_archivos.php" method="POST" enctype="multipart/form-data" id="formSolicitud">
                <input type="hidden" name="id_asignacion" value="<?php echo $id_asignacion; ?>">

                <div class="d-flex gap-2 mb-2">
                    <input type="file" id="inputSolicitud" class="form-control"
                           accept=".pdf,.jpg,.jpeg,.png" multiple>
                    <button type="button" class="btn btn-outline-primary text-nowrap" id="btnAgregarSolicitud">
                        ➕ Agregar
                    </button>
                </div>
                <div class="form-text mb-3">Selecciona uno o varios archivos y presiona Agregar.</div>

                <ul class="list-group mb-3 d-none" id="listaSolicitud"></ul>
                <input type="file" name="documentos[]" id="inputSolicitudReal" multiple class="d-none">

                <div class="progress mb-3 d-none" id="barraProgresoSolicitud">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width:100%">
                        Enviando solicitud...
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 d-none" id="btnEnviarSolicitud">
                    📤 Enviar Solicitud
                </button>
            </form>
        </div>

    <?php elseif ($t_status['estatus'] == 'Deudor'): ?>
    <!-- Deudor -->
        <div class="alert alert-danger p-5 text-center shadow">
            <h2>⚠️ Estatus: DEUDOR</h2>
            <p class="lead">Se ha detectado una irregularidad en tu proceso.</p>
            <p>Por favor, acude a las oficinas de Recursos Financieros para aclarar tu situación.</p>
            <hr>
            <a href="alumno_tramites.php" class="btn btn-danger">Regresar a mis trámites</a>
        </div>

    <?php elseif ($t_status['estatus'] == 'Pendiente' || $t_status['estatus'] == 'En revisión'): ?>
    <!-- Pendiente / En revisión -->
        <div class="alert alert-light border p-4 shadow-sm">
            <h4 class="text-center mb-4">🕐 Estado del Trámite</h4>

            <?php if (!empty($t_status['comentarios'])): ?>
                <div class="alert alert-danger border-start border-4 mb-4">
                    <h6 class="fw-bold">⚠️ Observación para corregir:</h6>
                    <p class="mb-0"><?php echo htmlspecialchars($t_status['comentarios']); ?></p>
                </div>

                <div class="card shadow-sm p-3 mb-3">
                    <h5 class="mb-3">📁 Tus archivos actuales</h5>
                    <ul class="list-group mb-4" id="listaArchivosActuales">
                        <?php
                        $stmtActuales = $pdo->prepare("SELECT * FROM expediente_archivos WHERE id_solicitud = ? AND tipo_archivo != 'pago'");
                        $stmtActuales->execute([$solicitud_id]);
                        $archivosActuales = $stmtActuales->fetchAll();
                        foreach ($archivosActuales as $f): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center"
                                id="archivo-<?php echo $f['id_archivo']; ?>">
                                <span>📄 <?php echo htmlspecialchars($f['nombre_original']); ?></span>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="eliminarArchivo(<?php echo $f['id_archivo']; ?>, this)">✕</button>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <hr>

                    <h5 class="mb-2">➕ Agregar archivo(s) nuevo(s)</h5>
                    <p class="text-muted small">Solo archivos PDF, JPG, PNG (Máx. 5MB)</p>

                    <form action="../auth/subir_archivos.php" method="POST" enctype="multipart/form-data" id="formNuevosArchivos">
                        <input type="hidden" name="id_asignacion" value="<?php echo $id_asignacion; ?>">
                        <input type="hidden" name="id_solicitud_existente" value="<?php echo $solicitud_id; ?>">

                        <div class="d-flex gap-2 mb-2">
                            <input type="file" id="inputNuevosArchivos" class="form-control"
                                   accept=".pdf,.jpg,.jpeg,.png" multiple>
                            <button type="button" class="btn btn-outline-primary text-nowrap" id="btnAgregarNuevos">
                                ➕ Agregar
                            </button>
                        </div>
                        <div class="form-text mb-3">Selecciona uno o varios archivos y presiona Agregar.</div>

                        <ul class="list-group mb-3 d-none" id="listaNuevosArchivos"></ul>
                        <input type="file" name="documentos[]" id="inputNuevosReal" multiple class="d-none">

                        <div class="progress mb-3 d-none" id="barraProgresoNuevos">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width:100%">
                                Enviando archivos...
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 d-none" id="btnEnviarNuevos">
                            📤 Enviar Archivos
                        </button>
                    </form>
                </div>

            <?php else: ?>
                <div class="text-center py-4">
                    <span class="badge rounded-pill bg-primary px-4 py-2 fs-6">🔍 Documentación en Revisión</span>
                    <p class="lead mt-3 mb-1">¡Hemos recibido tus documentos!</p>
                    <p class="text-muted">El personal administrativo está validando tu información.</p>
                    <hr class="mx-auto" style="max-width: 400px;">
                    <a href="alumno_tramites.php" class="btn btn-sm btn-outline-secondary">← Volver a mis trámites</a>
                </div>
            <?php endif; ?>
        </div>

    <?php elseif ($t_status['estatus'] == 'Pago pendiente'): ?>
    <!-- Pago pendiente -->
        <div class="card p-4 shadow-sm border-success">
            <h5 class="text-success fw-bold">✅ Solicitud Aceptada</h5>
            <p>Tu documentación ha sido aprobada. Por favor, sube tu <strong>comprobante de pago</strong> para finalizar.</p>

            <?php if (!empty($t_status['comentarios'])): ?>
                <div class="p-3 border border-success rounded mb-3" style="background-color:rgba(255,255,255,0.3);">
                    <h6 class="fw-bold">💬 Mensaje del área administrativa:</h6>
                    <p class="mb-0"><?php echo htmlspecialchars($t_status['comentarios']); ?></p>
                </div>
            <?php endif; ?>

            <form action="../auth/subir_pago.php" method="POST" enctype="multipart/form-data" id="formPago">
                <input type="hidden" name="id_solicitud" value="<?php echo $solicitud_id; ?>">

                <label class="form-label fw-bold">Voucher de Pago</label>
                <div class="d-flex gap-2 mb-2">
                    <input type="file" id="inputPago" class="form-control"
                           accept=".pdf,.jpg,.jpeg,.png" multiple>
                    <button type="button" class="btn btn-outline-success text-nowrap" id="btnAgregarPago">
                        ➕ Agregar
                    </button>
                </div>
                <div class="form-text mb-3">Formatos permitidos: PDF, JPG, PNG (Máx. 5MB).</div>

                <ul class="list-group mb-3 d-none" id="listaPagos"></ul>
                <input type="file" name="pagos[]" id="inputPagoReal" multiple class="d-none">

                <div class="progress mb-3 d-none" id="barraProgresoPago">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width:100%">
                        Enviando comprobante...
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 d-none" id="btnEnviarPago">
                    📤 Enviar Comprobante
                </button>
            </form>
        </div>

    <?php elseif ($t_status['estatus'] == 'Validando pago'): ?>
    <!-- Validando pago -->
        <?php if (!empty($t_status['comentarios'])): ?>
            <!-- Con comentario: mostrar comprobantes actuales + opción de quitar/subir nuevos -->
            <div class="card p-4 shadow-sm border-warning">
                <h5 class="text-warning fw-bold">💳 Comprobante en Revisión</h5>

                <div class="alert alert-warning border-start border-4 mb-4">
                    <h6 class="fw-bold">⚠️ Observación sobre tu comprobante:</h6>
                    <p class="mb-0"><?php echo htmlspecialchars($t_status['comentarios']); ?></p>
                </div>

                <div class="card shadow-sm p-3 mb-3">
                    <h5 class="mb-3">📁 Tus comprobantes actuales</h5>
                    <ul class="list-group mb-4" id="listaComprobantesActuales">
                        <?php
                        $stmtPagos = $pdo->prepare("SELECT * FROM expediente_archivos WHERE id_solicitud = ? AND tipo_archivo = 'pago'");
                        $stmtPagos->execute([$solicitud_id]);
                        $pagosActuales = $stmtPagos->fetchAll();
                        foreach ($pagosActuales as $p): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center"
                                id="archivo-<?php echo $p['id_archivo']; ?>">
                                <span>🧾 <?php echo htmlspecialchars($p['nombre_original']); ?></span>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="eliminarArchivo(<?php echo $p['id_archivo']; ?>, this)">✕</button>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <hr>

                    <h5 class="mb-2">➕ Agregar comprobante(s) nuevo(s)</h5>
                    <p class="text-muted small">Formatos permitidos: PDF, JPG, PNG (Máx. 5MB)</p>

                    <form action="../auth/subir_pago.php" method="POST" enctype="multipart/form-data" id="formNuevoPago">
                        <input type="hidden" name="id_solicitud" value="<?php echo $solicitud_id; ?>">

                        <div class="d-flex gap-2 mb-2">
                            <input type="file" id="inputNuevoPago" class="form-control"
                                   accept=".pdf,.jpg,.jpeg,.png" multiple>
                            <button type="button" class="btn btn-outline-warning text-nowrap" id="btnAgregarNuevoPago">
                                ➕ Agregar
                            </button>
                        </div>
                        <div class="form-text mb-3">Selecciona uno o varios archivos y presiona Agregar.</div>

                        <ul class="list-group mb-3 d-none" id="listaNuevoPago"></ul>
                        <input type="file" name="pagos[]" id="inputNuevoPagoReal" multiple class="d-none">

                        <div class="progress mb-3 d-none" id="barraProgresoNuevoPago">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" style="width:100%">
                                Enviando comprobante...
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 d-none" id="btnEnviarNuevoPago">
                            📤 Enviar Comprobante
                        </button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <!-- Sin comentario: espera normal -->
            <div class="alert alert-info p-4 text-center">
                <h4>💳 Pago Recibido</h4>
                <p>Estamos validando tu comprobante de pago. Pronto recibirás el dictamen final.</p>
            </div>
        <?php endif; ?>

    <?php elseif ($t_status['estatus'] == 'Rechazada'): ?>
    <!-- ESTADO: Rechazada -->
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
    <!-- Finalizada -->
        <div class="alert alert-success p-5 text-center shadow">
            <h2>✨ ¡Trámite Finalizado!</h2>
            <p>Tu prórroga ha sido aprobada y el proceso se ha completado correctamente.</p>
            <?php if (!empty($t_status['comentarios'])): ?>
                <div class="mt-3 text-start p-3 border border-success rounded" style="background-color:rgba(255,255,255,0.3);">
                    <h6 class="fw-bold">💬 Mensaje del área administrativa:</h6>
                    <p class="mb-0"><?php echo htmlspecialchars($t_status['comentarios']); ?></p>
                </div>
            <?php endif; ?>
            <a href="alumno_tramites.php" class="btn btn-outline-success mt-3">Volver al inicio</a>
        </div>
    <?php endif; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function crearAcumulador(cfg) {
    const inputVisible = document.getElementById(cfg.inputVisibleId);
    const inputReal    = document.getElementById(cfg.inputRealId);
    const btnAgregar   = document.getElementById(cfg.btnAgregarId);
    const lista        = document.getElementById(cfg.listaId);
    const btnEnviar    = document.getElementById(cfg.btnEnviarId);
    const barra        = document.getElementById(cfg.barraId);
    const form         = document.getElementById(cfg.formId);

    if (!btnAgregar || !form) return null;

    let dt = new DataTransfer();

    btnAgregar.addEventListener('click', function() {
        if (!inputVisible.files.length) {
            alert('Selecciona al menos un archivo primero.');
            return;
        }

        Array.from(inputVisible.files).forEach(file => {
            const existe = Array.from(dt.files).some(f => f.name === file.name);
            if (existe) return;
            dt.items.add(file);
            const safeId = cfg.listaId + '_' + file.name.replace(/[^a-z0-9]/gi, '_');
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center small';
            li.id = safeId;
            li.innerHTML = `
                <span>📄 ${file.name} <span class="text-muted">(${(file.size/1024).toFixed(1)} KB)</span></span>
                <button type="button" class="btn btn-sm btn-outline-danger"
                    onclick="quitarArchivo('${safeId}', '${file.name.replace(/'/g,"\\'")}', '${cfg.listaId}', '${cfg.inputRealId}', '${cfg.btnEnviarId}', '${cfg.formId}')">✕</button>
            `;
            lista.appendChild(li);
        });
        const nuevodt = new DataTransfer();
        Array.from(dt.files).forEach(f => nuevodt.items.add(f));
        inputReal.files = nuevodt.files;

        lista.classList.remove('d-none');
        btnEnviar.classList.remove('d-none');
        inputVisible.value = '';
    });

    form.addEventListener('submit', function(e) {
        if (dt.files.length === 0) {
            e.preventDefault();
            alert('Agrega al menos un archivo antes de enviar.');
            return;
        }
        const nuevodt = new DataTransfer();
        Array.from(dt.files).forEach(f => nuevodt.items.add(f));
        inputReal.files = nuevodt.files;

        btnEnviar.disabled = true;
        btnEnviar.innerHTML = '⏳ Enviando...';
        if (barra) barra.classList.remove('d-none');
    });

    form.dataset.acumuladorId = cfg.formId;
    window['dt_' + cfg.formId] = dt;
    return dt;
}

function quitarArchivo(safeId, nombre, listaId, inputRealId, btnEnviarId, formId) {
    const dt = window['dt_' + formId];
    if (!dt) return;
    const nuevo = new DataTransfer();
    Array.from(dt.files).forEach(f => {
        if (f.name !== nombre) nuevo.items.add(f);
    });
    while (dt.items.length > 0) dt.items.remove(0);
    Array.from(nuevo.files).forEach(f => dt.items.add(f));
    const inputReal = document.getElementById(inputRealId);
    const sync = new DataTransfer();
    Array.from(dt.files).forEach(f => sync.items.add(f));
    inputReal.files = sync.files;
    const li = document.getElementById(safeId);
    if (li) li.remove();

    if (dt.files.length === 0) {
        document.getElementById(listaId)?.classList.add('d-none');
        document.getElementById(btnEnviarId)?.classList.add('d-none');
    }
}

// Inicializar los 3 acumuladores
crearAcumulador({
    formId: 'formSolicitud',
    inputVisibleId: 'inputSolicitud',
    inputRealId: 'inputSolicitudReal',
    btnAgregarId: 'btnAgregarSolicitud',
    listaId: 'listaSolicitud',
    btnEnviarId: 'btnEnviarSolicitud',
    barraId: 'barraProgresoSolicitud'
});

crearAcumulador({
    formId: 'formNuevosArchivos',
    inputVisibleId: 'inputNuevosArchivos',
    inputRealId: 'inputNuevosReal',
    btnAgregarId: 'btnAgregarNuevos',
    listaId: 'listaNuevosArchivos',
    btnEnviarId: 'btnEnviarNuevos',
    barraId: 'barraProgresoNuevos'
});

crearAcumulador({
    formId: 'formPago',
    inputVisibleId: 'inputPago',
    inputRealId: 'inputPagoReal',
    btnAgregarId: 'btnAgregarPago',
    listaId: 'listaPagos',
    btnEnviarId: 'btnEnviarPago',
    barraId: 'barraProgresoPago'
});

// Acumulador para nuevo comprobante en Validando pago
crearAcumulador({
    formId: 'formNuevoPago',
    inputVisibleId: 'inputNuevoPago',
    inputRealId: 'inputNuevoPagoReal',
    btnAgregarId: 'btnAgregarNuevoPago',
    listaId: 'listaNuevoPago',
    btnEnviarId: 'btnEnviarNuevoPago',
    barraId: 'barraProgresoNuevoPago'
});

document.getElementById('btnAcepto')?.addEventListener('click', function() {
    confirmarAccion(
        '¿Confirmas que has leído y aceptas los términos y condiciones?',
        function() {
            document.getElementById('seccionTerminos').classList.add('d-none');
            document.getElementById('seccionSubida').classList.remove('d-none');
        },
        'success'
    );
});

document.getElementById('btnNoAcepto')?.addEventListener('click', function() {
    window.location.href = 'alumno_tramites.php';
});

function eliminarArchivo(idArchivo, boton) {
    confirmarAccion(
        '¿Deseas quitar este archivo de tu expediente?',
        function() {
            boton.disabled = true;
            boton.innerHTML = '...';
            fetch('../auth/eliminar_archivo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id_archivo=' + idArchivo
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const item = document.getElementById('archivo-' + idArchivo);
                    item.style.transition = 'opacity 0.3s';
                    item.style.opacity = '0';
                    setTimeout(() => item.remove(), 300);
                } else {
                    boton.disabled = false;
                    boton.innerHTML = '✕';
                    alert('Error al eliminar: ' + data.message);
                }
            })
            .catch(() => {
                boton.disabled = false;
                boton.innerHTML = '✕';
                alert('Error de conexión');
            });
        },
        'danger'
    );
}
</script>

<?php include '../vistas/modales/modal_confirmacion.php'; ?>
<?php include '../includes/footer.php'; ?>
</body>
</html>