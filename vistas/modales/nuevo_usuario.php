<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Registrar Nuevo Personal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="../auth/guardar_usuario.php" method="POST">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Número de Trabajador</label>
            <input type="number" name="num_trabajador" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nombre Completo</label>
            <input type="text" name="nombre_completo" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Área de Trabajo</label>
            <input type="text" name="area_trabajo" class="form-control" placeholder="Ej: Recursos Financieros" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Rol del Usuario</label>
            <select name="rol" class="form-select">
                <option value="0">Contribuyente (Solo revisa)</option>
                <option value="1">Administrador (Control total)</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Contraseña Temporal</label>
            <input type="password" name="password" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary w-100">Guardar Usuario</button>
        </div>
      </form>
    </div>
  </div>
</div>