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
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido" 
                   value="<?= htmlspecialchars($datos['values']['apellido'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="dni" class="form-label">DNI</label>
            <input type="text" class="form-control" id="dni" name="dni" 
                   value="<?= htmlspecialchars($datos['values']['dni'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="nacionalidad" class="form-label">Nacionalidad</label>
            <select class="form-select" id="nacionalidad" name="nacionalidad" required>
                <option value="">Seleccione...</option>
                <?php foreach ($datos['nacionalidades'] as $n): ?>
                    <option value="<?= $n['id_nacionalidad'] ?>"
                        <?= ($datos['values']['nacionalidad'] ?? '') == $n['id_nacionalidad'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($n['nacionalidad']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="<?= URL ?>/chofer" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
