<div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">⚠️ Confirmar Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="fs-5 mb-0" id="modalConfirmMensaje">¿Estás seguro de realizar esta acción?</p>
            </div>
            <div class="modal-footer justify-content-center gap-3 border-0 pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    No
                </button>
                <button type="button" class="btn btn-primary px-4" id="btnConfirmarSi">
                    Sí, continuar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ══════════════════════════════════════════
// Función global para usar el modal
// Parámetros:
//   mensaje  — texto a mostrar en el modal
//   callback — función a ejecutar si confirma
//   colorBtn — color del botón Sí (opcional, default: 'primary')
// ══════════════════════════════════════════
function confirmarAccion(mensaje, callback, colorBtn = 'primary') {
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmacion'));
    const btnSi = document.getElementById('btnConfirmarSi');
    const msgEl = document.getElementById('modalConfirmMensaje');

    msgEl.textContent = mensaje;

    // Cambiar color del botón según la acción
    btnSi.className = `btn btn-${colorBtn} px-4`;

    // Clonar para limpiar listeners anteriores
    const btnSiNuevo = btnSi.cloneNode(true);
    btnSi.parentNode.replaceChild(btnSiNuevo, btnSi);

    btnSiNuevo.addEventListener('click', function() {
        modal.hide();
        callback();
    });

    modal.show();
}
</script>