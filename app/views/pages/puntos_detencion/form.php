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
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
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
        <div class="mb-3">
            <label for="calle" class="form-label">Calle</label>
            <select class="form-select" id="calle" name="calle" required>
                <option value="">Seleccione...</option>
                <?php foreach ($datos['calles'] as $n): ?>
                    <?php
                        $selected = '';
                        if (isset($datos['values']['calle']) && $datos['values']['calle'] == $n['id_calle']) {
                            $selected = 'selected';
                        }
                    ?>
                    <option value="<?= $n['id_calle'] ?>" <?= $selected ?>>
                        <?= htmlspecialchars($n['nombre']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> Guardar</button>
            <a href="<?= URL ?>/puntosdetencion" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancelar</a>
        </div>
    </form>
</div>