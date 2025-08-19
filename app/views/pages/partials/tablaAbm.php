
<?php if (!empty($datos['errores'])): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($datos['errores'] as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>
<div class="container-fluid mt-5 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><?= $datos['title'] ?></h2>
        <?php if (!empty($datos['urlCrear'])): ?>
            <a href="<?= $datos['urlCrear'] ?>" class="btn btn-success">+ Nuevo</a>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <input type="text" id="busqueda" class="form-control" placeholder="Buscar...">
    </div>

    <div class="table-responsive-lg shadow rounded" style="overflow: hidden;">
        <table class="table table-hover align-middle mb-0" id="tablaABM" style="min-width: 800px;">
            <thead class="table-light">
                <tr>
                    <?php foreach ($datos['columnas'] as $col): ?>
                        <th><?= $col ?></th>
                    <?php endforeach ?>
                    <?php if (!empty($datos['acciones'])): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos['data'] as $fila): ?>
                    <tr>
                        <?php foreach ($datos['columnas_claves'] as $key): ?>
                            <td class="text-truncate" style="max-width: 200px;"><?= ucfirst(htmlspecialchars($fila[$key])) ?></td>
                        <?php endforeach ?>
                        <?php if (!empty($datos['acciones'])): ?>
                            <td><?= $datos['acciones']($fila) ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const input = document.getElementById('busqueda');
                const tabla = document.getElementById('tablaABM').getElementsByTagName('tbody')[0];

                input.addEventListener('keyup', function () {
                    const filtro = input.value.toLowerCase();
                    const filas = tabla.getElementsByTagName('tr');

                    Array.from(filas).forEach(fila => {
                        const celdas = fila.getElementsByTagName('td');
                        let coincide = false;

                        for (let celda of celdas) {
                            if (!(celda.firstElementChild && celda.firstElementChild.tagName === "A") && celda.textContent.toLowerCase().includes(filtro)) {
                                coincide = true;
                                break;
                            }
                        }

                        fila.style.display = coincide ? '' : 'none';
                    });
                });
            });
        </script>
    </div>
</div>
<!-- Modal para permisos-->
<div class="modal fade" id="modalPermiso" tabindex="-1" aria-labelledby="modalPermisoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPermisoLabel">Detalles del Permiso</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tbody id="modalPermisoBody">
            <!-- Aquí se insertarán los datos dinámicamente -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalPermiso = document.getElementById('modalPermiso');
    var datos = <?= json_encode($datos['data'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    modalPermiso.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Botón que abrió el modal
        var permisoId = button.getAttribute('data-permiso');

        // Buscar la fila correspondiente
        var filaData = null;
        for (var i = 0; i < datos.length; i++) {
            if (datos[i]["Permiso Nro."] == permisoId) {
                filaData = datos[i];
                break; // Salir del bucle
            }
        }

        if (!filaData) return; // Si no se encuentra, salir

        var tbody = modalPermiso.querySelector('#modalPermisoBody');
        tbody.innerHTML = ''; // Limpiar contenido previo

        // Generar filas dinámicamente
        Object.entries(filaData).forEach(([key, value]) => {
            if (key != 'id_recorrido' ){
                tbody.innerHTML += `<tr><th>${key}</th><td>${value}</td></tr>`;
            }
        });
    });
});
</script>