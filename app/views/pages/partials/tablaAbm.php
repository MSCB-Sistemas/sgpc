<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><?= $datos['title'] ?></h2>
        <?php if (!empty($datos['urlCrear'])): ?>
            <a href="<?= $datos['urlCrear'] ?>" class="btn btn-success">+ Nuevo</a>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <input type="text" id="busqueda" class="form-control" placeholder="Buscar...">
    </div>

    <div class="table-responsive shadow rounded" style="overflow: hidden;">
        <table class="table table-hover align-middle mb-0" id="tablaABM">
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
                            <td><?= ucfirst(htmlspecialchars($fila[$key])) ?></td>
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
