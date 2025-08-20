<!-- Modal para permisos-->
<div class="modal fade" id="modalPermiso" tabindex="-1" aria-labelledby="modalPermisoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="modalPermisoLabel">Detalles del Permiso</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
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
            if (key != 'id_recorrido' && key != 'activo') { // Excluir campos no deseados
                tbody.innerHTML += `<tr><th>${key}</th><td>${value}</td></tr>`;
            }
        });
    });
});
</script>