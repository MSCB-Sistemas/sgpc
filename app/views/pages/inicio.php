<div class="container mt-4">
    <h1 class="mb-4">Panel de Inicio</h1>

    <!-- Promedio diario -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">📈 Promedio Diario de Ingresos</div>
        <div class="card-body">
            <h3><?= $datos['promedio_ingresos']['promedio_diario'] ?? '0' ?> ingresos/día</h3>
        </div>
    </div>
   

    <!-- Promedio mensual -->
    <!-- Permisos por tipo -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">🚍 Permisos por Tipo</div>
        <div class="card-body">
            <?php if (!empty($datos['por_tipo'])): ?>
                <ul class="list-group">
                    <?php foreach ($datos['por_tipo'] as $item): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <?= ucfirst($item['tipo']) ?>
                            <span class="badge bg-info"><?= $item['cantidad'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">Sin datos disponibles.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hoteles más usados -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">🏨 Hoteles Más Utilizados</div>
        <div class="card-body">
            <?php if (!empty($datos['hoteles_usados'])): ?>
                <ul class="list-group">
                    <?php foreach ($datos['hoteles_usados'] as $h): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <?= $h['nombre_hotel'] ?>
                            <span class="badge bg-success"><?= $h['cantidad'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">Sin datos disponibles.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Empresas más frecuentes -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">🏢 Empresas Más Frecuentes</div>
        <div class="card-body">
            <?php if (!empty($datos['empresas_frecuentes'])): ?>
                <ul class="list-group">
                    <?php foreach ($datos['empresas_frecuentes'] as $e): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <?= $e['nombre_empresa'] ?>
                            <span class="badge bg-warning text-dark"><?= $e['cantidad'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">Sin datos disponibles.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reservas desde hoy -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">📅 Puntos Reservados desde Hoy</div>
        <div class="card-body">
            <?php if (!empty($datos['reservas_desde_hoy'])): ?>
                <ul class="list-group">
                    <?php foreach ($datos['reservas_desde_hoy'] as $r): ?>
                        <li class="list-group-item">
                            <strong><?= date('d/m/Y H:i', strtotime($r['fecha_horario'])) ?></strong><br>
                            <span class="text-muted">📍 Punto: </span><?= $r['punto'] ?><br>
                            <span class="text-muted">🛣️ Calle: </span><?= $r['calle'] ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">No hay reservas futuras.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
