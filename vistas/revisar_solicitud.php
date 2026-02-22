<?php 
require_once '../config/db.php'; 
include '../includes/auth_check.php'; 
$id_solicitud = $_GET['id'];
// 1. Obtener datos de la solicitud, alumno y trámite
$sql = "SELECT s.*, al.nombre_completo, al.num_control, a.titulo, a.instrucciones 
        FROM solicitudes s
        JOIN alumnos al ON s.num_control_alumno = al.num_control
        JOIN asignaciones a ON s.id_asignacion = a.id_asignacion
        WHERE s.id_solicitud = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_solicitud]);
$solicitud = $stmt->fetch();
// 2. Obtener los archivos subidos por el alumno
$stmtFiles = $pdo->prepare("SELECT * FROM expediente_archivos WHERE id_solicitud = ?");
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
// Buscamos si existe un archivo tipo 'pago'
$stmtPago = $pdo->prepare("SELECT * FROM expediente_archivos WHERE id_solicitud = ? AND tipo_archivo = 'pago'");
$stmtPago->execute([$id_solicitud]);
$pago = $stmtPago->fetch();
?>

<?php if ($pago): ?>
<div class="card border-success mt-4 shadow-sm">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">Comprobante de Pago Recibido</h5>
    </div>
    <div class="card-body text-center">
        <p>El alumno ha subido su voucher:</p>
        <a href="<?php echo $pago['ruta_fisica']; ?>" target="_blank" class="btn btn-outline-success mb-3">
            🔍 Abrir Comprobante de Pago
        </a>
        <hr>
        <form action="../auth/finalizar_tramite.php" method="POST">
            <input type="hidden" name="id_solicitud" value="<?php echo $id_solicitud; ?>">
            <button type="submit" class="btn btn-success btn-lg w-100">
                ✅ Validar y Finalizar Trámite
            </button>
        </form>
    </div>
</div>
<?php endif; ?>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Dictamen</h5>
                        <form action="../auth/procesar_dictamen.php" method="POST">
                            <input type="hidden" name="id_solicitud" value="<?php echo $id_solicitud; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Comentarios / Observaciones</label>
                                <textarea name="comentarios" class="form-control" rows="4" placeholder="Escribe aquí por qué se acepta o rechaza..."></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="accion" value="aceptar" class="btn btn-success">
                                    Aceptar (Habilitar Pago)
                                </button>
                                
                                <button type="submit" name="accion" value="rechazar" class="btn btn-danger">
                                    Rechazar Solicitud
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>