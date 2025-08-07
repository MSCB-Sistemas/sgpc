<div class="container mt-4">
    <h1 class="mb-4 text-center">📊 Municipalidad de San Carlos de Bariloche</h1>
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
                    <h5>Reservas Actuales</h5>
                    <h2><?= count($datos['reservas_desde_hoy'] ?? []) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white" style="background: linear-gradient(135deg,#43e97b,#38f9d7); height:185px;">
                <div class="card-body text-center">
                    <h3>🏢 </h3>
                    <h4>Empresa con más reservas</h4>
                    <h5><?= $datos['empresas_frecuentes'][0]['nombre_empresa'] ?? 'N/A' ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white" style="background: linear-gradient(135deg,#f7971e,#ffd200); height:185px;">
                <div class="card-body text-center">
                    <h3>🚍</h3>
                    <h4>Servicio más usado</h4>
                    <h5><?= ucfirst($datos['por_tipo']['tipo'] ?? 'N/A') ?></h5>
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
    </div>

    <!-- Reservas desde hoy detalladas -->
    <div class="row mb-4">
        <div class="col-md-12 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">📅 Reservas Programadas</div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    <?php if (!empty($datos['reservas_desde_hoy'])): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($datos['reservas_desde_hoy'] as $r): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= date('d/m/Y H:i', strtotime($r['fecha_horario'])) ?></strong><br>
                                        <small class="text-muted">📍 <?= $r['punto'] ?> | 🛣️ <?= $r['calle'] ?></small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">No hay reservas futuras.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
</div>
