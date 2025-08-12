<div class="container mt-4">
    <h1 class="mb-4 text-center">👋 Bienvenido al Sistema SGPC</h1>
    
    <input type="text" id="buscarReservas" class="form-control mb-3" placeholder="🔍 Buscar reservas...">

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




    