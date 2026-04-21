<!-- ══════════════════════════════════════════
     Incluir al final de cada vista:
     <?php //include '../includes/footer.php'; ?>
══════════════════════════════════════════ -->
<?php $rol = $_SESSION['rol'] ?? null; ?>

<footer class="ftr">
    <div class="ftr-recursos">

        <?php if ($rol === 'alumno'): ?>
        <button class="ftr-acordeon-btn" onclick="toggleFooter(this)">
            🛠️ Herramientas útiles
            <span class="ftr-chevron">▼</span>
        </button>
        <div class="ftr-acordeon-body">
            <div class="ftr-recursos-grid">

                <a href="https://www.ilovepdf.com/es/comprimir_pdf" target="_blank" class="ftr-recurso-item">
                    <span class="ftr-recurso-icono">📉</span>
                    <div>
                        <span class="ftr-recurso-nombre">Comprimir PDF</span>
                        <span class="ftr-recurso-desc">ilovepdf.com</span>
                    </div>
                </a>

                <a href="https://www.ilovepdf.com/es/jpg_a_pdf" target="_blank" class="ftr-recurso-item">
                    <span class="ftr-recurso-icono">🖼️</span>
                    <div>
                        <span class="ftr-recurso-nombre">JPG a PDF</span>
                        <span class="ftr-recurso-desc">ilovepdf.com</span>
                    </div>
                </a>

                <a href="https://www.ilovepdf.com/es/unir_pdf" target="_blank" class="ftr-recurso-item">
                    <span class="ftr-recurso-icono">📎</span>
                    <div>
                        <span class="ftr-recurso-nombre">Unir PDFs</span>
                        <span class="ftr-recurso-desc">ilovepdf.com</span>
                    </div>
                </a>

                <a href="../assets/pdf/Reinscripcion.pdf" target="_blank" class="ftr-recurso-item">
                    <span class="ftr-recurso-icono">📄</span>
                    <div>
                        <span class="ftr-recurso-nombre">Prorroga de reinscripción</span>
                        <span class="ftr-recurso-desc">Archivo reinscripción</span>
                    </div>
                </a>

                <!-- Aqui agregar mas recursos -->

            </div>
        </div>

        <!-- footer para el admin y contribuyente -->
        <?php elseif ($rol === 'admin' || $rol === 'contribuyente'): ?>
        <button class="ftr-acordeon-btn" onclick="toggleFooter(this)">
            🛠️ Recursos administrativos
            <span class="ftr-chevron">▼</span>
        </button>
        <div class="ftr-acordeon-body">
            <p class="ftr-proximamente">Próximamente</p>
            <!-- agregar más informacion-->
        </div>

        <?php endif; ?>

    </div>

    <!-- Barra inferior -->
    <div class="ftr-barra-inferior">
        <span class="ftr-copy">Sistema de Prórrogas y Condonaciones</span>
        <span class="ftr-version"></span>
    </div>
</footer>

<script>
function toggleFooter(btn) {
    const body = btn.nextElementSibling;
    body.classList.toggle('visible');
    btn.classList.toggle('abierto');
}
</script>