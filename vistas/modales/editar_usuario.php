<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Editar Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../auth/editar_usuario.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_usuario" id="edit_user_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" name="nombre_completo" id="edit_user_nombre" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">N° Trabajador</label>
                            <input type="text" name="num_trabajador" id="edit_user_num" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Rol</label>
                            <select name="rol" id="edit_user_rol" class="form-select">
                                <option value="admin">Admin</option>
                                <option value="contribuyente">Contribuyente</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Área de Trabajo</label>
                        <input type="text" name="area_trabajo" id="edit_user_area" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nueva Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                        <div class="form-text text-muted">Solo escribe si deseas restablecer la clave del usuario.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>