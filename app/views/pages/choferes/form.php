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
                   value="<?php if (!empty($datos['values']['nombre'])){echo htmlspecialchars($datos['values']['nombre']);}?>" required>
        </div>

        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido" 
                   value="<?php if (!empty($datos['values']['apellido'])){echo htmlspecialchars($datos['values']['apellido']);}?>" required>
        </div>

        <div class="mb-3">
            <label for="dni" class="form-label">DNI</label>
            <input type="text" class="form-control" id="dni" name="dni" 
                   value="<?php if (!empty($datos['values']['dni'])){echo htmlspecialchars($datos['values']['dni']);}?>" required>
        </div>

        <div class="mb-3">
            <label for="nacionalidad" class="form-label">Nacionalidad</label>
            <select class="form-select" id="nacionalidad" name="nacionalidad" required>
                <option value="">Seleccione...</option>
                <?php foreach ($datos['nacionalidades'] as $n): ?>
                    <option value="<?= $n['id_nacionalidad'] ?>"
                        <?php if (!empty($datos['values']['nacionalidad']) && $datos['values']['nacionalidad'] == $n['id_nacionalidad']){echo 'selected';} else {echo '';}?>>
                        <?= htmlspecialchars($n['nacionalidad']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> Guardar</button>
            <a href="<?= URL ?>/calle" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancelar</a>
        </div>
    </form>
</div>
