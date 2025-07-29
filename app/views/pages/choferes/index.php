<div class="container mt-5">
    <h2 class="mb-4">Listado de Choferes</h2>
    <div class="table-responsive">
        <table class="table table-dark table-striped table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>DNI</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos['choferes'] as $chofer): ?>
                    <tr>
                        <td><?= htmlspecialchars($chofer['nombre']) ?></td>
                        <td><?= htmlspecialchars($chofer['apellido']) ?></td>
                        <td><?= htmlspecialchars($chofer['dni']) ?></td>
                        <td>
                            <a href="<?= URL ?>/chofer/edit/<?= $chofer['id_chofer'] ?>" class="btn btn-sm btn-primary">
                                Editar
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
