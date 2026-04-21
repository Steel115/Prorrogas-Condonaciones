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
                        <p class="text-muted text-center small">
                            Tu nombre será obtenido automáticamente del sistema escolar.
                        </p>

                        <?php if (!empty($_GET['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ⚠️ <?php echo htmlspecialchars($_GET['error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="../auth/registro.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Número de Control</label>
                                <input type="number" name="num_control" class="form-control" 
                                       placeholder="Ej: 211130240" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Crea tu Contraseña</label>
                                <div id="pwHelp" class="form-text mt-2">
                                    <p>💡 Usa al menos 8 caracteres, números y un símbolo como <strong>@, $, ! o %.</strong></p>
                                </div>
                                <input type="password" name="password" id="password" class="form-control" 
                                    required 
                                    pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                                    title="Mínimo 8 caracteres, incluye letras, números y un carácter especial">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirma tu Contraseña</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
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
    const confirm  = document.getElementById('confirm_password');
    const message  = document.getElementById('matchMessage');
    const pwHelp   = document.getElementById('pwHelp');
    const btn      = document.getElementById('btnRegistrar');

    // Expresión regular para: Letras, Números y Caracteres Especiales
    const regexSeguridad = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])/;

    function validar() {
        const val1 = password.value;
        const val2 = confirm.value;

        // Validación de complejidad visual (Sugerencia)
        if (val1.length > 0) {
            if (!regexSeguridad.test(val1)) {
                pwHelp.style.color = '#856404';
                pwHelp.innerHTML = '⚠️ Debe incluir letras, números y almenos un carácter especial (@, $, !, %).';
            } else {
                pwHelp.style.color = '#155724';
                pwHelp.innerHTML = '✅ ¡Contraseña segura!';
            }
        }

        // Validación de coincidencia y longitud
        if (val1 === "" || val2 === "") {
            message.textContent = "";
            btn.disabled = true;
            return;
        }

        if (val1 === val2 && val1.length >= 8 && regexSeguridad.test(val1)) {
            message.textContent = "✅ Las contraseñas coinciden y son seguras";
            message.className = "form-text fw-bold text-success";
            password.classList.replace('is-invalid', 'is-valid');
            confirm.classList.replace('is-invalid', 'is-valid');
            btn.disabled = false;
        } else {
            if (val1 !== val2) {
                message.textContent = "❌ Las contraseñas no coinciden";
            } else if (val1.length < 8) {
                message.textContent = "⚠️ Mínimo 8 caracteres";
            } else {
                message.textContent = "⚠️ Debe incluir letras, números y símbolos";
            }
            message.className = "form-text fw-bold text-danger";
            password.classList.add('is-invalid');
            confirm.classList.add('is-invalid');
            btn.disabled = true;
        }
    }

    password.addEventListener('input', validar);
    confirm.addEventListener('input', validar);
});
</script>
</body>
</html>