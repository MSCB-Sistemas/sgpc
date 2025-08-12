<div class="container mt-4">
    <h1 class="mb-4 text-center">📊 Panel de Estadisticas</h1>
<?php

// Variables para filtrar y error
$buscar_por = $_GET['buscar_por'] ?? '';
$dni = trim($_GET['dni'] ?? '');
$error = '';
$filtrar = isset($_GET['filtrar']); // Detecta si se envió el formulario con el botón "Filtrar"

// Validación: solo si se presionó "Filtrar" y buscar_por es chofer
if ($filtrar && $buscar_por === 'chofer' && $dni === '') {
    $error = "Debe ingresar un DNI para buscar por chofer.";
    // No mostrar resultados si hay error
    $datos['movimientos'] = [];
}
?>

<style>
    th a {
        color: white;
        text-decoration: none;
        cursor: pointer;
    }
    th a:hover {
        color: #ddd;
    }
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
        list-style: none;
        padding: 0;
    }
    .pagination li {
        margin: 0 0.25rem;
    }
    .pagination a {
        color: white;
        text-decoration: none;
        padding: 0.4rem 0.75rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        transition: background 0.3s ease;
    }
    .pagination a:hover {
        background: #444;
    }
    .pagination a.pagina-link {
    color: #333;
    background: white;
    text-decoration: none;
    padding: 0.4rem 0.75rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    transition: background 0.3s ease;
}

.pagination a.pagina-link:hover {
    background: #e9ecef;
}

.pagination a.pagina-activa {
    background-color: #030c14ff;
    color: white;
    border-color: #061422ff;
    font-weight: bold;
}

</style>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="tablas-tab" data-bs-toggle="tab" data-bs-target="#tablas" type="button" role="tab">Datos</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="resumen-tab" data-bs-toggle="tab" data-bs-target="#resumen" type="button" role="tab">Resumen</button>
        </li>
    </ul>

    <div class="tab-content mt-4">
        <div class="tab-pane fade show active" id="tablas" role="tabpanel">
            <!-- Formulario de filtros -->
    <form method="GET" class="row g-3 mb-4 justify-content-center">
        <div class="col-auto">
            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?= htmlspecialchars($datos['fecha_inicio'] ?? '') ?>">
        </div>

        <div class="col-auto">
            <label for="fecha_fin" class="form-label">Fecha Fin</label>
            <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" value="<?= htmlspecialchars($datos['fecha_fin'] ?? '') ?>">
        </div>

        <div class="col-auto">
            <label for="buscar_por" class="form-label">Buscar por</label>
            <select name="buscar_por" id="buscar_por" class="form-select" onchange="this.form.submit()">
                <option value="">-- Seleccionar --</option>
                <option value="chofer" <?= ($datos['buscar_por'] ?? '') === 'chofer' ? 'selected' : '' ?>>Chofer</option>
                <option value="tipo" <?= ($datos['buscar_por'] ?? '') === 'tipo' ? 'selected' : '' ?>>Tipo</option>
            </select>
        </div>

        <!-- Campo DNI solo visible si buscar_por es chofer -->
        <div class="col-auto" id="campo_dni" style="display: <?= ($datos['buscar_por'] ?? '') === 'chofer' ? 'block' : 'none' ?>;">
            <label for="dni" class="form-label">DNI del Chofer</label>
            <input type="text" class="form-control" name="dni" id="dni" value="<?= htmlspecialchars($datos['dni'] ?? '') ?>">
        </div>

        <!-- Campo Tipo visible si buscar_por es chofer o tipo -->
        <div class="col-auto" id="campo_tipo" style="display: <?= in_array($datos['buscar_por'] ?? '', ['chofer','tipo']) ? 'block' : 'none' ?>;">
            <label for="tipo" class="form-label">Tipo de Servicio</label>
            <select name="tipo" id="tipo" class="form-select">
                <option value="">-- Todos --</option>
                <option value="linea" <?= ($datos['tipo'] ?? '') === 'linea' ? 'selected' : '' ?>>Línea</option>
                <option value="charter" <?= ($datos['tipo'] ?? '') === 'charter' ? 'selected' : '' ?>>Charter</option>
                <option value="otros" <?= ($datos['tipo'] ?? '') === 'otros' ? 'selected' : '' ?>>Otros</option>
            </select>
        </div>

        <div class="col-auto align-self-end">
            <!-- Boton con name="filtrar" para detectar submit intencional -->
            <button type="submit" name="filtrar" class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    <!-- Mostrar error solo si hay -->
    <?php if ($error): ?>
        <div class="alert alert-warning text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th><?= generarOrdenLink('empresa', 'Empresa', $datos) ?></th>
                <th><?= generarOrdenLink('fecha', 'Fecha', $datos) ?></th>
                <th><?= generarOrdenLink('lugar', 'Lugar', $datos) ?></th>
                <th><?= generarOrdenLink('tipo_movimiento', 'Arribo / Salida', $datos) ?></th>
                <th><?= generarOrdenLink('cantidad', 'Cantidad de Pax', $datos) ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($datos['movimientos'])): ?>
                <?php foreach ($datos['movimientos'] as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['empresa']) ?></td>
                        <td><?= htmlspecialchars($m['fecha']) ?></td>
                        <td><?= htmlspecialchars($m['lugar']) ?></td>
                        <td><?= htmlspecialchars($m['tipo_movimiento']) ?></td>
                        <td><?= htmlspecialchars($m['cantidad']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">No se encontraron resultados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (($datos['total_paginas'] ?? 1) > 1): ?>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $datos['total_paginas']; $i++): ?>
                <li>
                    <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>"
                        class="pagina-link <?= ($i == ($datos['pagina_actual'] ?? 1)) ? 'pagina-activa' : '' ?>">
                            <?= $i ?>
                    </a>

                </li>
            <?php endfor; ?>
        </ul>
    <?php endif; ?>
</div>

<?php
// Función para generar links con ordenamiento (orden asc/desc)
function generarOrdenLink($columna, $etiqueta, $datos) {
    $direccion_actual = strtolower($datos['sort_dir'] ?? 'asc');
    $columna_actual = $datos['sort_col'] ?? '';

    // Cambia la dirección si la columna es la misma, sino por defecto asc
    $direccion_siguiente = 'asc';
    if ($columna_actual === $columna) {
        $direccion_siguiente = ($direccion_actual === 'asc') ? 'desc' : 'asc';
    }

    $query_params = $_GET;
    $query_params['sort_col'] = $columna;
    $query_params['sort_dir'] = $direccion_siguiente;

    $link = '?' . http_build_query($query_params);
    return "<a href=\"$link\">$etiqueta</a>";
}
?>

        </div>
        <div class="tab-pane fade" id="resumen" role="tabpanel">
            <!-- Fila de métricas principales -->
             <div class="dropdown mb-4">
                <label for="buscar_por" class="form-label">Filtrar por </label>
                <button class="btn btn-secondary dropdown-toggle" style="background-color:#FFFFFF; color:black;"  type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    Dia
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="#">Dia</a></li>
                    <li><a class="dropdown-item" href="#">Mes</a></li>
                    <li><a class="dropdown-item" href="#">Año</a></li>
                </ul>
            </div>

     <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card text-white" style="background: linear-gradient(135deg,#94BBE9,#EEAECA); height:185px;">
                    <div class="card-body text-center">
                        <h3>📅</h3>
                        <h5>Promedio Diario de Permisos</h5>
                        <h2><?= number_format($datos['promedio_diario'] ?? 0, 2) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card text-white" style="background: linear-gradient(135deg,#43e97b,#38f9d7); height:185px;">
                    <div class="card-body text-center">
                        <h3>🏢</h3>
                        <h5>Empresa más activa</h5>
                        <h6><?= $datos['empresa_mas_usada']['nombre'] ?? 'N/A' ?></h6>
                        <small><?= number_format($datos['empresa_mas_usada']['promedio_diario'] ?? 0, 2) ?> permisos/día</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: linear-gradient(135deg,#6a11cb,#2575fc); height:185px;">
                    <div class="card-body text-center">
                        <h3>📅</h3>
                        <h5>Reservas Actuales</h5>
                        <h2><?= count($datos['reservas_desde_hoy'] ?? []) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: linear-gradient(#e66465, #9198e5); height:185px;">
                    <div class="card-body text-center">
                        <h3>🏨</h3>
                        <h4>Hotel con mas Reservas</h4>
                        <h5><?= $datos['hoteles_usados'][0]['nombre_hotel'] ?? 'N/A' ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: linear-gradient(#FF5E00,#B81D89,#47C447); height:185px;">
                    <div class="card-body text-center">
                        <h3>🚍</h3>
                        <h4>Servicio más usado</h4>
                        <h5><?= ucfirst($datos['por_tipo']['tipo'] ?? 'N/A') ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: linear-gradient(#57C785, #8ED938); height:185px;">
                    <div class="card-body text-center">
                        <h3>🗺️</h3>
                        <h5>Recorrido más usado</h5>
                        <h6><?= $datos['recorrido_mas_usado']['nombre'] ?? 'N/A' ?></h6>
                        <small><?= $datos['recorrido_mas_usado']['cantidad'] ?? 0 ?> veces</small>
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
                    <h6><?= $datos['punto_mas_usado']['nombre'] ?? 'N/A' ?></h6>
                    <small><?= $datos['punto_mas_usado']['cantidad'] ?? 0 ?> veces</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card text-white" style="background: linear-gradient(to right,#ff758c,#D143D1); height:185px;">
                <div class="card-body text-center">
                    <h3>📊</h3>
                    <h5>Tipos de Permisos</h5>
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

    