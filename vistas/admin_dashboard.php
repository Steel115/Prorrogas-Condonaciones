<?php 
require_once '../config/db.php';
include '../includes/auth_check.php'; 
if ($_SESSION['rol'] !== 'admin') {
    header("Location: alumno_tramites.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
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
            <li class="nav-item"><a class="nav-link active" href="#">Asignaciones</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_usuarios.php">Usuarios</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_solicitudes.php">Solicitudes</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_historial.php">Historial</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_deudores.php">Deudores</a></li>
        </ul>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Asignaciones Activas</h4>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevaAsignacion">
                + Nueva Asignación
            </button>
        </div>
            <?php
            // 
            $stmt = $pdo->prepare("SELECT * FROM asignaciones WHERE estatus = 0 ORDER BY fecha_creacion DESC");
            $stmt->execute();
            $asignaciones = $stmt->fetchAll();

            foreach ($asignaciones as $asig): ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm border-primary">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($asig['titulo']); ?></h5>
                            <p class="text-muted mb-1"><strong>Ciclo:</strong> <?php echo $asig['ciclo_escolar']; ?></p>
                            <p class="text-danger small"><strong>Fecha Límite:</strong> <?php echo date('d/m/Y H:i', strtotime($asig['fecha_limite'])); ?></p>
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button class="btn btn-outline-primary btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditarAsignacion"
                                        data-id="<?php echo $asig['id_asignacion']; ?>"
                                        data-titulo="<?php echo htmlspecialchars($asig['titulo']); ?>"
                                        data-ciclo="<?php echo htmlspecialchars($asig['ciclo_escolar']); ?>"
                                        data-fecha="<?php echo date('Y-m-d\TH:i', strtotime($asig['fecha_limite'])); ?>"
                                        data-instrucciones="<?php echo htmlspecialchars($asig['instrucciones']); ?>"
                                        data-terminos="<?php echo htmlspecialchars($asig['terminos']); ?>"
                                        onclick="llenarModalEditar(this)">
                                    ✏️ Editar
                                </button>
                                <a href="../auth/archivar_asignacion.php?id=<?php echo $asig['id_asignacion']; ?>" 
                                class="btn btn-warning btn-sm"> 📁 </a>
                                <a href="../auth/eliminar_asignacion.php?id=<?php echo $asig['id_asignacion']; ?>" 
                                class="btn btn-outline-danger btn-sm" 
                                onclick="return confirm('¡ADVERTENCIA! Esta acción borrará el trámite PERMANENTEMENTE de la base de datos. ¿Deseas continuar?');">
                                ❌ </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

    </div>

    <?php include 'modales/nueva_asignacion.php'; ?>
    <?php include 'modales/editar_asignacion.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
function llenarModalEditar(boton) {
    // Obtenemos los datos del botón
    const id = boton.getAttribute('data-id');
    const titulo = boton.getAttribute('data-titulo');
    const ciclo = boton.getAttribute('data-ciclo');
    const fecha = boton.getAttribute('data-fecha');
    const instrucciones = boton.getAttribute('data-instrucciones');
    const terminos = boton.getAttribute('data-terminos');

    // Los ponemos en los inputs del modal
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_titulo').value = titulo;
    document.getElementById('edit_ciclo').value = ciclo;
    document.getElementById('edit_fecha').value = fecha;
    document.getElementById('edit_instrucciones').value = instrucciones;
    document.getElementById('edit_terminos').value = terminos;
}
</script>
</body>
</html>