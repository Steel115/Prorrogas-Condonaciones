<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center">Inicio de sesión</h3>
                        <form action="../auth/login_process.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Número de Control / Trabajador</label>
                                <input type="text" name="identificador" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="terminos" id="terminos" required>
                                <label class="form-check-label" for="terminos">
                                    Acepto los <a href="../assets/pdf/terminos.pdf" target="_blank">términos de uso</a>
                                </label>
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
    <footer class="mt-auto py-3 bg-white border-top fixed-bottom">
        
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>