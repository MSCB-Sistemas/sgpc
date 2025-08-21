
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
    <div class="row mb-3">
    <div class="col-md-2">
        <label for="fecha_desde" class="form-label">Fecha de emision desde</label>
        <input type="date" id="fecha_desde" class="form-control" value="<?php if(!empty($datos['fecha_desde'])){echo $datos['fecha_desde'];} else {echo date('Y-m-d', strtotime("-1 week"));}?>">
    </div>
    <div class="col-md-2">
        <label for="fecha_hasta" class="form-label">Fecha de emision hasta</label>
        <input type="date" id="fecha_hasta" class="form-control" value="<?php if(!empty($datos['fecha_hasta'])){echo $datos['fecha_hasta'];} else {echo date('Y-m-d');}?>">
    </div>
    <div class="col-md-1 d-flex align-items-end">
        <button id="btnFiltrar" class="btn btn-primary w-100">Filtrar</button>
    </div>
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
<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btnFiltrar');
    const fechaDesde = document.getElementById('fecha_desde');
    const fechaHasta = document.getElementById('fecha_hasta');

    function filtrar() {
        const desde = fechaDesde.value;
        const hasta = fechaHasta.value;

        let url = "<?= URL ?>/permiso/index";

        if (desde && hasta) {
            url += "/" + desde + "/" + hasta;
        } else if (desde) {
            url += "/" + desde;
        } else if (hasta) {
            url += "/0/" + hasta; // ejemplo: "0" cuando no hay fecha desde
        }

        window.location.href = url;
    }

    // Click en el botón
    btn.addEventListener('click', filtrar);

    // Enter en los inputs
    [fechaDesde, fechaHasta].forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // evita enviar un form si hubiera
                filtrar();
            }
        });
    });
});
</script>
<?php require_once APP . '/views/pages/partials/modalPermisoIndex.php' ?>
