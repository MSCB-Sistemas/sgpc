<!-- Modal Chofer -->
<div class="modal fade" id="modalChofer" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formNuevoChofer">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo Chofer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" class="form-control mb-2" name="nombre" placeholder="Nombre" required>
          <input type="text" class="form-control mb-2" name="apellido" placeholder="Apellido" required>
          <input type="text" class="form-control mb-2" name="dni" placeholder="DNI" required>
          <select class="form-select mb-2" name="nacionalidad" required>
            <option value="">Seleccione Nacionalidad</option>
            <?php foreach ($datos['nacionalidades'] as $n): ?>
              <option value="<?= $n['id_nacionalidad'] ?>"><?= htmlspecialchars($n['nacionalidad']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Servicio -->
<div class="modal fade" id="modalServicio" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formNuevoServicio">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo Servicio</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" class="form-control mb-2" name="interno" placeholder="Interno" required>
          <input type="text" class="form-control mb-2" name="dominio" placeholder="Dominio" required>
          <input type="text" class="form-control mb-2" name="id_empresa" placeholder="ID Empresa" required>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Recorrido -->
<div class="modal fade" id="modalRecorrido" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formNuevoRecorrido">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo Recorrido</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" class="form-control mb-2" name="nombre" placeholder="Nombre" required>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>
