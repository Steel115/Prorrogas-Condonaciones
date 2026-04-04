<?php 
require_once '../config/db.php'; 
require_once '../includes/auth_check.php';
permitirAcceso(['admin', 'contribuyente']);
include '../includes/header.php';

$sql = "SELECT s.id_solicitud, al.nombre_completo, al.num_control, a.titulo, s.ultima_modificacion 
        FROM solicitudes s
        JOIN alumnos al ON s.num_control_alumno = al.num_control
        JOIN asignaciones a ON s.id_asignacion = a.id_asignacion
        WHERE s.estatus = 'Deudor'";

$stmt = $pdo->query($sql);
$deudores = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <main class="container mt-4">
        <div class="container mt-4">
            <h3 class="text-danger">Lista de Deudores</h3>
            <div class="card shadow">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>N° Control</th>
                                <th>Trámite</th>
                                <th>Fecha de Reporte</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deudores as $d): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($d['nombre_completo']); ?></td>
                                <td><?php echo $d['num_control']; ?></td>
                                <td><?php echo htmlspecialchars($d['titulo']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($d['ultima_modificacion'])); ?></td>
                                <td>
                                    <button type="button"
                                        class="btn btn-sm btn-success btn-quitar-deuda"
                                        data-url="../auth/quitar_deudor.php?id=<?php echo $d['id_solicitud']; ?>"
                                        data-nombre="<?php echo htmlspecialchars($d['nombre_completo'], ENT_QUOTES); ?>">
                                        Quitar deuda
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>

    <?php include 'modales/modal_confirmacion.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.btn-quitar-deuda').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const nombre = this.getAttribute('data-nombre');
            confirmarAccion(
                '¿Confirmas que deseas quitar la deuda de ' + nombre + '?',
                function() { window.location.href = url; },
                'success'
            );
        });
    });
    </script>
</body>
</html>