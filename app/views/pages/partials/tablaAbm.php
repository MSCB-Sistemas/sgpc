<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><?= $datos['title'] ?></h2>
        <a href="<?= $datos['urlCrear'] ?>" class="btn btn-success">+ Nuevo</a>
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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos['data'] as $fila): ?>
                    <tr>
                        <?php foreach ($datos['columnas_claves'] as $key): ?>
                            <td><?= ucfirst(htmlspecialchars($fila[$key])) ?></td>
                        <?php endforeach ?>
                        <td><?= $datos['acciones']($fila) ?></td>
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
