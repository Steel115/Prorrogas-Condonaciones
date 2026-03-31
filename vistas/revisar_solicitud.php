<?php 
require_once '../config/db.php'; 
require_once '../includes/auth_check.php';
permitirAcceso(['admin', 'contribuyente']);

$id_solicitud = $_GET['id'];

$sql = "SELECT s.*, al.nombre_completo, al.num_control, a.titulo, a.instrucciones 
        FROM solicitudes s
        JOIN alumnos al ON s.num_control_alumno = al.num_control
        JOIN asignaciones a ON s.id_asignacion = a.id_asignacion
        WHERE s.id_solicitud = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_solicitud]);
$solicitud = $stmt->fetch();
// 2. Obtener los archivos subidos por el alumno
$stmtFiles = $pdo->prepare("SELECT * FROM expediente_archivos 
WHERE id_solicitud = ? AND tipo_archivo != 'pago'");
$stmtFiles->execute([$id_solicitud]);
$archivos = $stmtFiles->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Revisando Solicitud #<?php echo $id_solicitud; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <a href="admin_solicitudes.php" class="btn btn-sm btn-outline-secondary mb-3">← Volver</a>
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Expediente de: <?php echo htmlspecialchars($solicitud['nombre_completo']); ?></h5>
                        <small class="text-muted">Control: <?php echo $solicitud['num_control']; ?> | Trámite: <?php echo $solicitud['titulo']; ?></small>
                    </div>
                    <div class="card-body">
                        <h6>Documentos Entregados:</h6>
                        <div class="list-group">
                            <?php foreach ($archivos as $archivo): ?>
                                <a href="<?php echo $archivo['ruta_fisica']; ?>" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span>📄 <?php echo $archivo['nombre_original']; ?></span>
                                    <span class="btn btn-sm btn-outline-primary">Ver PDF</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
        <?php 
        // Obtener todos los comprobantes de pago
        $stmtPago = $pdo->prepare("SELECT * FROM expediente_archivos WHERE id_solicitud = ? AND tipo_archivo = 'pago'");
        $stmtPago->execute([$id_solicitud]);
        $pagos = $stmtPago->fetchAll();
        $totalPagos = count($pagos);
        ?>
        <?php if ($totalPagos > 0): ?>
        <div class="card mt-4 shadow-sm <?php echo ($solicitud['estatus'] == 'Finalizada') ? 'border-secondary' : 'border-success'; ?>">
            <div class="card-header <?php echo ($solicitud['estatus'] == 'Finalizada') ? 'bg-secondary' : 'bg-success'; ?> text-white">
                <h5 class="mb-0">
                    <?php echo ($solicitud['estatus'] == 'Finalizada') ? '🏁 Trámite Finalizado' : 'Comprobante de Pago Recibido'; ?>
                </h5>
            </div>
            <div class="card-body text-center">
                <?php if ($solicitud['estatus'] == 'Finalizada'): ?>
                    <p class="text-muted">Este trámite ha sido completado.</p>
                    <?php if ($totalPagos === 1): ?>
                        <!-- Un solo comprobante — botón directo -->
                        <a href="<?php echo $pagos[0]['ruta_fisica']; ?>" target="_blank" class="btn btn-outline-secondary mb-3">
                            🔍 Ver Comprobante de Pago
                        </a>
                    <?php else: ?>
                        <!-- Varios comprobantes — lista -->
                        <div class="list-group mb-3 text-start">
                            <?php foreach ($pagos as $pago): ?>
                                <a href="<?php echo $pago['ruta_fisica']; ?>" target="_blank"
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span>🧾 <?php echo htmlspecialchars($pago['nombre_original']); ?></span>
                                    <span class="btn btn-sm btn-outline-secondary">Ver</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <span class="badge bg-secondary fs-6 px-4 py-2">✔️ Proceso concluido</span>

                <?php else: ?>
                    <p>El alumno ha subido su<?php echo $totalPagos > 1 ? 's vouchers:' : ' voucher:'; ?></p>
                    <?php if ($totalPagos === 1): ?>
                        <!-- Un solo comprobante — botón directo -->
                        <a href="<?php echo $pagos[0]['ruta_fisica']; ?>" target="_blank" class="btn btn-outline-success mb-3">
                            🔍 Abrir Comprobante de Pago
                        </a>
                    <?php else: ?>
                        <!-- Varios comprobantes — lista -->
                        <div class="list-group mb-3 text-start">
                            <?php foreach ($pagos as $pago): ?>
                                <a href="<?php echo $pago['ruta_fisica']; ?>" target="_blank"
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span>🧾 <?php echo htmlspecialchars($pago['nombre_original']); ?></span>
                                    <span class="btn btn-sm btn-outline-success">Ver</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <hr>
                    <form action="../auth/finalizar_tramite.php" method="POST">
                        <input type="hidden" name="id_solicitud" value="<?php echo $id_solicitud; ?>">
                        <button type="submit" class="btn btn-success btn-lg w-100"
                            onclick="return confirm('¿Confirmas que deseas FINALIZAR este trámite?')">
                            ✅ Validar y Finalizar Trámite
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

            <div class="col-md-4">
                <!-- Caja de mensajes -->
                <div class="card shadow-sm mb-4 border-info">
                    <div class="card-body">
                        <h5 class="card-title text-info">💬 Observaciones</h5>
                        <p class="small text-muted">Escribe un mensaje para el alumno si hay alguna observación.</p>
                        <form action="../auth/guardar_comentario.php" method="POST" id="formComentario">
                            <input type="hidden" name="id_solicitud" value="<?php echo $id_solicitud; ?>">
                            <div class="mb-3">
                                <textarea name="comentario" id="textoComentario" class="form-control" rows="4" 
                                placeholder="<?php echo !empty($solicitud['comentarios']) 
                                    ? 'Último mensaje: ' . htmlspecialchars($solicitud['comentarios'], ENT_QUOTES) 
                                    : 'Ej: El archivo no es legible...'; ?>"></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" id="btnEnviarComentario" class="btn btn-info text-white">
                                    Enviar Observación
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Dictamen final-->
                <div class="card shadow-sm border-dark">
                    <div class="card-body">
                        <h5 class="card-title">Dictamen Final</h5>
                        <p class="small text-muted">Esto cambiará el estatus y notificará al alumno.</p>
                        <?php 
                        // ✅ Bloquear si ya fue aceptada, rechazada o finalizada
                        $estatusActual = $solicitud['estatus'];
                        $bloqueado = in_array($estatusActual, ['Pago pendiente', 'Rechazada', 'Validando pago', 'Finalizada']);
                        ?>
                        <form action="../auth/procesar_dictamen.php" method="POST">
                            <input type="hidden" name="id_solicitud" value="<?php echo $id_solicitud; ?>">
                            <div class="d-grid gap-2">
                                <button type="submit" name="accion" value="aceptar" 
                                    class="btn <?php echo $bloqueado ? 'btn-secondary' : 'btn-success'; ?>"
                                    <?php echo $bloqueado ? 'disabled' : ''; ?>
                                    <?php echo !$bloqueado ? 'onclick="return confirm(\'¿Confirmas que deseas ACEPTAR esta solicitud?\')"' : ''; ?>>
                                    ✅ Aceptar
                                </button>
                                <button type="submit" name="accion" value="rechazar" 
                                    class="btn <?php echo $bloqueado ? 'btn-secondary' : 'btn-danger'; ?>"
                                    <?php echo $bloqueado ? 'disabled' : ''; ?>
                                    <?php echo !$bloqueado ? 'onclick="return confirm(\'¿Confirmas que deseas RECHAZAR esta solicitud?\')"' : ''; ?>>
                                    ❌ Rechazar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('formComentario').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnEnviarComentario');
    const area = document.getElementById('textoComentario');
    const formData = new FormData(this);
    btn.disabled = true;
    btn.innerHTML = 'Enviando...';
    fetch('../auth/guardar_comentario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        area.value = '';
        btn.innerHTML = '✅ ¡Enviado!';
        btn.classList.replace('btn-info', 'btn-success');
        setTimeout(() => {
            btn.innerHTML = 'Enviar Observación';
            btn.classList.replace('btn-success', 'btn-info');
            btn.disabled = false;
        }, 2000);
    })
    .catch(error => {
        btn.innerHTML = '❌ Error al enviar';
        btn.classList.replace('btn-info', 'btn-danger');
        btn.disabled = false;
    });
});
</script>                  
</body>
</html>