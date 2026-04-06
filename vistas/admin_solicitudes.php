<?php 
require_once '../config/db.php';  
require_once '../includes/auth_check.php';
permitirAcceso(['admin', 'contribuyente']);
include '../includes/header.php';

if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'contribuyente') {
    header("Location: ../login.php");
    exit;
}
$sql = "SELECT s.id_solicitud, a.titulo, al.nombre_completo, al.num_control, 
               s.fecha_envio, s.estatus, al.es_deudor 
        FROM solicitudes s
        JOIN asignaciones a ON s.id_asignacion = a.id_asignacion
        JOIN alumnos al ON s.num_control_alumno = al.num_control
        WHERE a.estatus IN (0, 2)
        ORDER BY s.fecha_envio DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$solicitudes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes Recibidas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/layout.css">
</head>

<body class="bg-light">
    <main class="container mt-4">
        <h3 class="mb-4">Solicitudes Recibidas</h3>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Tipo de Trámite</th>
                            <th>Nombre del Alumno</th>
                            <th>N° Control</th>
                            <th>Fecha</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $s): ?>
                        <tr>
                            <td><?php echo $s['id_solicitud']; ?></td>
                            <td><?php echo htmlspecialchars($s['titulo']); ?></td>
                            <td class="<?php echo ($s['es_deudor'] == 1) ? 'text-danger fw-bold' : ''; ?>">
                                <?php echo htmlspecialchars($s['nombre_completo']); ?>
                                <?php if($s['es_deudor'] == 1): ?> 
                                    <span class="badge bg-danger">⚠️ DEUDOR</span> 
                                <?php endif; ?>
                            </td>
                            <td><?php echo $s['num_control']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($s['fecha_envio'])); ?></td>
                            <td>
                                <span class="badge <?php 
                                    echo ($s['estatus'] == 'Pendiente') ? 'bg-secondary' : 
                                         (($s['estatus'] == 'En revisión') ? 'bg-info' : 
                                         (($s['estatus'] == 'Finalizada') ? 'bg-success' : 'bg-danger')); 
                                ?>">
                                    <?php echo $s['estatus']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="revisar_solicitud.php?id=<?php echo $s['id_solicitud']; ?>" 
                                   class="btn btn-sm btn-primary">Revisar</a>
                                
                                <?php if (!in_array($s['estatus'], ['Finalizada', 'Rechazada'])): ?>
                                <button type="button"
                                    class="btn btn-sm btn-danger btn-deudor"
                                    data-url="../auth/marcar_deudor.php?id=<?php echo $s['id_solicitud']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($s['nombre_completo'], ENT_QUOTES); ?>">
                                    Deudor
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>

    <?php include 'modales/modal_confirmacion.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.btn-deudor').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const nombre = this.getAttribute('data-nombre');
            confirmarAccion(
                '¿Confirmas que deseas marcar a ' + nombre + ' como DEUDOR?',
                function() { window.location.href = url; },
                'danger'
            );
        });
    });
    </script>
</body>
</html>