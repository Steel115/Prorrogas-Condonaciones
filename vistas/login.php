<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/layout.css">
</head>

<body class="bg-light">
    <?php include '../includes/header.php'; ?>
    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center">Inicio de sesión</h3>

                        <?php if (!empty($_GET['msg'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                ✅ <?php echo htmlspecialchars($_GET['msg']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($_GET['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ⚠️ <?php echo htmlspecialchars($_GET['error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        <form action="../auth/login_process.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Número de Control / Trabajador</label>
                                <input type="text" name="identificador" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            
                            <div class="mb-3 p-3 border rounded shadow-sm bg-light">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" class="btn-check" name="terminos" id="terminos" autocomplete="off" required>
                                
                                    <label class="btn btn-sm btn-outline-success fw-bold w-auto me-2" for="terminos">
                                        <i class="bi bi-check-lg">Acepto</i> 
                                    </label>

                                    <span class="small text-muted">
                                        los términos de uso
                                        <a href="../assets/pdf/terminos.pdf" target="_blank" class="text-decoration-none fw-bold">
                                            términos de uso
                                        </a>
                                    </span>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Entrar al Sistema</button>
                            <div class="mt-3 text-center">
                                <a href="registro.php" style="font-size: 1.2rem;">¿Aun no tienes cuenta? Registrate aqui</a>
                            </div>
                        </form>
                        <!--documento de los avisos de privacidad-->
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