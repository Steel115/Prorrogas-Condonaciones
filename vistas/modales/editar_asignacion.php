<div class="modal fade" id="modalEditarAsignacion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">✏️ Editar Trámite</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="../auth/actualizar_asignacion.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="id_asignacion" id="edit_id">

          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label fw-bold">Título del Trámite</label>
              <input type="text" name="titulo" id="edit_titulo" class="form-control" required>
            </div>
            
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Ciclo Escolar</label>
              <input type="text" name="ciclo_escolar" id="edit_ciclo" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Fecha Límite</label>
              <input type="datetime-local" name="fecha_limite" id="edit_fecha" class="form-control" required>
            </div>

            <div class="col-md-12 mb-3">
              <label class="form-label fw-bold">Instrucciones</label>
              <textarea name="instrucciones" id="edit_instrucciones" class="form-control" rows="3" required></textarea>
            </div>

            <div class="col-md-12 mb-3">
              <label class="form-label fw-bold">Términos y Acuerdos</label>
              <textarea name="terminos" id="edit_terminos" class="form-control" rows="3" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>