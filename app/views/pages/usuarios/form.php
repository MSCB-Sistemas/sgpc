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
            <?php
                if (!empty($datos['values']['usuario'])) {
                    $usuario = htmlspecialchars($datos['values']['usuario']);
                } else {$usuario = ''; }
            ?>
            <input type="text" class="form-control" id="usuario" name="usuario" value="<?= $usuario ?>" required>
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <?php 
                if (!empty($datos['values']['nombre'])) {
                    $nombre = htmlspecialchars($datos['values']['nombre']);
                } else {$nombre = '';}
            ?></php>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $nombre ?>" required>
        </div>

        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <?php 
                if (!empty($datos['values']['apellido'])) {
                    $apellido = htmlspecialchars($datos['values']['apellido']);
                } else {$apellido = '';} 
            ?>
            <input type="text" class="form-control" id="apellido" name="apellido" value="<?= $apellido ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="cargo" class="form-label">Cargo</label>
            <?php 
                if (!empty($datos['values']['cargo'])) {
                    $cargo = htmlspecialchars($datos['values']['cargo']);
                } else {$cargo = '';} 
            ?>
            <input type="text" class="form-control" id="cargo" name="cargo" value="<?= $cargo ?>">
        </div>

        <div class="mb-3">
            <label for="sector" class="form-label">Sector</label>
            <?php 
                if (!empty($datos['values']['sector'])) {
                    $sector = htmlspecialchars($datos['values']['sector']);
                } else {$sector = '';} 
            ?>
            <input type="text" class="form-control" id="sector" name="sector" value="<?= $sector ?>">
        </div>

        <div class="mb-3">
            <label for="tipo_usuario" class="form-label">Tipo de usuario</label>
            <?php
                $selectedTipo = '';
                if (!empty($datos['values']['id_tipo_usuario'])) {
                    $selectedTipo = $datos['values']['id_tipo_usuario'];
                } 
            ?>
            <div class="input-group">
                <select class="form-select" id="tipo_usuario" name="tipo_usuario" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($datos['tipos'] as $n): ?>
                        <?php
                            if ($n['id_tipo_usuario'] == 1) continue; // Omitir el tipo 'admin'
                            $selected = '';
                            if (isset($datos['values']['id_tipo_usuario']) && $datos['values']['id_tipo_usuario'] == $n['id_tipo_usuario']) {
                                $selected = 'selected';
                            }
                        ?>
                        <option value="<?= $n['id_tipo_usuario'] ?>"<?= $selected ?>>
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
