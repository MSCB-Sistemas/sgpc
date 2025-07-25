<!-- app/views/calles/show.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Calle</title>
</head>
<body>
    <h1>Detalles de la Calle</h1>

    <?php if (!empty($calle)) : ?>
        <p><strong>ID:</strong> <?= htmlspecialchars($calle['id_calle']) ?></p>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($calle['nombre']) ?></p>

        <a href="/SGPC/public/index.php?action=edit&id_calle=<?= $calle['id_calle'] ?>">Editar</a> |
        <a href="/SGPC/public/index.php?action=delete&id_calle=<?= $calle['id_calle'] ?>" onclick="return confirm('¿Seguro que deseas eliminar esta calle?')">Eliminar</a> |
        <a href="/SGPC/public/index.php?action=index">Volver a la lista</a>
    <?php else : ?>
        <p>Calle no encontrada.</p>
    <?php endif; ?>
</body>
</html>
