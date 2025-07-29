<h1>Listado de Calles-Recorridos</h1>

<?php if (!empty($callesRecorridos)): ?>
    <ul>
        <?php foreach ($callesRecorridos as $cr): ?>
            <li>ID: <?= $cr['id_calle_recorrido'] ?> - Calle: <?= $cr['id_calle'] ?> - Recorrido: <?= $cr['id_recorrido'] ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No hay registros.</p>
<?php endif; ?>

<a href="index.php?controller=CalleRecorrido&action=create">Agregar nuevo</a>
