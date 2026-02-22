<?php 
require_once '../config/db.php'; 
include '../includes/auth_check.php'; 

if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'contribuyente') {
    header("Location: ../login.php");
    exit;
}
$sql = "SELECT s.id_solicitud, a.titulo, al.nombre_completo, al.num_control, 
               s.fecha_envio, s.estatus, al.es_deudor 
        FROM solicitudes s
        JOIN asignaciones a ON s.id_asignacion = a.id_asignacion
        JOIN alumnos al ON s.num_control_alumno = al.num_control
        WHERE a.estatus = 0 
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
</head>
<header class="p-3 bg-white border-bottom shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <span><strong>ADMINISTRADOR:</strong> <?php echo $_SESSION['nombre']; ?></span>
            <a href="../auth/logout.php" class="btn btn-sm btn-outline-danger ms-2">Cerrar Sesión</a>
        </div>
</header>
<body class="bg-light">
    <div class="container mt-5">
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
                                
                                <!--<button class="btn btn-sm btn-success">Finalizar</button>-->
                                
                                <a href="../auth/marcar_deudor.php?id=<?php echo $s['id_solicitud']; ?>" 
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('¿Confirmas que deseas marcar a este alumno como DEUDOR?');">
                                Deudor
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>