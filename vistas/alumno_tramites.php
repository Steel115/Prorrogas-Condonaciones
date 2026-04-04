<?php 
require_once '../config/db.php'; 
require_once '../includes/auth_check.php';
require_once '../includes/verificar_vencimientos.php'; // cierra asignaciones vencidas
include '../includes/header.php';

if ($_SESSION['rol'] !== 'alumno') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Trámites</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container mt-5">
        <h2 class="mb-4">Trámites Disponibles</h2>
        <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ⚠️ <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✅ <?php echo htmlspecialchars($_GET['msg']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <?php
            // ✅ Traer activas (0) y vencidas (2) — el alumno ve ambas pero las vencidas bloqueadas
            $stmt = $pdo->prepare("SELECT * FROM asignaciones WHERE estatus IN (0, 2) ORDER BY fecha_limite ASC");
            $stmt->execute();
            $tramites = $stmt->fetchAll();

            if (count($tramites) > 0):
                foreach ($tramites as $t):
                    $vencida = ($t['estatus'] == 2);
                ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm <?php echo $vencida ? 'border-danger opacity-75' : 'border-0'; ?>">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h5 class="card-title fw-bold mb-0"><?php echo htmlspecialchars($t['titulo']); ?></h5>
                                    <?php if ($vencida): ?>
                                        <span class="badge bg-danger ms-2">⏰ Vencida</span>
                                    <?php endif; ?>
                                </div>
                                <p class="card-text text-muted flex-grow-1 mt-2">
                                    <?php echo mb_strimwidth($t['instrucciones'], 0, 100, "..."); ?>
                                </p>
                                <div class="mt-3">
                                    <span class="badge <?php echo $vencida ? 'bg-danger' : 'bg-warning text-dark'; ?> mb-2">
                                        <?php echo $vencida ? '⏰ Venció:' : 'Límite:'; ?> <?php echo date('d/m/Y H:i', strtotime($t['fecha_limite'])); ?>
                                    </span>
                                    <?php if ($vencida): ?>
                                        <button class="btn btn-secondary w-100" disabled>
                                            🔒 Trámite Cerrado
                                        </button>
                                    <?php else: ?>
                                        <a href="detalle_tramite.php?id=<?php echo $t['id_asignacion']; ?>" 
                                           class="btn btn-primary w-100">Abrir Trámite</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; 
            else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No hay trámites activos por el momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>