<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include '../includes/header.php'; ?>
    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center">Registro de Alumno</h3>

                        <?php if (!empty($_GET['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ⚠️ <?php echo htmlspecialchars($_GET['error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="../auth/registro.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" name="nombre_completo" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Número de Control</label>
                                <input type="number" name="num_control" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Crea tu Contraseña</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary w-100">Registrarme</button>
                            </div>
                            <div class="mt-3 text-center">
                                <a href="login.php" style="font-size: 1.2rem;">¿Ya tienes cuenta? Inicia sesión</a>
                            </div>
                        </form>
                        <div class="mt-2 text-center">
                            <a href="../assets/pdf/aviso_privacidad.pdf" target="_blank" class="text-muted small">Ver Aviso de Privacidad</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>