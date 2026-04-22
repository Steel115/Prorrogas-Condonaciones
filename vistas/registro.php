<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/layout.css">
    <style>
        .form-section { display: none; }
        .form-section.active { display: block; }
        .toggle-switch { cursor: pointer; }
    </style>
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

                        <div class="mb-4 p-3 bg-light rounded border">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switchNuevoIngreso">
                                <label class="form-check-label toggle-switch" for="switchNuevoIngreso">
                                    <strong>¿Eres de Nuevo Ingreso?</strong>
                                    <p class="text-muted small mb-0">Marca esta opción si te inscribes por primera vez y no apareces en el sistema escolar.</p>
                                </label>
                            </div>
                        </div>

                        <form action="../auth/registro.php" method="POST" id="formInstitucional">
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
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                                <div id="matchMessage" class="form-text fw-bold"></div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" id="btnRegistrar" class="btn btn-primary w-100" disabled>
                                    Registrarme
                                </button>
                            </div>
                        </form>

                        <form action="../auth/registro_nuevo.php" method="POST" id="formNuevoIngreso" class="form-section">
                            <input type="hidden" name="es_nuevo_ingreso" value="1">

                            <div class="mb-3">
                                <label class="form-label">CURP</label>
                                <input type="text" name="curp" class="form-control text-uppercase" 
                                       placeholder="AAAA000000HITNRRR00" maxlength="18" required>
                                <div class="form-text">18 caracteres. Consulta tu CURP en 
                                    <a href="https://www.gob.mx/curp" target="_blank">gob.mx/curp</a>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nombre(s)</label>
                                <input type="text" name="nombre" class="form-control" 
                                       placeholder="Juan Manuel" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Apellido Paterno</label>
                                    <input type="text" name="apellido_paterno" class="form-control" 
                                           placeholder="García" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Apellido Materno</label>
                                    <input type="text" name="apellido_materno" class="form-control" 
                                           placeholder="López">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Carrera</label>
                                <select name="carrera" class="form-select" required>
                                    <option value="">Selecciona tu carrera</option>
                                    <option value="INGENIERÍA EN SISTEMAS COMPUTACIONALES">Ing. en Sistemas Computacionales</option>
                                    <option value="INGENIERÍA EN GESTIÓN EMPRESARIAL">Ing. en Gestión Empresarial</option>
                                    <option value="INGENIERÍA EN MECATRÓNICA">Ing. en Mecatrónica</option>
                                    <option value="INGENIERÍA EN INDUSTRIAS ALIMENTARIAS">Ing. en Industrias Alimentarias</option>
                                    <option value="INGENIERÍA EN ADMINISTRACIÓN">Ing. en Administración</option>
                                    <option value="INGENIERÍA CONTABLE Y FINANCIERA">Ing. Contable y Financiera</option>
                                    <option value="LICENCIATURA EN BIOLOGÍA">Lic. en Biología</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control" 
                                       placeholder="correo@tuemail.com" required>
                                <div class="form-text">Este será tu usuario para iniciar sesión.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Crea tu Contraseña</label>
                                <div id="pwHelpNuevo" class="form-text mt-2">
                                    <p>💡 Usa al menos 8 caracteres, números y un símbolo como <strong>@, $, ! o %.</strong></p>
                                </div>
                                <input type="password" name="password" id="passwordNuevo" class="form-control" 
                                    required 
                                    pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                                    title="Mínimo 8 caracteres, incluye letras, números y un carácter especial">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirma tu Contraseña</label>
                                <input type="password" name="confirm_password" id="confirm_passwordNuevo" class="form-control" required>
                                <div id="matchMessageNuevo" class="form-text fw-bold"></div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" id="btnRegistrarNuevo" class="btn btn-success w-100" disabled>
                                    Registrarme
                                </button>
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="login.php" style="font-size: 1.2rem;">¿Ya tienes cuenta? Inicia sesión</a>
                        </div>

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
    const switchNuevo = document.getElementById('switchNuevoIngreso');
    const formInstitucional = document.getElementById('formInstitucional');
    const formNuevo = document.getElementById('formNuevoIngreso');

    switchNuevo.addEventListener('change', function() {
        if (this.checked) {
            formInstitucional.classList.remove('active');
            formInstitucional.classList.add('form-section');
            formNuevo.classList.add('active');
            formNuevo.classList.remove('form-section');
        } else {
            formInstitucional.classList.add('active');
            formInstitucional.classList.remove('form-section');
            formNuevo.classList.remove('active');
            formNuevo.classList.add('form-section');
        }
    });

    // Por defecto mostrar formulario institucional
    formInstitucional.classList.add('active');

    // Validación formulario institucional
    (function() {
        const password = document.getElementById('password');
        const confirm  = document.getElementById('confirm_password');
        const message  = document.getElementById('matchMessage');
        const pwHelp   = document.getElementById('pwHelp');
        const btn      = document.getElementById('btnRegistrar');
        const regexSeguridad = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])/;

        function validar() {
            const val1 = password.value;
            const val2 = confirm.value;

            if (val1.length > 0) {
                if (!regexSeguridad.test(val1)) {
                    pwHelp.style.color = '#856404';
                    pwHelp.innerHTML = '⚠️ Debe incluir letras, números y almenos un carácter especial (@, $, !, %).';
                } else {
                    pwHelp.style.color = '#155724';
                    pwHelp.innerHTML = '✅ ¡Contraseña segura!';
                }
            }

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
    })();

    // Validación formulario nuevo ingreso
    (function() {
        const password = document.getElementById('passwordNuevo');
        const confirm  = document.getElementById('confirm_passwordNuevo');
        const message  = document.getElementById('matchMessageNuevo');
        const pwHelp   = document.getElementById('pwHelpNuevo');
        const btn      = document.getElementById('btnRegistrarNuevo');
        const regexSeguridad = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])/;

        function validar() {
            const val1 = password.value;
            const val2 = confirm.value;

            if (val1.length > 0) {
                if (!regexSeguridad.test(val1)) {
                    pwHelp.style.color = '#856404';
                    pwHelp.innerHTML = '⚠️ Debe incluir letras, números y almenos un carácter especial (@, $, !, %).';
                } else {
                    pwHelp.style.color = '#155724';
                    pwHelp.innerHTML = '✅ ¡Contraseña segura!';
                }
            }

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
    })();
    </script>
</body>
</html>