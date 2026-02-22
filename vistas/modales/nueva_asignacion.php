<div class="modal fade" id="modalNuevaAsignacion" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalLabel">Crear Nueva Asignación</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="../auth/guardar_asignacion.php" method="POST">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label fw-bold">Título del Trámite</label>
              <input type="text" name="titulo" class="form-control" placeholder="Ej: Prórroga de Reinscripción 2026-1" required>
            </div>
            
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Ciclo Escolar</label>
              <input type="text" name="ciclo_escolar" class="form-control" placeholder="Ej: 2026-1" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Fecha Límite</label>
              <input type="datetime-local" name="fecha_limite" class="form-control" required>
            </div>

            <div class="col-md-12 mb-3">
              <label class="form-label fw-bold">Instrucciones (Descripción)</label>
              <textarea name="instrucciones" class="form-control" rows="3" placeholder="Instrucciones que el estudiante debe de seguir" required></textarea>
            </div>

            <div class="col-md-12 mb-3">
              <label class="form-label fw-bold">Términos y Acuerdos</label>
              <textarea name="terminos" class="form-control" rows="3" placeholder="Compromisos del estudiante" required></textarea>
              <div class="form-text text-warning">Este texto aparecera en un recuadro que el estudiante debera de aceptar</div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Crear Asignación</button>
        </div>
      </form>
    </div>
  </div>
</div>