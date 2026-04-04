<!-- ══════════════════════════════════════════
     footer.php — Footer centralizado
     Incluir al final de cada vista:
     <?php //include '../includes/footer.php'; ?>
══════════════════════════════════════════ -->
<?php $rol = $_SESSION['rol'] ?? null; ?>

<style>
    html, body {
    height: 100%;
    margin: 0;
    display: flex;
    flex-direction: column;
    }

    body > *:not(footer) {
        flex: 1;
    }
    .ftr {
        background-color: #1c3c6c;
        margin-top: 3rem;
        box-shadow: 0 -2px 6px rgba(0,0,0,0.2);
    }
    .ftr-recursos {
        padding: 0 2rem;
    }
    .ftr-acordeon-btn {
        width: 100%;
        background: none;
        border: none;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        color: rgba(255,255,255,0.75);
        font-size: 0.9rem;
        font-weight: 600;
        text-align: left;
        padding: 0.9rem 0;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: color 0.2s;
    }
    .ftr-acordeon-btn:hover {
        color: #ffffff;
    }
    .ftr-acordeon-btn .ftr-chevron {
        font-size: 0.75rem;
        transition: transform 0.3s ease;
    }
    .ftr-acordeon-btn.abierto .ftr-chevron {
        transform: rotate(180deg);
    }
    .ftr-acordeon-body {
        display: none;
        padding: 1rem 0;
    }
    .ftr-acordeon-body.visible {
        display: block;
    }
    .ftr-recursos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.75rem;
    }
    .ftr-recurso-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        background-color: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 6px;
        padding: 0.65rem 0.85rem;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    .ftr-recurso-item:hover {
        background-color: rgba(255,255,255,0.14);
    }
    .ftr-recurso-icono {
        font-size: 1.3rem;
        line-height: 1;
    }
    .ftr-recurso-nombre {
        color: #ffffff;
        font-size: 0.85rem;
        font-weight: 600;
        display: block;
        line-height: 1.2;
    }
    .ftr-recurso-desc {
        color: rgba(255,255,255,0.5);
        font-size: 0.75rem;
    }
    .ftr-proximamente {
        color: rgba(255,255,255,0.45);
        font-size: 0.85rem;
        padding: 0.5rem 0;
        font-style: italic;
    }
    .ftr-barra-inferior {
        background-color: #15305a;
        border-top: 1px solid #0f2240;
        padding: 0.6rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .ftr-copy {
        color: rgba(255,255,255,0.45);
        font-size: 0.8rem;
    }
    .ftr-version {
        color: rgba(255,255,255,0.3);
        font-size: 0.75rem;
    }
</style>

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

                <a href="#" class="ftr-recurso-item">
                    <span class="ftr-recurso-icono">📖</span>
                    <div>
                        <span class="ftr-recurso-nombre">Manual de Usuario</span>
                        <span class="ftr-recurso-desc">Guía del sistema</span>
                    </div>
                </a>

                <!-- ✅ Agrega más recursos aquí siguiendo el mismo patrón -->

            </div>
        </div>

        <?php elseif ($rol === 'admin' || $rol === 'contribuyente'): ?>
        <button class="ftr-acordeon-btn" onclick="toggleFooter(this)">
            🛠️ Recursos administrativos
            <span class="ftr-chevron">▼</span>
        </button>
        <div class="ftr-acordeon-body">
            <p class="ftr-proximamente">Próximamente se agregarán recursos útiles para el área administrativa.</p>
            <!-- ✅ Agrega recursos aquí cuando los tengas -->
        </div>

        <?php endif; ?>

    </div>

    <!-- Barra inferior -->
    <div class="ftr-barra-inferior">
        <span class="ftr-copy">Sistema de Prórrogas y Condonaciones</span>
        <span class="ftr-version">v1.0</span>
    </div>
</footer>

<script>
function toggleFooter(btn) {
    const body = btn.nextElementSibling;
    body.classList.toggle('visible');
    btn.classList.toggle('abierto');
}
</script>