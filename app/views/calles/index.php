<!-- SGPC/app/views/calles/index.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Lista de Calles</title>
</head>
<body>
    <h1>Calles cargadas:</h1>

    <?php if (!empty($calles)) : ?>
        <ul>
            <?php foreach ($calles as $calle): ?>
                <li><?= htmlspecialchars($calle['nombre']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No hay calles disponibles.</p>
    <?php endif; ?>
</body>
</html>
