<?php
// Variables para filtrar y error

// Buscar por
if (isset($_GET['buscar_por'])) {
    $buscar_por = $_GET['buscar_por'];
} else {
    $buscar_por = '';
}

// DNI
if (isset($_GET['dni'])) {
    $dni = trim($_GET['dni']);
} else {
    $dni = '';
}

// Fecha inicio
if (isset($_GET['fecha_inicio'])) {
    $fecha_inicio = $_GET['fecha_inicio'];
} else {
    $fecha_inicio = '';
}

// Fecha fin
if (isset($_GET['fecha_fin'])) {
    $fecha_fin = $_GET['fecha_fin'];
} else {
    $fecha_fin = '';
}

$error = '';

// Filtrar
if (isset($_GET['filtrar'])) {
    $filtrar = true;
} else {
    $filtrar = false;
}


// Validación: solo si se presionó "Filtrar" y buscar_por es chofer
if ($filtrar && $buscar_por === 'chofer' && $dni === '') {
    $error = "Debe ingresar un DNI para buscar por chofer.";
    // No mostrar resultados si hay error
    $datos['movimientos'] = [];
}

// Para los valores que usas en la vista desde $datos, hacer asignaciones con if para evitar usar ?? o ternarios:

if (isset($datos['fecha_inicio']) && is_string($datos['fecha_inicio']) && !empty($datos['fecha_inicio'])) {
    $valor_fecha_inicio = htmlspecialchars($datos['fecha_inicio']);
} else {
    $valor_fecha_inicio = '';
}

if (isset($datos['fecha_fin']) && !empty($datos['fecha_fin'])) {
    $valor_fecha_fin = htmlspecialchars($datos['fecha_fin']);
} else {
    $valor_fecha_fin = '';
}

if (isset($datos['buscar_por'])) {
    $valor_buscar_por = $datos['buscar_por'];
} else {
    $valor_buscar_por = '';
}

if (isset($datos['dni']) && !empty($datos['dni'])) {
    $valor_dni = htmlspecialchars($datos['dni']);
} else {
    $valor_dni = '';
}

if (isset($datos['tipo']) && !empty($datos['tipo'])) {
    $valor_tipo = $datos['tipo'];
} else {
    $valor_tipo = '';
}

if (isset($datos['movimientos']) && !empty($datos['movimientos'])) {
    $movimientos = $datos['movimientos'];
} else {
    $movimientos = [];
}

if (isset($datos['total_paginas']) && $datos['total_paginas'] > 1) {
    $total_paginas = $datos['total_paginas'];
} else {
    $total_paginas = 0;
}

if (isset($datos['pagina_actual'])) {
    $pagina_actual = $datos['pagina_actual'];
} else {
    $pagina_actual = 1;
}

if (isset($datos['promedio_diario'])) {
    $promedio_diario = $datos['promedio_diario'];
} else {
    $promedio_diario = 0;
}

if (isset($datos['empresa_mas_usada']['nombre'])) {
    $empresa_mas_usada_nombre = $datos['empresa_mas_usada']['nombre'];
} else {
    $empresa_mas_usada_nombre = 'N/A';
}

if (isset($datos['empresa_mas_usada']['total'])) {
    $empresa_mas_usada_total = $datos['empresa_mas_usada']['total'];
} else {
    $empresa_mas_usada_total = 0;
}

if (isset($datos['empresa_mas_usada']['promedio_diario'])) {
    $empresa_mas_usada_promedio_diario = $datos['empresa_mas_usada']['promedio_diario'];
} else {
    $empresa_mas_usada_promedio_diario = 0;
}

if (isset($datos['promedio_reservas'])) {
    $promedio_reservas_count = count($datos['promedio_reservas']);
} else {
    $promedio_reservas_count = 0;
}

if (isset($datos['hoteles_usados'][0]['nombre_hotel'])) {
    $hotel_nombre = $datos['hoteles_usados'][0]['nombre_hotel'];
} else {
    $hotel_nombre = 'N/A';
}

if (isset($datos['hoteles_usados'][0]['total'])) {
    $hotel_total = $datos['hoteles_usados'][0]['total'];
} else {
    $hotel_total = 'N/A';
}

if (isset($datos['hoteles_usados'][0]['promedio'])) {
    $hotel_promedio = $datos['hoteles_usados'][0]['promedio'];
} else {
    $hotel_promedio = 'N/A';
}

if (isset($datos['por_tipo'][0]['tipo'])) {
    $por_tipo_tipo = ucfirst($datos['por_tipo'][0]['tipo']);
} else {
    $por_tipo_tipo = 'N/A';
}


if (isset($datos['recorrido_mas_usado']['nombre'])) {
    $recorrido_nombre = $datos['recorrido_mas_usado']['nombre'];
} else {
    $recorrido_nombre = 'N/A';
}

if (isset($datos['recorrido_mas_usado']['cantidad'])) {
    $recorrido_cantidad = $datos['recorrido_mas_usado']['cantidad'];
} else {
    $recorrido_cantidad = 0;
}

if (isset($datos['punto_mas_usado']['nombre'])) {
    $punto_nombre = $datos['punto_mas_usado']['nombre'];
} else {
    $punto_nombre = 'N/A';
}

if (isset($datos['punto_mas_usado']['cantidad'])) {
    $punto_cantidad = $datos['punto_mas_usado']['cantidad'];
} else {
    $punto_cantidad = 0;
}

if (isset($datos['promedio_por_tipo']) && !empty($datos['promedio_por_tipo'])) {
    $promedio_por_tipo = $datos['promedio_por_tipo'];
} else {
    $promedio_por_tipo = [];
}

$tabActivo = 'tablas'; // valor por defecto

if (isset($_GET['tab'])) {
    if ($_GET['tab'] === 'resumen') {
        $tabActivo = 'resumen';
    } else if ($_GET['tab'] === 'tablas') {
        $tabActivo = 'tablas';
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Asegurar que mantenemos la pestaña activa desde el action
    if (isset($_GET['tab'])) {
        $tabActivo = $_GET['tab'];
    }

    if ($tabActivo === 'tablas') {
        if (isset($_POST['fecha_inicio'])) $fecha_inicio = $_POST['fecha_inicio'];
        if (isset($_POST['fecha_fin'])) $fecha_fin = $_POST['fecha_fin'];
        if (isset($_POST['buscar_por'])) $buscar_por = $_POST['buscar_por'];
        if (isset($_POST['dni'])) $dni = $_POST['dni'];
        if (isset($_POST['tipo'])) $tipo = $_POST['tipo'];

        // Asegurar que esos valores se pasen a las variables para usar en el formulario
        $valor_fecha_inicio = $fecha_inicio;
        $valor_fecha_fin = $fecha_fin;
        $valor_buscar_por = $buscar_por;
        $valor_dni = $dni;
        $valor_tipo = $tipo;
    }

    if ($tabActivo === 'resumen') {
        if (isset($_POST['fecha_inicio_resumen'])) $valor_fecha_inicio_resumen = $_POST['fecha_inicio_resumen'];
        if (isset($_POST['fecha_fin_resumen'])) $valor_fecha_fin_resumen = $_POST['fecha_fin_resumen'];
    }
}

?>

<link rel="stylesheet" href="<?= URL . '/public/css/estadisticas.css' ?>">

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link <?php if ($tabActivo === 'tablas') { echo 'active'; } ?>" 
            id="tablas-tab" 
            data-bs-toggle="tab" 
            data-bs-target="#tablas" 
            role="tab">
        Datos
        </a>

    </li>
    <li class="nav-item">
        <a class="nav-link <?php if ($tabActivo === 'resumen') { echo 'show active'; } ?>"
         id="resumen-tab" 
                data-bs-toggle="tab" 
                data-bs-target="#resumen" 
                type="button" 
                role="tab">
        Resumen</a>
</ul>

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


        <!-- Tabla -->
       <!-- Cargar bootstrap-table -->
        <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css">
        <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.js"></script>

        <table 
            class="table table-bordered table-striped"
            data-toggle="table"
            data-search="true"
            data-pagination="false">
            
            <thead class="table-dark">
                <tr>
                    <th data-sortable="true">
                        <?php
                        if ($valor_buscar_por === 'chofer') {
                            echo 'Chofer';
                        } else {
                            echo 'Empresa';
                        }
                        ?>
                    </th>
                    <th data-field="fecha" data-sortable="true">Fecha</th>
                    <th data-field="lugar" data-sortable="true">Lugar</th>
                    <th data-field="movimiento" data-sortable="true">Tipo de Movimiento</th>
                    <th data-field="pax" data-sortable="true">Cantidad de Pax</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($movimientos)): ?>
                    <?php foreach ($movimientos as $m): ?>
                        <tr>
                            <td>
                                <?php 
                                if ($valor_buscar_por === 'chofer') {
                                    echo htmlspecialchars($m['chofer_completo']);
                                } else {
                                    echo htmlspecialchars($m['empresa']);
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($m['fecha_emision']) ?></td>
                            <td><?= htmlspecialchars($m['lugar']) ?></td>
                            <td><?= htmlspecialchars($m['arribo_salida']) ?></td>
                            <td><?= htmlspecialchars($m['pasajeros']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No se encontraron resultados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Paginado -->
       <?php if ($total_paginas > 1): ?>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li>
                        <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>"
                           class="pagina-link <?php if ($pagina_actual == $i) { echo 'pagina-activa'; } ?>">
                           <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>
    </div>


    
    <!-- TAB RESUMEN -->
                    
    <div class="tab-pane fade <?php if ($tabActivo === 'resumen') { echo 'show active'; } ?>"
     id="resumen"
     role="tabpanel"
     aria-labelledby="tablas-tab">
        <?php
        if (isset($_POST['fecha_inicio_resumen'])) {
            $valor_fecha_inicio_resumen = $_POST['fecha_inicio_resumen'];
        } else {
            $valor_fecha_inicio_resumen = '';
        }

        if (isset($_POST['fecha_fin_resumen'])) {
            $valor_fecha_fin_resumen = $_POST['fecha_fin_resumen'];
        } else {
            $valor_fecha_fin_resumen = '';
        }

        ?>
        <form method="GET" action="?tab=resumen" id="form-filtro-resumen" class="row g-3 mb-4 justify-content-center">
            <input type="hidden" name="tab" value="resumen">
            <div class="col-auto">
                <label for="fecha_inicio_resumen" class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" name="fecha_inicio_resumen" id="fecha_inicio_resumen" value="<?= htmlspecialchars($valor_fecha_inicio_resumen) ?>">
            </div>

            <div class="col-auto">
                <label for="fecha_fin_resumen" class="form-label">Fecha Fin</label>
               <input type="date" class="form-control" name="fecha_fin_resumen" id="fecha_fin_resumen" value="<?= htmlspecialchars($valor_fecha_fin_resumen) ?>">
            </div>

            <div class="col-auto align-self-end">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>
    
        <!-- Cards métricas -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card text-white" style="background: linear-gradient(135deg,#94BBE9,#EEAECA); height:185px;">
                    <div class="card-body text-center">
                        <h3>📅</h3>
                        <h5>Promedio de Permisos entre fechas</h5>
                        <h2><?= number_format($promedio_diario, 2) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card text-white" style="background: linear-gradient(135deg,#43e97b,#38f9d7,#81FF47); height:185px;">
                    <div class="card-body text-center">
                        <h3>🏢</h3>
                        <h4>Empresa más activa</h4>
                        <h5><?= htmlspecialchars($empresa_mas_usada_nombre) ?></h5>
                        <h6>Total de Permisos: <?= htmlspecialchars($empresa_mas_usada_total) ?></h6>
                        <small><?= number_format($empresa_mas_usada_promedio_diario, 2) ?> permisos/día</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: linear-gradient(135deg,#6a11cb,#2575fc); height:185px;">
                    <div class="card-body text-center">
                        <h3>🌎</h3>
                        <h5>Destinos más Frecuentes</h5>
                        <h5><?php if (!empty($datos['arribo_mas_usado'][0]['nombre'])){echo $datos['arribo_mas_usado'][0]['nombre'];} else {echo 'N/A';} ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: linear-gradient(#e66465, #9198e5); height:185px;">
                    <div class="card-body text-center">
                        <h3>🏨</h3>
                        <h4>Hotel con mas Reservas</h4>
                        <h5><?php if (!empty($datos['hoteles_usados'][0]['nombre_hotel'])){echo $datos['hoteles_usados'][0]['nombre_hotel'];} else {echo 'N/A';} ?></h5>
                        <h5><?php if (!empty($datos['hoteles_usados'][0]['total'])){echo $datos['hoteles_usados'][0]['total'];} else {echo 'N/A';} ?></h5>
                        <h5><?php if (!empty($datos['hoteles_usados'][0]['promedio'])){echo $datos['hoteles_usados'][0]['promedio'];} else {echo 'N/A';} ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: linear-gradient(#FF5E00,#B81D89,#47C447); height:185px;">
                    <div class="card-body text-center">
                        <h3>🚍</h3>
                        <h4>Servicio más usado</h4>
                        <h5><?php if (!empty($datos['por_tipo'][0]['tipo'])){echo ucfirst($datos['por_tipo'][0]['tipo']);} else {echo 'N/A';} ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: linear-gradient(#57C785, #8ED938); height:185px;">
                    <div class="card-body text-center">
                        <h3>🗺️</h3>
                        <h5>Recorrido más usado</h5>
                        <h6><?php if (!empty($datos['recorrido_mas_usado']['nombre'])){echo $datos['recorrido_mas_usado']['nombre'];} else {echo 'N/A';} ?></h6>
                        <small><?php if (!empty($datos['recorrido_mas_usado']['cantidad'])){echo $datos['recorrido_mas_usado']['cantidad'];} else {echo 0;} ?> veces</small>
                    </div>
                </div>
            </div>
        </div>

<!-- Segunda fila de métricas -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card text-white" style="background: linear-gradient(to right,#8360c3,#2ebf91); height:185px;">
                <div class="card-body text-center">
                    <h3>📍</h3>
                    <h5>Parada más usada</h5>
                    <h6><?php if (!empty($datos['punto_mas_usado'][0]['nombre'])){echo $datos['punto_mas_usado'][0]['nombre'];} else {echo 'N/A';} ?></h6>
                    <small><?php if (!empty($datos['punto_mas_usado'][0]['cantidad'])){echo $datos['punto_mas_usado'][0]['cantidad'];} else {echo 0;} ?> veces</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card text-white" style="background: linear-gradient(to right,#ff758c,#D143D1); height:185px;">
                <div class="card-body text-center">
                    <h3>📊</h3>
                    <h5>Promedio de Tipos de Permisos</h5>
                    <?php if (!empty($datos['promedio_por_tipo'])): ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($datos['promedio_por_tipo'] as $tipo): ?>
                                <li><?= ucfirst($tipo['tipo']) ?>: <?= number_format($tipo['promedio_diario'], 2) ?> / día</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay datos.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

