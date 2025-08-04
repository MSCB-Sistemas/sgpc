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
        <div id="calles-container">
            <div class="mb-3 calle-group">
                <label for="calle" class="form-label">Calle</label>
                <div class="input-group">
                    <select class="form-select" id="calle" name="calle" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($datos['calles'] as $n): ?>
                            <option value="<?= $n['id_calle'] ?>"
                                <?= ($datos['values']['nombre'] ?? '') == $n['id_calle'] ? 'selected' : '' ?>
                                <?= htmlspecialchars($n['nombre']) ?>>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <button type="button" class="btn btn-outline-primary agregar-calle">+</button>

                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del recorrido</label>
            <input type="text" class="form-control" id="nombre" name="nombre" 
                   value="<?= htmlspecialchars($datos['values']['nombre'] ?? '') ?>" required>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="<?= URL ?>/servicio/index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calles_container = document.getElementById('calles-container');

        // Función para agregar una nueva calle al formulario
        function agregarCalle() {
            const nuevo_grupo = document.createElement('div');
            nuevo_grupo.className = 'mb-3 calle-group';

            nuevo_grupo.innerHTML = `
                <label class="form-label">Calle adicional</label>
                <div class="input-group">
                    <select class="form-select" name="calles[]">
                        <option value="">Seleccione...</option>
                        <?php foreach ($datos['calles'] as $n): ?>
                            <option value="<?= $n['id_calle'] ?>">
                                <?= htmlspecialchars($n['nombre']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <button type="button" class="btn btn-outline-danger eliminar-calle">-</button>
                </div>
            `;

            calles_container.appendChild(nuevo_grupo);

            nuevo_grupo.querySelector(' .eliminar-calle').addEventListener('click', function() {
                nuevo_grupo.remove();
            });
        }

        document.querySelector(' .agregar-calle').addEventListener('click', agregarCalle);

        // Eliminar la calle al hacer clic en el botón de eliminar
        const botonEliminar = document.querySelectorAll('.eliminar-calle');
        if (botonEliminar) {
            botonEliminar.addEventListener('click', function() {
                this.closest('.calle-group').remove();
            });
        }
    });
</script>