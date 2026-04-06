<?php 
require_once '../config/db.php'; 
require_once '../includes/auth_check.php';
permitirAcceso(['admin', 'contribuyente']);
include '../includes/header.php';

$num_control = $_GET['num_control'];

$stmtAlu = $pdo->prepare("SELECT 
                            a.nombre_completo, 
                            a.num_control, 
                            a.es_deudor, 
                            e.carrera, 
                            e.semestre 
                          FROM alumnos a
                          INNER JOIN bd_estudiantes e ON a.num_control = e.num_control
                          WHERE a.num_control = ?");
$stmtAlu->execute([$num_control]);
$alumno = $stmtAlu->fetch();

// Si no se encuentra el alumno (por seguridad)
if (!$alumno) {
    die("Error: El alumno no se encuentra en los registros académicos.");
}

// El resto de la consulta de solicitudes se queda igual
$sql = "SELECT s.*, a.titulo, a.ciclo_escolar, u.nombre_completo as revisor 
        FROM solicitudes s
        JOIN asignaciones a ON s.id_asignacion = a.id_asignacion
        LEFT JOIN usuarios u ON s.id_revisor_actual = u.num_trabajador
        WHERE s.num_control_alumno = ?
        ORDER BY s.fecha_envio DESC";
$stmtSol = $pdo->prepare($sql);
$stmtSol->execute([$num_control]);
$solicitudes = $stmtSol->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/layout.css">
    <title>Expediente - <?php echo $num_control; ?></title>
</head>
<body>

<main class="container">
    <div class="d-block mb-2 mt-3">
        <a href="admin_historial.php" class="btn btn-sm btn-outline-secondary mb-3">
            ← Volver
        </a>
    </div>
    <div class="card shadow-sm mb-4 card-alumno">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-1 text-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        <?php echo substr($alumno['nombre_completo'], 0, 1); ?>
                    </div>
                </div>
                <div class="col-md-11">
                    <h4 class="mb-1 text-uppercase"><?php echo htmlspecialchars($alumno['nombre_completo']); ?></h4>
                    <p class="text-muted mb-0">No. Control: <strong><?php echo $alumno['num_control']; ?></strong> | Carrera: <?php echo $alumno['carrera']; ?></p>
                    <span class="badge <?php echo $alumno['es_deudor'] ? 'bg-danger' : 'bg-success'; ?>">
                        <?php echo $alumno['es_deudor'] ? 'Estatus: DEUDOR' : 'Alumno Regular'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-3">Historial de Solicitudes</h5>
    <div class="accordion shadow-sm" id="historialAlumno">
        
        <?php foreach ($solicitudes as $index => $sol): 
            $id_acordeon = "solicitud" . $sol['id_solicitud'];
            $stmtFiles = $pdo->prepare("SELECT * FROM expediente_archivos WHERE id_solicitud = ?");
            $stmtFiles->execute([$sol['id_solicitud']]);
            $archivos = $stmtFiles->fetchAll();
        ?>
        
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $id_acordeon; ?>">
                    <div class="d-flex justify-content-between w-100 me-3">
                        <span><?php echo htmlspecialchars($sol['titulo']); ?> (<?php echo $sol['ciclo_escolar']; ?>)</span>
                        <span class="badge <?php echo ($sol['estatus'] == 'Finalizada') ? 'bg-success' : 'badge bg-danger'; ?>">
                            <?php echo $sol['estatus']; ?>
                        </span>
                    </div>
                </button>
            </h2>
            <div id="<?php echo $id_acordeon; ?>" class="accordion-collapse collapse" data-bs-parent="#historialAlumno">
                <div class="accordion-body">
                    <p><strong>Fecha de envío:</strong> <?php echo date('d/m/Y H:i', strtotime($sol['fecha_envio'])); ?></p>
                    <p><strong>Documentos en expediente:</strong></p>
                    <div class="list-group list-group-flush mb-3">
                        <?php foreach ($archivos as $file): ?>
                            <a href="<?php echo $file['ruta_fisica']; ?>" target="_blank" class="list-group-item list-group-item-action small py-1 text-primary">
                                📄 <?php echo htmlspecialchars($file['nombre_original']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <hr>
                    <small class="text-muted">Última revisión por: <?php echo $sol['revisor'] ? htmlspecialchars($sol['revisor']) : 'Sin asignar'; ?></small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if(empty($solicitudes)): ?>
            <div class="alert alert-light text-center">Este alumno no ha iniciado ningún trámite.</div>
        <?php endif; ?>

    </div>
</main>
<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>