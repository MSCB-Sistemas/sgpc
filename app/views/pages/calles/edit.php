<h1>Editar Calle</h1>

<?php if (!empty($calle)) : ?>
    <form method="POST" action="/SGPC/public/index.php?action=update&id_calle=<?= $calle['id_calle'] ?>">
        <label for="nombre">Nombre de la calle:</label>
        <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($calle['nombre']) ?>" required>

        <br><br>
        <button type="submit">Actualizar</button>
    </form>
<?php else : ?>
    <p>Calle no encontrada.</p>
<?php endif; ?>

<p><a href="/SGPC/public/index.php?action=index">Volver a la lista</a></p>
