<div class="container mt-4">
    <h1 class="mb-4 text-center">📈 Panel de Analíticas</h1>

    <form method="GET" class="row g-3 mb-4 justify-content-center">
        <div class="col-auto">
            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?= $_GET['fecha_inicio'] ?? '' ?>">
        </div>
        <div class="col-auto">
            <label for="fecha_fin" class="form-label">Fecha Fin</label>
            <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" value="<?= $_GET['fecha_fin'] ?? '' ?>">
        </div>
        <div class="col-auto align-self-end">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>

        
    </form>        
</div>


<!-- Fila de métricas principales -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white" style="background: linear-gradient(135deg,#6a11cb,#2575fc); height:185px;">
            <div class="card-body text-center">
                <h3>📅</h3>
                <h5>Promedio Diario</h5>
                <h2><?= number_format($datos['promedio_diario'] ?? 0, 2) ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
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
        <div class="card text-white" style="background: linear-gradient(135deg,#f7971e,#ffd200); height:185px;">
            <div class="card-body text-center">
                <h3>🧾</h3>
                <h5>Tipo más común</h5>
                <h6><?= ucfirst($datos['promedio_por_tipo'][0]['tipo'] ?? 'N/A') ?></h6>
                <small><?= number_format($datos['promedio_por_tipo'][0]['promedio_diario'] ?? 0, 2) ?> por día</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-white" style="background: linear-gradient(#e66465, #9198e5); height:185px;">
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
                <h3>📌</h3>
                <h5>Punto más usado</h5>
                <h6><?= $datos['punto_mas_usado']['nombre'] ?? 'N/A' ?></h6>
                <small><?= $datos['punto_mas_usado']['cantidad'] ?? 0 ?> veces</small>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card text-white" style="background: linear-gradient(to right,#ff758c,#ff7eb3); height:185px;">
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
    <div class="container mt-4">
    <h2 class="mb-4 text-center">📋 Movimientos por Empresa</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Empresa</th>
                <th>Fecha</th>
                <th>Lugar</th>
                <th>Arribo / Salida</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($movimientos)): ?>
                <?php foreach ($movimientos as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['empresa']) ?></td>
                        <td><?= htmlspecialchars($m['fecha']) ?></td>
                        <td><?= htmlspecialchars($m['lugar']) ?></td>
                        <td><?= htmlspecialchars($m['tipo_movimiento']) ?></td>
                        <td><?= $m['cantidad Pax'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">No se encontraron resultados para el período seleccionado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div>
