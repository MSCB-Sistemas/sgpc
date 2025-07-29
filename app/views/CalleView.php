<!DOCTYPE html>
<html>
<head>
    <title>Test Calles</title>
</head>
<body>
    <h1>Agregar Calle</h1>
    <form action="index.php?action=store" method="post">
        <input type="text" name="nombre" placeholder="Nombre de la calle" required>
        <button type="submit">Agregar</button>
    </form>

    <h1>Buscar Calle por ID</h1>
    <form action="index.php" method="show">
        <input type="hidden" name="action" value="show">
        <input type="number" name="id_calle" placeholder="ID de la calle" required>
        <button type="submit">Buscar</button>
    </form>

    <h1>Listado de Calles</h1>
    <a href="index.php?action=index">Ver todas las calles</a>

    <div>
        <?php
        // Mostrar resultado si hay variable $resultado
        if (isset($resultado)) {
            echo '<pre>' . print_r($resultado, true) . '</pre>';
        }
        ?>
    </div>
</body>
</html>
