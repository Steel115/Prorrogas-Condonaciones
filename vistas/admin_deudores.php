<?php 
require_once '../config/db.php'; 
include '../includes/auth_check.php'; 
require_once '../includes/auth_check.php';
permitirAcceso(['admin', 'contribuyente']);

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
    <style>
        .nav-tabs .nav-link.active {
            border-bottom: 3px solid #007bff;
            color: #007bff;
        }
    </style>
</head>

<body class="bg-light">
    <header class="p-3 bg-white border-bottom shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <span><strong>ADMINISTRADOR:</strong> <?php echo $_SESSION['nombre']; ?></span>
            <a href="../auth/logout.php" class="btn btn-sm btn-outline-danger ms-2">Cerrar Sesión</a>
        </div>
    </header>
    <main class="container mt-4">
        <div class="container mt-4">
            <ul class="nav nav-tabs mb-4">
                <?php if ($_SESSION['rol'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php') ? 'active' : ''; ?>" href="admin_dashboard.php">Asignaciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_usuarios.php') ? 'active' : ''; ?>" href="admin_usuarios.php">Usuarios</a>
                    </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_solicitudes.php') ? 'active' : ''; ?>" href="admin_solicitudes.php">Solicitudes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_historial.php') ? 'active' : ''; ?>" href="admin_historial.php">Historial</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_deudores.php') ? 'active' : ''; ?>" href="admin_deudores.php">Deudores</a>
                </li>
            </ul>
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
                                    <a href="../auth/quitar_deudor.php?id=<?php echo $d['id_solicitud']; ?>" class="btn btn-sm btn-success">
                                        Quitar deuda
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <footer class="mt-auto py-3 bg-white border-top fixed-bottom">
        
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>