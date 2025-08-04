<form action="<?= $datos['action'] ?>" method="POST" id="permisoForm">

    <div class="mb-3 row">
        <div class="col-md-4">
            <label class="form-label d-block">Tipo de Permiso</label>
            <div class="btn-group" role="group" aria-label="Tipo de permiso">
                <input type="radio" class="btn-check" name="tipo_permiso" id="charter" autocomplete="off" value="charter">
                <label class="btn btn-outline-primary" for="charter">Charter</label>

                <input type="radio" class="btn-check" name="tipo_permiso" id="linea" autocomplete="off" value="linea">
                <label class="btn btn-outline-primary" for="linea">Línea</label>
            </div>
            <div class="btn-group" role="group" aria-label="arribo_salida">
                <input type="radio" class="btn-check" name="arribo_salida" id="arribo" autocomplete="off" value="arribo">
                <label class="btn btn-outline-primary" for="arribo">Arribo</label>

                <input type="radio" class="btn-check" name="arribo_salida" id="salida" autocomplete="off" value="salida">
                <label class="btn btn-outline-primary" for="salida">Salida</label>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <!-- Chofer -->
        <div class="col-md-6 d-flex align-items-end">
            <div class="flex-grow-1">
                <label for="chofer" class="form-label">Chofer</label>
                <select class="form-select" id="chofer" name="id_chofer" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($datos['choferes'] as $c): ?>
                        <option value="<?= $c['id_chofer'] ?>"
                            <?= ($datos['values']['id_chofer'] ?? '') == $c['id_chofer'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" class="btn btn-outline-success ms-2" data-bs-toggle="modal" data-bs-target="#modalChofer">+</button>
        </div>

        <!-- Servicio -->
        <div class="col-md-6 d-flex align-items-end">
            <div class="flex-grow-1">
                <label for="servicio" class="form-label">Servicio</label>
                <select class="form-select" id="servicio" name="id_servicio" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($datos['servicios'] as $s): ?>
                        <option value="<?= $s['id_servicio'] ?>"
                            <?= ($datos['values']['id_servicio'] ?? '') == $s['id_servicio'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['interno'] . ' - ' . $s['dominio']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" class="btn btn-outline-success ms-2" data-bs-toggle="modal" data-bs-target="#modalServicio">+</button>
        </div>
    </div>

    <!-- Recorrido -->
    <div class="mb-3 row">
        <div class="col-md-10">
            <label for="recorrido" class="form-label">Recorrido</label>
            <select class="form-select" id="recorrido" name="id_recorrido" required>
                <option value="">Seleccione...</option>
                <?php foreach ($datos['recorridos'] as $r): ?>
                    <option value="<?= $r['id_recorrido'] ?>"
                        <?= ($datos['values']['id_recorrido'] ?? '') == $r['id_recorrido'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#modalRecorrido">+</button>
        </div>
    </div>

    <!-- Fechas -->
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="fecha_reserva" class="form-label">Fecha Reserva</label>
            <input type="date" class="form-control" id="fecha_reserva" name="fecha_reserva" 
                   value="<?= htmlspecialchars($datos['values']['fecha_reserva'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label for="fecha_emision" class="form-label">Fecha Emisión</label>
            <input type="date" class="form-control" id="fecha_emision" name="fecha_emision" 
                   value="<?= htmlspecialchars($datos['values']['fecha_emision'] ?? '') ?>">
        </div>
    </div>

    <!-- Observación -->
    <div class="mb-3">
        <label for="observacion" class="form-label">Observación</label>
        <textarea class="form-control" id="observacion" name="observacion" rows="3"><?= htmlspecialchars($datos['values']['observacion'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="<?= URL ?>/permiso" class="btn btn-secondary">Cancelar</a>
</form>
<?php include APP.'/views/pages/partials/modalesPermiso.php'; ?>
<script>
    _URL = '<?= URL ?>';
</script>
<script src="<?= URL ?>/public/js/permiso.js"></script>