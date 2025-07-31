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
            <label for="empresas" class="form-label">Empresa</label>
            <select class="form-select" id="empresa" name="empresa" required>
                <option value="">Seleccione...</option>
                <?php foreach ($datos['empresas'] as $n): ?>
                    <option value="<?= $n['id_empresa'] ?>"
                        <?= ($datos['values']['nombre'] ?? '') == $n['id_empresa'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($n['nombre']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="interno" class="form-label">Interno</label>
            <input type="text" class="form-control" id="interno" name="interno" 
                   value="<?= htmlspecialchars($datos['values']['interno'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="dominio" class="form-label">Dominio</label>
            <input type="text" class="form-control" id="dominio" name="dominio" 
                   value="<?= htmlspecialchars($datos['values']['dominio'] ?? '') ?>" required>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="<?= URL ?>/servicio/index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
