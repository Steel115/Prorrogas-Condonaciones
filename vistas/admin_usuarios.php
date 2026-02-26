<?php 
require_once '../config/db.php'; 
include '../includes/auth_check.php'; 
require_once '../includes/auth_check.php';

permitirAcceso(['admin']);

$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY nombre_completo ASC");
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Personal</title>
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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Usuarios</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                + Agregar Personal
            </button>
        </div>

        <div class="card shadow-sm">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>N° Trabajador</th>
                        <th>Nombre</th>
                        <th>Área</th>
                        <th>Rol</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?php echo $u['num_trabajador']; ?></td>
                        <td><?php echo htmlspecialchars($u['nombre_completo']); ?></td>
                        <td><?php echo htmlspecialchars($u['area_trabajo']); ?></td>
                        <td>
                            <?php echo ($u['rol'] == 1) ? '<span class="badge bg-danger">Admin</span>' : '<span class="badge bg-info">Contribuyente</span>'; ?>
                        </td>
                        <td>
                            <?php echo ($u['estatus'] == 1) ? '<span class="text-success">Activo</span>' : '<span class="text-muted">Inactivo</span>'; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditarUsuario" 
                                    data-nombre="<?php echo htmlspecialchars($u['nombre_completo']); ?>"
                                    data-num="<?php echo $u['num_trabajador']; ?>"
                                    data-rol="<?php echo $u['rol']; ?>"
                                    data-area="<?php echo htmlspecialchars($u['area_trabajo']); ?>"
                                    onclick="llenarModalUsuario(this)">
                                ✏️ Editar
                            </button>
                            <a href="../auth/cambiar_estatus_usuario.php?id=<?php echo $u['num_trabajador']; ?>" class="btn btn-sm <?php echo ($u['estatus'] == 1) ? 'btn-outline-danger' : 'btn-outline-success'; ?>">
                                <?php echo ($u['estatus'] == 1) ? 'Desactivar' : 'Activar'; ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include 'modales/nuevo_usuario.php'; ?>
    <?php include 'modales/editar_usuario.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function llenarModalUsuario(boton) {
    const id = boton.getAttribute('data-id');
    const nombre = boton.getAttribute('data-nombre');
    const num = boton.getAttribute('data-num');
    const rol = boton.getAttribute('data-rol');
    document.getElementById('edit_user_nombre').value = nombre;
    document.getElementById('edit_user_num').value = num;
    document.getElementById('edit_user_rol').value = rol;
}
</script>
</body>
</html>