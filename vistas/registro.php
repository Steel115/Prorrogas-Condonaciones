<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
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
                        <h3 class="card-title text-center">Registro de Alumno</h3>

                        <?php if (!empty($_GET['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ⚠️ <?php echo htmlspecialchars($_GET['error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="../auth/registro.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nombre Completo Empezando por apelldios</label>
                                <input type="text" name="nombre_completo" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Número de Control</label>
                                <input type="number" name="num_control" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Crea tu Contraseña</label>
                                <div id="pwHelp" class="form-text">
                                    <label>Debe de tener 8 caracteres</label>
                                </div>
                                <input type="password" name="password" id="password" class="form-control" 
                                    required minlength="8">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirma tu Contraseña</label>
                                <input type="password" id="confirm_password" class="form-control" required>
                                <div id="matchMessage" class="form-text fw-bold"></div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" id="btnRegistrar" class="btn btn-primary w-100" disabled>
                                    Registrarme
                                </button>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const password = document.getElementById('password');
        const confirm = document.getElementById('confirm_password');
        const message = document.getElementById('matchMessage');
        const btn = document.getElementById('btnRegistrar');

        function validar() {
            const val1 = password.value;
            const val2 = confirm.value;

            // Si están vacíos, no mostrar nada
            if (val1 === "" || val2 === "") {
                message.textContent = "";
                btn.disabled = true;
                return;
            }

            // Validar coincidencia y longitud
            if (val1 === val2 && val1.length >= 8) {
                message.textContent = "✅ Las contraseñas coinciden";
                message.classList.replace('text-danger', 'text-success');
                password.classList.add('is-valid');
                confirm.classList.add('is-valid');
                password.classList.remove('is-invalid');
                confirm.classList.remove('is-invalid');
                btn.disabled = false; // Habilitar botón
            } else {
                if (val1 !== val2) {
                    message.textContent = "❌ Las contraseñas no coinciden";
                } else {
                    message.textContent = "⚠️ La contraseña debe tener al menos 8 caracteres";
                }
                message.classList.add('text-danger');
                password.classList.add('is-invalid');
                confirm.classList.add('is-invalid');
                btn.disabled = true; // Bloquear botón
            }
        }

        // Escuchar cuando el usuario escribe en cualquiera de los dos
        password.addEventListener('input', validar);
        confirm.addEventListener('input', validar);
    });
    </script>
</body>
</html>