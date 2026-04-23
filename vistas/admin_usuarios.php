<?php 
require_once '../config/db.php'; 
require_once '../includes/auth_check.php';
permitirAcceso(['admin']);
include '../includes/header.php';

$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY estatus DESC, nombre_completo ASC");
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Personal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/layout.css">
</head>
<body class="bg-light">
    <main class="container mt-4">
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
                    <tr class="<?php echo ($u['estatus'] == 0) ? 'opacity-50' : ''; ?>">
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
                                    data-id="<?php echo $u['num_trabajador']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($u['nombre_completo'], ENT_QUOTES); ?>"
                                    data-num="<?php echo $u['num_trabajador']; ?>"
                                    data-rol="<?php echo $u['rol']; ?>"
                                    data-area="<?php echo htmlspecialchars($u['area_trabajo'], ENT_QUOTES); ?>"
                                    onclick="llenarModalUsuario(this)">
                                ✏️ Editar
                            </button>
                            <button type="button"
                                class="btn btn-sm <?php echo ($u['estatus'] == 1) ? 'btn-outline-danger' : 'btn-outline-success'; ?> btn-cambiar-estatus"
                                data-url="../auth/cambiar_estatus_usuario.php?id=<?php echo $u['num_trabajador']; ?>"
                                data-nombre="<?php echo htmlspecialchars($u['nombre_completo'], ENT_QUOTES); ?>"
                                data-accion="<?php echo ($u['estatus'] == 1) ? 'deshabilitar' : 'habilitar'; ?>">
                                <?php echo ($u['estatus'] == 1) ? 'Deshabilitar' : 'Habilitar'; ?>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include 'modales/nuevo_usuario.php'; ?>
    <?php include 'modales/editar_usuario.php'; ?>
    <?php include 'modales/modal_confirmacion.php'; ?>
    <?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function llenarModalUsuario(boton) {
    document.getElementById('edit_user_id').value     = boton.getAttribute('data-id');
    document.getElementById('edit_user_nombre').value = boton.getAttribute('data-nombre');
    document.getElementById('edit_user_num').value    = boton.getAttribute('data-num');
    document.getElementById('edit_user_rol').value    = boton.getAttribute('data-rol');
    document.getElementById('edit_user_area').value   = boton.getAttribute('data-area');
}

document.querySelectorAll('.btn-cambiar-estatus').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const url     = this.getAttribute('data-url');
        const nombre  = this.getAttribute('data-nombre');
        const accion  = this.getAttribute('data-accion');
        const esDeshabilitar = accion === 'deshabilitar';

        confirmarAccion(
            '¿Confirmas que deseas ' + accion + ' al usuario ' + nombre + '?',
            function() { window.location.href = url; },
            esDeshabilitar ? 'danger' : 'success'
        );
    });
});
</script>
</body>
</html>