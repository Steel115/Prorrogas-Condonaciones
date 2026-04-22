<?php 
require_once '../config/db.php';
require_once '../config/db_institucional.php';
require_once '../includes/auth_check.php';
permitirAcceso(['admin']);
include '../includes/header.php';

$asignacionesActivas = $pdo->prepare("SELECT * FROM asignaciones WHERE estatus IN (0, 2) ORDER BY fecha_creacion DESC");
$asignacionesActivas->execute();
$asignaciones = $asignacionesActivas->fetchAll();

$solicitudesGeneral = [];

foreach ($asignaciones as &$asig) {
    $stmtSol = $pdo->prepare("
        SELECT s.id_solicitud, s.id_asignacion, al.num_control, al.nombre_completo, al.es_deudor, al.carrera,
               s.estatus, a.titulo as nombre_asignacion
        FROM solicitudes s
        JOIN alumnos al ON s.num_control_alumno = al.num_control
        JOIN asignaciones a ON s.id_asignacion = a.id_asignacion
        WHERE s.id_asignacion = ? AND s.estatus IN ('Finalizada', 'Rechazada', 'Deudor')
        ORDER BY 
            CASE s.estatus 
                WHEN 'Deudor' THEN 1 
                WHEN 'Rechazada' THEN 2 
                WHEN 'Finalizada' THEN 3 
            END,
            al.nombre_completo ASC
    ");
    $stmtSol->execute([$asig['id_asignacion']]);
    $asig['solicitudes'] = $stmtSol->fetchAll();

    foreach ($asig['solicitudes'] as $sol) {
        $solicitudesGeneral[] = $sol;
    }
}
unset($asig);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes de Trámites</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/layout.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        /* Estilos para impresión limpia */
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-size: 12px; }
            .tabla-reporte { width: 100%; border-collapse: collapse; }
            .tabla-reporte th, .tabla-reporte td {
                border: 1px solid #ccc;
                padding: 6px 10px;
                text-align: left;
            }
            .tabla-reporte thead { background-color: #f0f0f0 !important; }
            .estatus-pill {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 10px;
                font-size: 11px;
                font-weight: bold;
            }
        }

        /* Tabla minimalista */
        .tabla-reporte {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }
        .tabla-reporte thead tr {
            border-bottom: 2px solid #dee2e6;
        }
        .tabla-reporte thead th {
            padding: 10px 14px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            background: transparent;
        }
        .tabla-reporte tbody tr {
            border-bottom: 1px solid #f1f3f5;
            transition: background 0.15s;
        }
        .tabla-reporte tbody tr:hover {
            background-color: #f8f9fa;
        }
        .tabla-reporte tbody td {
            padding: 10px 14px;
            vertical-align: middle;
            color: #343a40;
        }
        .tabla-reporte tbody tr:last-child {
            border-bottom: none;
        }

        /* Pills de estatus */
        .estatus-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 500;
        }
        .estatus-finalizada { background-color: #d1f5e0; color: #1a7a45; }
        .estatus-rechazada  { background-color: #fde0e0; color: #9b1c1c; }
        .estatus-deudor     { background-color: #fff3cd; color: #856404; }

        /* Separador de secciones */
        .seccion-titulo {
            font-size: 1rem;
            font-weight: 600;
            color: #495057;
            padding: 0.5rem 0;
            border-bottom: 2px solid #1c3c6c;
            margin-bottom: 1.25rem;
        }

        /* Footer de tabla */
        .tabla-footer {
            display: flex;
            justify-content: space-between;
            padding: 8px 14px;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            font-size: 0.8rem;
            color: #6c757d;
            border-radius: 0 0 8px 8px;
        }
    </style>
</head>
<body class="bg-light">
<main class="container mt-4">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h4 class="mb-0 fw-semibold">📊 Reportes de Trámites</h4>
        <button class="btn btn-sm btn-outline-secondary" onclick="verPDFTodo()">
            <i class="bi bi-file-earmark-pdf"></i> Ver PDF General
        </button>
    </div>

    <?php if (count($asignaciones) === 0): ?>
        <div class="alert alert-info">No hay asignaciones activas.</div>
    <?php else: ?>

        <!-- ══════════════════════════════════════════
             SECCIÓN 1: RESUMEN GENERAL
        ══════════════════════════════════════════ -->
        <p class="seccion-titulo">Resumen General</p>

        <div class="bg-white rounded shadow-sm mb-5">
            <div class="d-flex justify-content-between align-items-center px-3 pt-3 pb-2 no-print">
                <small class="text-muted">Todas las asignaciones consolidadas</small>
                <button class="btn btn-sm btn-outline-primary" onclick="verPDF('contenido_general', 'Resumen_General')">
                    <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                </button>
            </div>

            <?php if (count($solicitudesGeneral) > 0): ?>
                <div id="contenido_general">
                    <table class="tabla-reporte">
                        <thead>
                            <tr>
                                <th>Asignación</th>
                                <th>N° Control</th>
                                <th>Nombre del Alumno</th>
                                <th>Carrera</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudesGeneral as $sol): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sol['nombre_asignacion']); ?></td>
                                <td class="fw-semibold"><?php echo $sol['num_control']; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($sol['nombre_completo']); ?>
                                    <?php if($sol['es_deudor'] == 1): ?>
                                        <span class="estatus-pill estatus-deudor ms-1" style="font-size:0.72rem;">DEUDOR</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($sol['carrera']); ?></td>
                                <td>
                                    <?php if($sol['estatus'] === 'Finalizada'): ?>
                                        <span class="estatus-pill estatus-finalizada">Finalizada</span>
                                    <?php elseif($sol['estatus'] === 'Rechazada'): ?>
                                        <span class="estatus-pill estatus-rechazada">Rechazada</span>
                                    <?php elseif($sol['estatus'] === 'Deudor'): ?>
                                        <span class="estatus-pill estatus-deudor">Deudor</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="tabla-footer">
                    <span>Total: <?php echo count($solicitudesGeneral); ?> registro(s)</span>
                    <span class="no-print">
                        ✅ Finalizadas: <?php echo count(array_filter($solicitudesGeneral, fn($s) => $s['estatus'] === 'Finalizada')); ?> &nbsp;|&nbsp;
                        ❌ Rechazadas: <?php echo count(array_filter($solicitudesGeneral, fn($s) => $s['estatus'] === 'Rechazada')); ?> &nbsp;|&nbsp;
                        ⚠️ Deudores: <?php echo count(array_filter($solicitudesGeneral, fn($s) => $s['estatus'] === 'Deudor')); ?>
                    </span>
                </div>
            <?php else: ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    No hay trámites finalizados.
                </div>
            <?php endif; ?>
        </div>

        <hr class="my-5">

        <!-- ══════════════════════════════════════════
             SECCIÓN 2: POR ASIGNACIÓN
        ══════════════════════════════════════════ -->
        <p class="seccion-titulo">Por Asignación</p>

        <?php foreach ($asignaciones as $asig): ?>
            <?php $idContenido = 'contenido_' . $asig['id_asignacion']; ?>
            <div class="bg-white rounded shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center px-3 pt-3 pb-2">
                    <div>
                        <span class="fw-semibold"><?php echo htmlspecialchars($asig['titulo']); ?></span>
                        <small class="text-muted ms-2">Ciclo: <?php echo htmlspecialchars($asig['ciclo_escolar']); ?></small>
                    </div>
                    <?php if (count($asig['solicitudes']) > 0): ?>
                    <button class="btn btn-sm btn-outline-primary no-print"
                            onclick="verPDF('<?php echo $idContenido; ?>', '<?php echo htmlspecialchars($asig['titulo'], ENT_QUOTES); ?>')">
                        <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                    </button>
                    <?php endif; ?>
                </div>

                <?php if (count($asig['solicitudes']) > 0): ?>
                    <div id="<?php echo $idContenido; ?>">
                        <table class="tabla-reporte">
                            <thead>
                                <tr>
                                    <th>N° Control</th>
                                    <th>Nombre del Alumno</th>
                                    <th>Carrera</th>
                                    <th>Estatus</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($asig['solicitudes'] as $sol): ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo $sol['num_control']; ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($sol['nombre_completo']); ?>
                                        <?php if($sol['es_deudor'] == 1): ?>
                                            <span class="estatus-pill estatus-deudor ms-1" style="font-size:0.72rem;">DEUDOR</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($sol['carrera']); ?></td>
                                    <td>
                                        <?php if($sol['estatus'] === 'Finalizada'): ?>
                                            <span class="estatus-pill estatus-finalizada">Finalizada</span>
                                        <?php elseif($sol['estatus'] === 'Rechazada'): ?>
                                            <span class="estatus-pill estatus-rechazada">Rechazada</span>
                                        <?php elseif($sol['estatus'] === 'Deudor'): ?>
                                            <span class="estatus-pill estatus-deudor">Deudor</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tabla-footer">
                        <span>Total: <?php echo count($asig['solicitudes']); ?> registro(s)</span>
                        <span class="no-print">
                            ✅ <?php echo count(array_filter($asig['solicitudes'], fn($s) => $s['estatus'] === 'Finalizada')); ?> &nbsp;|&nbsp;
                            ❌ <?php echo count(array_filter($asig['solicitudes'], fn($s) => $s['estatus'] === 'Rechazada')); ?> &nbsp;|&nbsp;
                            ⚠️ <?php echo count(array_filter($asig['solicitudes'], fn($s) => $s['estatus'] === 'Deudor')); ?>
                        </span>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4" style="font-size:0.88rem;">
                        No hay trámites finalizados en esta asignación.
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</main>

<script>
// ══════════════════════════════════════════
// Genera un PDF limpio para impresión/guardado
// ══════════════════════════════════════════
function verPDF(contenidoId, titulo) {
    const contenido = document.getElementById(contenidoId);
    if (!contenido) return;

    const ventana = window.open('', '_blank');
    ventana.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>${titulo}</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; padding: 24px; color: #222; }
                h2 { font-size: 15px; margin-bottom: 4px; color: #1c3c6c; }
                p.sub { font-size: 11px; color: #666; margin-bottom: 16px; }
                table { width: 100%; border-collapse: collapse; margin-top: 8px; }
                th { background-color: #f0f0f0; border-bottom: 2px solid #ccc; 
                     padding: 7px 10px; text-align: left; font-size: 11px;
                     text-transform: uppercase; color: #555; }
                td { padding: 7px 10px; border-bottom: 1px solid #eee; font-size: 12px; }
                tr:last-child td { border-bottom: none; }
                .pill { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
                .fin { background: #d1f5e0; color: #1a7a45; }
                .rec { background: #fde0e0; color: #9b1c1c; }
                .deu { background: #fff3cd; color: #856404; }
                .footer { margin-top: 16px; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 8px; }
            </style>
        </head>
        <body>
            <h2>${titulo}</h2>
            <p class="sub">Sistema de Prórrogas y Condonaciones — Generado el ${new Date().toLocaleDateString('es-MX')}</p>
            ${contenido.innerHTML
                .replace(/estatus-pill estatus-finalizada/g, 'pill fin')
                .replace(/estatus-pill estatus-rechazada/g, 'pill rec')
                .replace(/estatus-pill estatus-deudor/g, 'pill deu')
                .replace(/tabla-reporte/g, '')
                .replace(/fw-semibold/g, '')
                .replace(/no-print[^""]*/g, '')
            }
            <div class="footer">Total de registros mostrados en este reporte.</div>
        </body>
        </html>
    `);
    ventana.document.close();
    ventana.focus();
    setTimeout(() => ventana.print(), 600);
}

function verPDFTodo() {
    const ventana = window.open('', '_blank');

    // Recopilar todo el contenido de las tablas
    let htmlTablas = '';

    // Resumen general
    const general = document.getElementById('contenido_general');
    if (general) {
        htmlTablas += `
            <h2>Resumen General</h2>
            <p class="sub">Todas las asignaciones consolidadas</p>
            ${limpiarHTML(general.innerHTML)}
            <div class="separador"></div>
        `;
    }

    // Una tabla por asignación
    document.querySelectorAll('[id^="contenido_"]').forEach(function(el) {
        if (el.id === 'contenido_general') return;
        const card = el.closest('.bg-white');
        const tituloEl = card ? card.querySelector('.fw-semibold') : null;
        const cicloEl = card ? card.querySelector('.text-muted.ms-2') : null;
        const titulo = tituloEl ? tituloEl.textContent.trim() : 'Asignación';
        const ciclo = cicloEl ? cicloEl.textContent.trim() : '';
        htmlTablas += `
            <h2>${titulo}</h2>
            <p class="sub">${ciclo}</p>
            ${limpiarHTML(el.innerHTML)}
            <div class="separador"></div>
        `;
    });

    ventana.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte General de Trámites</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; padding: 24px; color: #222; }
                h2 { font-size: 15px; margin: 20px 0 4px; color: #1c3c6c; }
                h2:first-child { margin-top: 0; }
                p.sub { font-size: 11px; color: #666; margin-bottom: 10px; }
                table { width: 100%; border-collapse: collapse; }
                th { background-color: #f0f0f0; border-bottom: 2px solid #ccc;
                     padding: 7px 10px; text-align: left; font-size: 11px;
                     text-transform: uppercase; color: #555; }
                td { padding: 7px 10px; border-bottom: 1px solid #eee; font-size: 12px; }
                tr:last-child td { border-bottom: none; }
                .pill { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
                .fin { background: #d1f5e0; color: #1a7a45; }
                .rec { background: #fde0e0; color: #9b1c1c; }
                .deu { background: #fff3cd; color: #856404; }
                .separador { border-top: 1px solid #ddd; margin: 20px 0; }
                .encabezado { border-bottom: 2px solid #1c3c6c; padding-bottom: 8px; margin-bottom: 20px; }
                .encabezado h1 { font-size: 16px; color: #1c3c6c; margin: 0; }
                .encabezado small { font-size: 11px; color: #999; }
                @media print { .separador { page-break-after: auto; } }
            </style>
        </head>
        <body>
            <div class="encabezado">
                <h1>Reporte General de Trámites</h1>
                <small>Sistema de Prórrogas y Condonaciones — Generado el ${new Date().toLocaleDateString('es-MX')}</small>
            </div>
            ${htmlTablas}
        </body>
        </html>
    `);
    ventana.document.close();
    ventana.focus();
    setTimeout(() => ventana.print(), 600);
}

function limpiarHTML(html) {
    return html
        .replace(/estatus-pill estatus-finalizada/g, 'pill fin')
        .replace(/estatus-pill estatus-rechazada/g, 'pill rec')
        .replace(/estatus-pill estatus-deudor/g, 'pill deu')
        .replace(/tabla-reporte/g, '')
        .replace(/fw-semibold/g, '')
        .replace(/class="no-print[^"]*"/g, 'style="display:none"');
}
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>