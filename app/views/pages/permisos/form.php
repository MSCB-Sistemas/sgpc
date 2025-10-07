
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
        <div class="col-md-10">
            <label class="form-label d-block">Tipo de Permiso</label>
            <div class="btn-group" role="group" aria-label="Tipo de permiso">
                <input type="radio" class="btn-check" name="tipo_permiso" id="charter" autocomplete="off" value="charter" checked>
                <label class="btn btn-outline-primary" for="charter">Charter</label>

                <input type="radio" class="btn-check" name="tipo_permiso" id="linea" autocomplete="off" value="linea">
                <label class="btn btn-outline-primary" for="linea">Línea</label>

                <input type="radio" class="btn-check" name="tipo_permiso" id="convalidaciones" autocomplete="off" value="convalidaciones">
                <label class="btn btn-outline-primary" for="convalidaciones">Convalidaciones</label>

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
                <label for="servicio_search" class="form-label">Servicio</label>
                <!-- SE CAMBIO EL SELECT POR UN INPUT CON DATALIST PARA BUSQUEDA PARA QUE SEA MAS EFICIENTE
                 Y SE TUVO QUE AGREGAR UNA FUNCION EN JAVA SCRIPT PARA QUE ENVIE LA ID Y NO ROMPA EL RESTO DEL CODIGO -->
                <input list="servicios" id="servicio_search" name="servicio_search" class="form-control" placeholder="Buscar servicio..." autocomplete="off" required>
                <input type="hidden" name="id_servicio" id="id_servicio">
                <datalist id="servicios">
                    <?php foreach ($datos['servicios'] as $s): ?>
                        <option value="<?= htmlspecialchars($s['interno'] . ' - ' . $s['dominio']) ?>" data-id="<?= $s['id_servicio'] ?>">
                    <?php endforeach; ?>
                </datalist>
                <!-- SCRIPT PARA QUE FUNCIONE EL DATALIST Y ENVIE LA ID CORRECTA -->
                <script>
                    const servicioInput = document.getElementById('servicio_search');
                    const servicioHidden = document.getElementById('id_servicio');
                    const servicioOptions = document.querySelectorAll('#servicios option');

                    servicioInput.addEventListener('input', () => {
                        const val = servicioInput.value.trim();
                        servicioHidden.value = ''; // limpiar si no coincide
                        let valid = false;

                        servicioOptions.forEach(opt => {
                            if(opt.value === val){
                                servicioHidden.value = opt.dataset.id;
                                valid = true;
                            }
                        });

                        if (!valid) {
                            servicioInput.setCustomValidity("Debe seleccionar un servicio de la lista");
                        } else {
                            servicioInput.setCustomValidity("");
                        }
                    });
                    //Funcion para que tome la validacion del input si se agrega un nuevo servicio
                    function agregarServicio(nombre, id) {
                        const datalist = document.getElementById('servicios');
                        const input = document.getElementById('servicio_search');
                        const hidden = document.getElementById('id_servicio');

                        const option = document.createElement('option');
                        option.value = nombre;
                        option.dataset.id = id;
                        datalist.appendChild(option);

                        hidden.value = id;
                        input.value = nombre;
                        input.setCustomValidity('');
                    }
                    </script>
            </div>
            <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#modalServicio">+</button>
        </div>

        <!-- Lugar -->
        <div class="col-md-6 d-flex align-items-end">
            <div class="flex-grow-1">
                 <!-- SE CAMBIO EL SELECT POR UN INPUT CON DATALIST PARA BUSQUEDA PARA QUE SEA MAS EFICIENTE
                 Y SE TUVO QUE AGREGAR UNA FUNCION EN JAVA SCRIPT PARA QUE ENVIE LA ID Y NO ROMPA EL RESTO DEL CODIGO -->
                <label for="lugar_search" class="form-label">Origen/Destino</label>
                <input list="lugares" id="lugar_search" name="lugar_search" class="form-control" placeholder="Buscar lugar..." autocomplete="off" required>
                <input type="hidden" name="id_lugar" id="id_lugar">
                <datalist id="lugares">
                    <?php foreach ($datos['lugares'] as $l): ?>
                        <option value="<?= htmlspecialchars($l['nombre']) ?>" data-id="<?= $l['id_lugar'] ?>">
                    <?php endforeach; ?>
                </datalist>
        </div>
        <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#modalLugar">+</button>
    </div>
            <!-- SCRIPT PARA QUE FUNCIONE EL DATALIST Y ENVIE LA ID CORRECTA -->
                <script>
                const lugarInput = document.getElementById('lugar_search');
                const lugarHidden = document.getElementById('id_lugar');
                const lugarOptions = document.querySelectorAll('#lugares option');

                lugarInput.addEventListener('input', () => {
                    const val = lugarInput.value.trim();
                    lugarHidden.value = ''; // limpiar si no coincide
                    let valid = false;
                    lugarOptions.forEach(opt => {
                        if(opt.value === val){
                            lugarHidden.value = opt.dataset.id;
                            valid = true;
                        }
                });

                if (!valid) {
                    lugarInput.setCustomValidity("Debe seleccionar un lugar de la lista");
                } else {
                    lugarInput.setCustomValidity(""); // válido
                }
            });
            // función para agregar un nuevo lugar al datalist y al hidden
            function agregarLugar(nombre, id) {
                const datalist = document.getElementById('lugares');
                const input = document.getElementById('lugar_search');
                const hidden = document.getElementById('id_lugar');

                // crear nueva opción
                const option = document.createElement('option');
                option.value = nombre;
                option.dataset.id = id;
                datalist.appendChild(option);

                // actualizar hidden y marcar input como válido
                hidden.value = id;
                input.value = nombre;
                input.setCustomValidity('');
            }
            </script>

    </div>
    <div class="row mb-3">
        <!-- Chofer -->
        <div class="col-md-6 d-flex align-items-end">
            <div class="flex-grow-1">
                <!-- SE CAMBIO EL SELECT POR UN INPUT CON DATALIST PARA BUSQUEDA PARA QUE SEA MAS EFICIENTE
                 Y SE TUVO QUE AGREGAR UNA FUNCION EN JAVA SCRIPT PARA QUE ENVIE LA ID Y NO ROMPA EL RESTO DEL CODIGO -->
                <label for="chofer_search" class="form-label">Chofer</label>
                <input list="choferes" id="chofer_search" name="chofer_search" class="form-control" placeholder="Buscar chofer..." autocomplete="off" required>
                <input type="hidden" name="id_chofer" id="id_chofer">

                <datalist id="choferes">
                    <?php foreach ($datos['choferes'] as $c): ?>
                        <option value="<?= htmlspecialchars($c['dni'] . ' - ' . $c['nombre'] . ' ' . $c['apellido']) ?>" data-id="<?= $c['id_chofer'] ?>">
                    <?php endforeach; ?>
                </datalist>
                <!-- SCRIPT PARA QUE FUNCIONE EL DATALIST Y ENVIE LA ID CORRECTA -->
                <script>
                const input = document.getElementById('chofer_search');
                const hidden = document.getElementById('id_chofer');
                const options = document.querySelectorAll('#choferes option');

                input.addEventListener('input', () => {
                    const val = input.value.trim();
                    hidden.value = ''; // limpiar si no coincide
                    let valid = false;

                    options.forEach(opt => {
                        if (opt.value === val) {
                            hidden.value = opt.dataset.id;
                            valid = true;
                        }
                    });

                    if (!valid) {
                        input.setCustomValidity("Debe seleccionar un chofer de la lista");
                    } else {
                        input.setCustomValidity("");
                    }
                });
            </script>
            </div>
            <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#modalChofer">+</button>
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
                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#modalRecorrido">+</button>
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
                        <!-- Selector de calles -->
                        <div class="mb-3 d-flex gap-2 align-items-end">
                            <div class="flex-grow-1">
                                <input list="callesList" id="inputCalleForm" class="form-control" placeholder="Escriba para buscar una calle">
                                <datalist id="callesList">
                                <?php foreach ($datos['calles'] as $c): ?>
                                    <option value="<?= htmlspecialchars($c['nombre']) ?>" data-id="<?= $c['id_calle'] ?>">
                                <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div>
                                <button type="button" id="addCalleForm" class="btn btn-primary">+</button>
                            </div>
                        </div>
                        <!-- Script para agregar calles al recorrido con el boton + -->
                        <script>
                        document.getElementById('addCalleForm').addEventListener('click', function () {
                            const input = document.getElementById('inputCalleForm');
                            const nombre = input.value.trim();
                            
                            if (!nombre) return;

                            // Buscar el option correspondiente para obtener el id
                            const option = document.querySelector(`#callesList option[value="${nombre}"]`);
                            const id = option ? option.dataset.id : null;

                            if (!id) {
                                alert("La calle no existe en la lista");
                                return;
                            }

                            // Evitar duplicados
                            if (document.querySelector(`#tablaCalles tbody tr[data-id="${id}"]`)) {
                                alert("Esa calle ya fue agregada.");
                                return;
                            }

                            // Crear fila
                            const tbody = document.querySelector('#tablaCalles tbody');
                            const tr = document.createElement('tr');
                            tr.setAttribute('data-id', id);
                            tr.innerHTML = `
                            <td class="calle-item">${nombre}</td>
                            <td><button type="button" class="btn btn-sm btn-danger removeCalle float-end">-</button></td>
                            `;
                            tbody.appendChild(tr);

                            input.value = ""; // limpiar input
                        });

                        // Manejo del botón de eliminar
                        document.querySelector('#tablaCalles tbody').addEventListener('click', function(e) {
                        if(e.target.classList.contains('removeCalle')) {
                            e.target.closest('tr').remove();
                        }
                        });
                        </script>
                        <table class="table table-hover align-middle mb-0" id="tablaCalles">
                        <thead>
                            <tr><th>Nombre</th><th></th></tr>
                            
                        </thead>
                        <tbody>
                            <!-- Se cargan dinámicamente -->
                        </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Puntos de Detención
                        <!-- <button type="button" id="btnRefreshPuntos" class="btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                            </svg>
                        </button> -->
                        <button type="button" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalPunto">Nuevo</button></h6>
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
    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
        <button type="submit" class="btn btn-success">
            <i class="bi bi-save"></i> Guardar</button>
        <a href="<?= URL ?>" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Cancelar</a>
    </div>
</form>
<style>
    #tablaCalles td button {
        display: inline-block;
    }

    #tablaCalles td:last-child {
        width: 1%;
        white-space: nowrap;
    }
</style>

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