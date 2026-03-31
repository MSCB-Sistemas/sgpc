<div class="container mt-5">
    <h2><?= $datos['title'] ?></h2>

    <?php if (!empty($datos['errores'])): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($datos['errores'] as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= $datos['action'] ?>" method="POST">
        <!-- Nombre del recorrido -->
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del recorrido</label>
            <?php
                if (isset($datos['values']['nombre'])) {
                    $valuesNombre = htmlspecialchars($datos['values']['nombre']);
                } else {
                    $valuesNombre = '';
                }
            ?>
            <input type="text" class="form-control" id="nombre" name="nombre"
                   value="<?= $valuesNombre ?>" required>
        </div>

        <!-- Selector de calles -->
        <div class="mb-3 d-flex gap-2 align-items-end">
            <div class="flex-grow-1">
                <label for="selectCalle" class="form-label">Agregar calle</label>
                <select id="selectCalle" class="form-select">
                    <option value="">-- Seleccionar calle --</option>
                    <?php foreach ($datos['calles'] as $c): ?>
                        <option value="<?= $c['id_calle'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="button" id="addCalle" class="btn btn-primary">+</button>
            </div>
        </div>

        <!-- Tabla de calles seleccionadas -->
        <div class="table-responsive shadow rounded mb-3" style="overflow: hidden;">
            <table class="table table-hover align-middle mb-0" id="tablaCalles">
                <thead class="table-light">
                    <tr>
                        <th>Calle</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($datos['values']['calles_array'])): ?>
                        <?php foreach ($datos['values']['calles_array'] as $idCalle => $nombreCalle): ?>
                            <tr data-id="<?= $idCalle ?>">
                                <td><?= htmlspecialchars($nombreCalle) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger removeCalle">Eliminar</button>
                                </td>
                                <input type="hidden" name="calles[]" value="<?= $idCalle ?>">
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Botones -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> Guardar</button>
            <a href="<?= URL ?>/recorrido" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancelar</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('addCalle').addEventListener('click', function () {
        const select = document.getElementById('selectCalle');
        const id = select.value;
        const nombre = select.options[select.selectedIndex].text;

        if (!id) return;

        if (document.querySelector('#tablaCalles tbody tr[data-id="' + id + '"]')) {
            alert("Esa calle ya fue agregada.");
            return;
        }

        const tbody = document.querySelector('#tablaCalles tbody');
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

    document.getElementById('tablaCalles').addEventListener('click', function (e) {
        if (e.target.classList.contains('removeCalle')) {
            e.target.closest('tr').remove();
        }
    });

    selectCalle.focus();
</script>
