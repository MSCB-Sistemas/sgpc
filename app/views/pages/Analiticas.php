<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>📊 Dashboard de Analíticas SGPC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            color: #000000;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
        }

        .card {
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .card h2 {
            margin: 0;
            font-size: 1.4em;
        }

        .value {
            font-size: 2em;
            color: #000000;
            margin-top: 10px;
        }

        ul {
            padding-left: 20px;
        }

        li {
            margin-bottom: 5px;
        }

        small {
            font-size: 0.8em;
            color: #333;
        }
    </style>
</head>
<body>

    <h1>📊 Panel de Analíticas - SGPC</h1>

    <div class="card">
        <h2>📆 Promedio de permisos diarios</h2>
        <div class="value"><?= number_format($datos['promedio_diario'], 2) ?> permisos/día</div>
    </div>

    <div class="card">
        <h2>🏢 Empresa con más permisos diarios</h2>
        <div class="value">
            <?= $datos['empresa_mas_usada']['nombre'] ?? 'No disponible' ?> <br>
            <small><?= number_format($datos['empresa_mas_usada']['promedio_diario'] ?? 0, 2) ?> permisos/día</small>
        </div>
    </div>

    <div class="card">
        <h2>🧾 Promedio de permisos por tipo</h2>
        <?php if (!empty($datos['promedio_por_tipo'])): ?>
            <ul>
                <?php foreach ($datos['promedio_por_tipo'] as $tipo): ?>
                    <li><strong><?= ucfirst($tipo['tipo']) ?>:</strong> <?= number_format($tipo['promedio_diario'], 2) ?> permisos/día</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No hay datos disponibles.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>🗺️ Recorrido más utilizado</h2>
        <div class="value">
            <?= $datos['recorrido_mas_usado']['nombre'] ?? 'No disponible' ?> <br>
            <small><?= $datos['recorrido_mas_usado']['cantidad'] ?? 0 ?> permisos</small>
        </div>
    </div>

    <div class="card">
        <h2>📌 Punto de detención más utilizado</h2>
        <div class="value">
            <?= $datos['punto_mas_usado']['nombre'] ?? 'No disponible' ?> <br>
            <small><?= $datos['punto_mas_usado']['cantidad'] ?? 0 ?> veces</small>
        </div>
    </div>

</body>
</html>
