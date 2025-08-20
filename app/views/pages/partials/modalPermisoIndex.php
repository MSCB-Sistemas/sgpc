<!-- Modal para permisos-->
<div class="modal fade" id="modalPermiso" tabindex="-1" aria-labelledby="modalPermisoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="modalPermisoLabel">Detalles del Permiso</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <!-- Mensaje de permiso desactivado -->
        <div id="permisoDesactivadoMsg" class="alert alert-secondary text-center fw-bold d-none">
          ESTE PERMISO ESTÁ DESACTIVADO
        </div>

        <table class="table table-striped table-hover table-sm align-middle">
          <tbody id="modalPermisoBody">
            <!-- Aquí se insertarán los datos dinámicamente -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalPermiso = document.getElementById('modalPermiso');
    var datos = <?= json_encode($datos['data'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    modalPermiso.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; 
        var permisoId = button.getAttribute('data-permiso');

        // Buscar la fila correspondiente
        var filaData = datos.find(d => d["Permiso Nro."] == permisoId);
        if (!filaData) return;

        var tbody = modalPermiso.querySelector('#modalPermisoBody');
        var msg = modalPermiso.querySelector('#permisoDesactivadoMsg');

        tbody.innerHTML = ''; // Limpiar contenido previo

        // Mostrar u ocultar el mensaje según estado "activo"
        if (filaData['activo'] == 0) {
            msg.classList.remove('d-none');
        } else {
            msg.classList.add('d-none');
        }

        // Generar filas dinámicamente
        Object.entries(filaData).forEach(([key, value]) => {
            if (key != 'id_recorrido' && key != 'activo') { 
                tbody.innerHTML += `<tr><th>${key}</th><td>${value}</td></tr>`;
            }
        });
    });
});
</script>
