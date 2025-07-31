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
            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" class="form-control" id="usuario" name="usuario" 
                   value="<?= htmlspecialchars($datos['values']['usuario'] ?? '') ?>" required>
        </div>

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
            <label for="cargo" class="form-label">Cargo</label>
            <input type="text" class="form-control" id="cargo" name="cargo" 
                   value="<?= htmlspecialchars($datos['values']['cargo'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="sector" class="form-label">Sector</label>
            <input type="text" class="form-control" id="sector" name="sector" 
                   value="<?= htmlspecialchars($datos['values']['sector'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="tipo_usuario" class="form-label">Tipo de usuario</label>
            <div class="input-group">
                <select class="form-select" id="tipo_usuario" name="tipo_usuario" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($datos['tipos'] as $n): ?>
                        <option value="<?= $n['id_tipo_usuario'] ?>"
                            <?= ($datos['values']['id_tipo_usuario'] ?? '') == $n['id_tipo_usuario'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($n['tipo_usuario']) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <?php if (!$datos['update']): ?>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="<?= URL ?>/usuarios" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
