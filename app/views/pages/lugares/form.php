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
                    $valorNombre = htmlspecialchars($datos['values']['nombre']);
                } else {
                    $valorNombre = '';
                }
            ?>
            <input type="text" class="form-control" id="nombre" name="nombre" 
                   value="<?= $valorNombre ?>" required>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> Guardar</button>
            <a href="<?= URL ?>/calle" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancelar</a>
        </div>
    </form>
</div>
