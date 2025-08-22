
<?php if (!empty($datos['errores'])): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($datos['errores'] as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>
<?php if (!empty($datos['mensajes'])): ?>
    <div class="alert alert-success">
        <ul>
            <?php foreach ($datos['mensajes'] as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>
<form action="<?= $datos['action'] ?>" method="POST" id="permisoForm">

    <div class="mb-3 row">
        <div class="col-md-5">
            <label class="form-label d-block">Tipo de Permiso</label>
            <div class="btn-group" role="group" aria-label="Tipo de permiso">
                <input type="radio" class="btn-check" name="tipo_permiso" id="charter" autocomplete="off" value="charter" checked>
                <label class="btn btn-outline-primary" for="charter">Charter</label>

                <input type="radio" class="btn-check" name="tipo_permiso" id="linea" autocomplete="off" value="linea">
                <label class="btn btn-outline-primary" for="linea">Línea</label>

                <input type="radio" class="btn-check" name="tipo_permiso" id="otros" autocomplete="off" value="otros">
                <label class="btn btn-outline-primary" for="otros">Otros</label>
            </div>
            <div class="btn-group" role="group" aria-label="arribo_salida">
                <input type="radio" class="btn-check" name="arribo_salida" id="arribo" autocomplete="off" value="arribo" checked>
                <label class="btn btn-outline-info" for="arribo">Arribo</label>

                <input type="radio" class="btn-check" name="arribo_salida" id="salida" autocomplete="off" value="salida">
                <label class="btn btn-outline-info" for="salida">Salida</label>
            </div>
        </div>

    </div>
    <div class="row mb-3">

        <!-- Servicio -->
        <div class="col-md-6 d-flex align-items-end">
            <div class="flex-grow-1">
                <label for="servicio" class="form-label">Servicio</label>
                <select class="form-select" id="servicio" name="id_servicio" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($datos['servicios'] as $s): ?>
                        <option value="<?= $s['id_servicio'] ?>">
                            <?= htmlspecialchars($s['interno'] . ' - ' . $s['dominio']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" class="btn btn-outline-success ms-2" data-bs-toggle="modal" data-bs-target="#modalServicio">+</button>
        </div>

        <!-- Lugar -->
        <div class="col-md-6 d-flex align-items-end">
            <div class="flex-grow-1">
                <label for="lugar" class="form-label">Origen/Destino</label>
                <select class="form-select" data-live-search="true" id="lugar" name="id_lugar" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($datos['lugares'] as $l): ?>
                        <option value="<?= $l['id_lugar'] ?>">
                            <?= htmlspecialchars($l['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" class="btn btn-outline-success ms-2" data-bs-toggle="modal" data-bs-target="#modalLugar">+</button>
        </div>
    </div>
    <div class="row mb-3">
        <!-- Chofer -->
        <div class="col-md-6 d-flex align-items-end">
            <div class="flex-grow-1">
                <label for="chofer" class="form-label">Chofer</label>
                <select class="form-select" data-live-search="true" id="chofer" name="id_chofer" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($datos['choferes'] as $c): ?>
                        <option value="<?= $c['id_chofer'] ?>">
                            <?= htmlspecialchars($c['dni'] . ' - ' . $c['nombre'] . ' ' . $c['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" class="btn btn-outline-success ms-2" data-bs-toggle="modal" data-bs-target="#modalChofer">+</button>
        </div>

    <!-- Fecha -->

        <div class="col-md-3">
            <label for="fecha_reserva" class="form-label">Fecha Reserva</label>
            <input type="date" class="form-control" id="fecha_reserva" name="fecha_reserva" 
                   value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="col-md-3">
            <label for="pasajeros" class="form-label">Pasajeros</label>
            <input type="number" class="form-control" id="pasajeros" name="pasajeros"></input>
        </div>
    </div>
    
    <!-- Recorrido -->
    <div class="mb-3 row">
        <div class="col-md-10 mb-2">
            <label for="recorrido" class="form-label">Recorrido</label>
            <select class="form-select" id="recorrido" name="id_recorrido" required>
                <option value="">Seleccione...</option>
                <?php foreach ($datos['recorridos'] as $r): ?>
                    <option value="<?= $r['id_recorrido'] ?>">
                        <?= htmlspecialchars($r['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

        </div>
            <div class="col-sm-2 d-flex align-items-end mb-2">
                <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#modalRecorrido">+</button>
            </div>
            <!-- Accordion de Recorrido -->
            <div class="accordion mb-3 d-none" id="accordionRecorrido">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingRecorrido">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRecorrido" aria-expanded="true" aria-controls="collapseRecorrido">
                    Detalles del Recorrido
                </button>
                </h2>
                <div id="collapseRecorrido" class="accordion-collapse collapse show" aria-labelledby="headingRecorrido">
                <div class="accordion-body">
                    <div class="row">
                    <div class="col-md-6">
                        <h6>Calles del Recorrido</h6>
                        <table class="table table-hover align-middle mb-0" id="tablaCalles">
                        <thead>
                            <tr><th>Nombre</th></tr>
                        </thead>
                        <tbody>
                            <!-- Se cargan dinámicamente -->
                        </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Puntos de Detención</h6>
                        <table class="table table-hover align-middle mb-0" id="tablaPuntos">
                        <thead>
                            <tr>
                                <th>Dirección</th>
                                <th>Hotel</th>
                                <th>Horario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se cargan dinámicamente -->
                        </tbody>
                        </table>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
    </div>


    <!-- Observación -->
    <div class="mb-3">
        <label for="observacion" class="form-label">Observación</label>
        <textarea class="form-control" id="observacion" name="observacion" rows="3"><?php 
                if (isset($datos['values']['observacion'])) 
                { 
                    $valorObservacion = htmlspecialchars($datos['values']['observacion']);
                }
            ?></textarea>
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="<?= URL ?>/permiso" class="btn btn-secondary">Cancelar</a>
</form>

<?php include APP.'/views/pages/partials/modalesPermiso.php'; ?>
<?php if(!empty($datos['imprimir'])): ?>
<script>
    window.open('/sgpc/permiso/imprimir/<?= $datos['imprimir'] ?>', '_blank');
</script>
<?php endif; ?>
<script>
    _URL = '<?= URL ?>';
    window._HOTELES = <?= json_encode($datos['hoteles']) ?>;
</script>
<script src="<?= URL ?>/public/js/permiso.js"></script>