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
            <input type="text" class="form-control" id="nombre" name="nombre" 
                   value="<?= htmlspecialchars($datos['values']['nombre'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="calle" class="form-label">Calle</label>
            <select class="form-select" id="calle" name="calle" required>
                <option value="">Seleccione...</option>
                <?php foreach ($datos['calles'] as $n): ?>
                    <option value="<?= $n['id_calle'] ?>"
                        <?= ($datos['values']['nombre'] ?? '') == $n['id_calle'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($n['nombre']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="<?= URL ?>/puntosDetencion" class="btn btn-secondary">Cancelar</a>
    </form>
</div>