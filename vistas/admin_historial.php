<?php 
require_once '../config/db.php'; 
require_once '../config/db_institucional.php';
require_once '../includes/auth_check.php';
permitirAcceso(['admin', 'contribuyente']);
include '../includes/header.php';

$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';

// ✅ Solo consulta la tabla alumnos del sistema
$sql = "SELECT a.num_control, a.nombre_completo, a.es_deudor
        FROM alumnos a
        WHERE a.num_control LIKE ? OR a.nombre_completo LIKE ? 
        ORDER BY a.nombre_completo ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute(["%$busqueda%", "%$busqueda%"]);
$alumnos = $stmt->fetchAll();

// ✅ Para cada alumno, obtener carrera de la BD institucional
foreach ($alumnos as &$alu) {
    $alu['carrera'] = 'N/A';
    if ($pdo_inst) {
        $stmtInst = $pdo_inst->prepare("SELECT carrera FROM alumnos_inst WHERE aluctr = ?");
        $stmtInst->execute([$alu['num_control']]);
        $instData = $stmtInst->fetch();
        if ($instData) $alu['carrera'] = $instData['carrera'] ?? 'N/A';
    }
}
unset($alu);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Alumnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/layout.css">
</head>
<body class="bg-light">
    <main class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Control de Expedientes</h3>
        </div>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-10">
                        <input type="text" name="buscar" class="form-control" 
                               placeholder="Buscar por Nombre, Apellido o Número de Control..." 
                               value="<?php echo htmlspecialchars($busqueda); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>No. Control</th>
                            <th>Nombre Completo</th>
                            <th>Carrera</th>
                            <th>Estatus</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($alumnos) > 0): ?>
                            <?php foreach ($alumnos as $alu): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $alu['num_control']; ?></td>
                                <td><?php echo htmlspecialchars($alu['nombre_completo']); ?></td>
                                <td><?php echo htmlspecialchars($alu['carrera']); ?></td>
                                <td>
                                    <?php if($alu['es_deudor']): ?>
                                        <span class="badge bg-danger">⚠️ DEUDOR</span>
                                    <?php else: ?>
                                        <span class="badge bg-success text-white">Regular</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="ver_expediente.php?num_control=<?php echo $alu['num_control']; ?>" 
                                       class="btn btn-sm btn-outline-primary shadow-sm">
                                        <i class="bi bi-eye"></i> Ver Expediente
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No se encontraron alumnos con ese criterio.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>