<?php 
require_once '../config/db.php';
require_once '../config/db_institucional.php';
require_once '../includes/auth_check.php';
permitirAcceso(['admin', 'contribuyente']);
include '../includes/header.php';

$asignacionesActivas = $pdo->prepare("SELECT * FROM asignaciones WHERE estatus IN (0, 2) ORDER BY fecha_creacion DESC");
$asignacionesActivas->execute();
$asignaciones = $asignacionesActivas->fetchAll();

foreach ($asignaciones as &$asig) {
    $stmtSol = $pdo->prepare("
        SELECT s.id_solicitud, al.num_control, al.nombre_completo, al.es_deudor,
               s.estatus, s.fecha_envio, s.comentarios
        FROM solicitudes s
        JOIN alumnos al ON s.num_control_alumno = al.num_control
        WHERE s.id_asignacion = ? AND s.estatus IN ('Finalizada', 'Rechazada', 'Deudor')
        ORDER BY s.fecha_envio DESC
    ");
    $stmtSol->execute([$asig['id_asignacion']]);
    $asig['solicitudes'] = $stmtSol->fetchAll();

    foreach ($asig['solicitudes'] as &$sol) {
        $sol['carrera'] = 'N/A';
        if ($pdo_inst) {
            $stmtInst = $pdo_inst->prepare("SELECT carrera FROM alumnos_inst WHERE aluctr = ?");
            $stmtInst->execute([$sol['num_control']]);
            $instData = $stmtInst->fetch();
            if ($instData) $sol['carrera'] = $instData['carrera'] ?? 'N/A';
        }
    }
    unset($sol);
}
unset($asig);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes de Trámites Finalizados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/layout.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
            body { background: white !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        }
    </style>
</head>
<body class="bg-light">
    <main class="container mt-4" id="contenidoReporte">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h3><i class="bi bi-file-earmark-text"></i> Reporte de Trámites Finalizados</h3>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="imprimirTodo()">
                    <i class="bi bi-printer"></i> Imprimir Todo
                </button>
                <button class="btn btn-success" onclick="descargarTodoPDF()">
                    <i class="bi bi-file-earmark-pdf"></i> Descargar Todo PDF
                </button>
            </div>
        </div>

        <?php if (count($asignaciones) === 0): ?>
            <div class="alert alert-info">No hay asignaciones activas.</div>
        <?php else: ?>
            <?php foreach ($asignaciones as $asig): ?>
                <?php 
                $tieneSolicitudes = count($asig['solicitudes']) > 0;
                $idTabla = 'tabla_' . $asig['id_asignacion'];
                ?>
                <div class="card shadow-sm mb-4" id="card_<?php echo $asig['id_asignacion']; ?>">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><?php echo htmlspecialchars($asig['titulo']); ?></h5>
                            <small>Ciclo: <?php echo htmlspecialchars($asig['ciclo_escolar']); ?></small>
                        </div>
                        <div class="no-print">
                            <button class="btn btn-sm btn-light" onclick="descargarPDF('<?php echo $idTabla; ?>', '<?php echo htmlspecialchars($asig['titulo']); ?>')">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </button>
                            <button class="btn btn-sm btn-outline-light" onclick="imprimirTabla('<?php echo $idTabla; ?>')">
                                <i class="bi bi-printer"></i> Imprimir
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($tieneSolicitudes): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="<?php echo $idTabla; ?>">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>N° Control</th>
                                            <th>Nombre del Alumno</th>
                                            <th>Carrera</th>
                                            <th>Estatus</th>
                                            <th>Fecha Finalización</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($asig['solicitudes'] as $sol): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo $sol['num_control']; ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($sol['nombre_completo']); ?>
                                                <?php if($sol['es_deudor'] == 1): ?>
                                                    <span class="badge bg-danger ms-1">DEUDOR</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($sol['carrera']); ?></td>
                                            <td>
                                                <?php if($sol['estatus'] === 'Finalizada'): ?>
                                                    <span class="badge bg-success">Finalizada</span>
                                                <?php elseif($sol['estatus'] === 'Rechazada'): ?>
                                                    <span class="badge bg-danger">Rechazada</span>
                                                <?php elseif($sol['estatus'] === 'Deudor'): ?>
                                                    <span class="badge bg-warning text-dark">Deudor</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($sol['fecha_envio'])); ?></td>
                                            <td class="small text-muted">
                                                <?php echo !empty($sol['comentarios']) ? htmlspecialchars($sol['comentarios']) : '-'; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                                <small class="text-muted">Total: <?php echo count($asig['solicitudes']); ?> registro(s)</small>
                                <small class="text-muted no-print">
                                    Finalizadas: <?php echo count(array_filter($asig['solicitudes'], fn($s) => $s['estatus'] === 'Finalizada')); ?> | 
                                    Rechazadas: <?php echo count(array_filter($asig['solicitudes'], fn($s) => $s['estatus'] === 'Rechazada')); ?> | 
                                    Deudores: <?php echo count(array_filter($asig['solicitudes'], fn($s) => $s['estatus'] === 'Deudor')); ?>
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="card-body text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mb-0">No hay trámites finalizados en esta asignación.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <script>
    function descargarPDF(tablaId, titulo) {
        const elemento = document.getElementById(tablaId);
        const card = elemento.closest('.card');
        
        const opt = {
            margin: 10,
            filename: 'Reporte_' + titulo.replace(/[^a-z0-9]/gi, '_') + '.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };
        
        const contenido = card.cloneNode(true);
        contenido.querySelectorAll('.no-print').forEach(el => el.remove());
        
        html2pdf().set(opt).from(contenido).save();
    }

    function imprimirTabla(tablaId) {
        const elemento = document.getElementById(tablaId);
        const card = elemento.closest('.card');
        const clonedCard = card.cloneNode(true);
        clonedCard.querySelectorAll('.no-print').forEach(el => el.remove());
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Impresión</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <body class="p-4">
                ${clonedCard.outerHTML}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    }

    function descargarTodoPDF() {
        const contenido = document.getElementById('contenidoReporte');
        const cloned = contenido.cloneNode(true);
        cloned.querySelectorAll('.no-print').forEach(el => el.remove());
        
        const opt = {
            margin: 10,
            filename: 'Reporte_General_Tramites_Finalizados.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };
        
        html2pdf().set(opt).from(cloned).save();
    }

    function imprimirTodo() {
        const contenido = document.getElementById('contenidoReporte');
        const cloned = contenido.cloneNode(true);
        cloned.querySelectorAll('.no-print').forEach(el => el.remove());
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Impresión</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { padding: 20px; }
                    .card { break-inside: avoid; margin-bottom: 20px; }
                </style>
            </head>
            <body>
                ${cloned.outerHTML}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    }
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
