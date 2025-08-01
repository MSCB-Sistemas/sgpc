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

    <form action="<?= $datos['action'] ?>" method="POST" id="permisoForm">

        <!-- Chofer -->
        <div class="mb-3 d-flex align-items-end gap-2">
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
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalChofer">+</button>
        </div>

        <!-- Servicio -->
        <div class="mb-3 d-flex align-items-end gap-2">
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
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalServicio">+</button>
        </div>

        <!-- Recorrido -->
        <div class="mb-3 d-flex align-items-end gap-2">
            <div class="flex-grow-1">
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
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalRecorrido">+</button>
        </div>

        <!-- Fechas -->
        <div class="mb-3">
            <label for="fecha_reserva" class="form-label">Fecha Reserva</label>
            <input type="date" class="form-control" id="fecha_reserva" name="fecha_reserva" 
                   value="<?= htmlspecialchars($datos['values']['fecha_reserva'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="fecha_emision" class="form-label">Fecha Emisión</label>
            <input type="date" class="form-control" id="fecha_emision" name="fecha_emision" 
                   value="<?= htmlspecialchars($datos['values']['fecha_emision'] ?? '') ?>">
        </div>

        <!-- Observación -->
        <div class="mb-3">
            <label for="observacion" class="form-label">Observación</label>
            <textarea class="form-control" id="observacion" name="observacion"><?= htmlspecialchars($datos['values']['observacion'] ?? '') ?></textarea>
        </div>

        <!-- Switches -->
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="es_arribo" name="es_arribo" 
                <?= !empty($datos['values']['es_arribo']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="es_arribo">Es Arribo</label>
        </div>

        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="activo" name="activo" 
                <?= !empty($datos['values']['activo']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="activo">Activo</label>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="<?= URL ?>/permiso" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include 'partials/modalesPermiso.php'; ?>
<script src="<?= URL ?>/public/js/permiso.js"></script>
