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

<!-- Modal Chofer -->
<div class="modal fade" id="modalLugar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formNuevoLugar">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo Lugar</h5>
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

          <div class="row mb-2 align-items-center">
            <div class="col-10">
              <select class="form-select" name="empresa" id="selectEmpresa" required>
                <option value="">Empresa</option>
                <?php foreach ($datos['empresas'] as $n): ?>
                  <option value="<?= $n['id_empresa'] ?>"><?= htmlspecialchars($n['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-2">
              <button type="button" class="btn btn-success w-100" id="btnAgregarEmpresa">+</button>
            </div>
          </div>

          <div class="mb-2 d-none" id="campoNuevaEmpresa">
            <input type="text" class="form-control" name="nueva_empresa" placeholder="Nombre de la nueva empresa">
          </div>
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
          <!-- Selector de calles -->
          <div class="mb-3 d-flex gap-2 align-items-end">
              <div class="flex-grow-1">
                  <label for="selectCalle" class="form-label me-1">Agregar calle</label>
                  <button type="button" id="btnRefreshCalles" class="btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                      <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                    </svg>
                  </button>
                  <select id="selectCalle" class="form-select">
                      <option value="">Seleccionar calle</option>
                      <?php foreach ($datos['calles'] as $c): ?>
                          <option value="<?= $c['id_calle'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                      <?php endforeach; ?>
                  </select>
              </div>
              <div>
                  <button type="button" id="addCalle" class="btn btn-primary">↓</button>
                  <a href="<?= URL ?>/calle/create" target="_blank"><button type="button" id="newCalle" class="btn btn-primary">Nueva</button></a>
              </div>
          </div>

          <!-- Tabla de calles seleccionadas -->
          <div class="table-responsive shadow rounded mb-3" style="overflow: hidden;">
              <table class="table table-hover align-middle mb-0" id="tablaCallesModal">
                  <thead class="table-light">
                      <tr>
                          <th>Calle</th>
                          <th>Acción</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const campoNuevaEmpresa = document.getElementById('campoNuevaEmpresa');
  const selectEmpresa = document.getElementById('selectEmpresa');
  const inputNuevaEmpresa = campoNuevaEmpresa.querySelector('input');

  document.getElementById('btnAgregarEmpresa').addEventListener('click', function() {
    campoNuevaEmpresa.classList.toggle('d-none');
    selectEmpresa.disabled = !selectEmpresa.disabled;
    inputNuevaEmpresa.required = !inputNuevaEmpresa.required;
    inputNuevaEmpresa.value = '';
  });

  document.getElementById('btnRefreshCalles').addEventListener('click', async function() {
    try {
      const res = await fetch(`${_URL}/calle/calles`);
      const calles = await res.json();

      const selectCalles = document.getElementById("selectCalle");
      selectCalles.innerHTML = '';
      selectCalles.innerHTML = '<option value="">Seleccionar calle</option>';
      calles.forEach(c => {
        selectCalles.innerHTML += `<option value="${c.id_calle}">${c.nombre}</option>`;
      });

      
    } catch (error) {
      console.log();
    }
  });

  
  document.getElementById('addCalle').addEventListener('click', function () {
      const select = document.getElementById('selectCalle');
      const id = select.value;
      const nombre = select.options[select.selectedIndex].text;

      if (!id) return;

      if (document.querySelector('#tablaCallesModal tbody tr[data-id="' + id + '"]')) {
          alert("Esa calle ya fue agregada.");
          return;
      }

      const tbody = document.querySelector('#tablaCallesModal tbody');
      const tr = document.createElement('tr');
      tr.setAttribute('data-id', id);
      tr.innerHTML = `
          <td>${nombre}</td>
          <td>
              <button type="button" class="btn btn-sm btn-danger removeCalle">Eliminar</button>
          </td>
          <input type="hidden" name="calles[]" value="${id}">
      `;
      tbody.appendChild(tr);
  });
  
  // capturar ENTER en el select
  document.getElementById('selectCalle').addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
          e.preventDefault(); // evita que el form se envíe
          document.getElementById('addCalle').click(); // dispara el mismo evento del botón +
      }
  });
</script>