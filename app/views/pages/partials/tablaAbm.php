
<?php if (!empty($datos['errores'])): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($datos['errores'] as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>
<div class="container-fluid mt-1 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><?= $datos['title'] ?></h2>
        <?php if (!empty($datos['urlCrear']) && in_array('cargar abm',$_SESSION['usuario_derechos'])): ?>
            <a href="<?= $datos['urlCrear'] ?>" class="btn btn-success">+ Nuevo</a>
        <?php endif; ?>
    </div>
    <div class="table-responsive-lg shadow rounded">
        <table class="table table-hover align-middle mb-0" id="tablaABM" style="min-width: 800px;">
            <thead class="table-light">
                <tr>
                    <?php foreach ($datos['columnas'] as $col): ?>
                        <th><?= $col ?></th>
                    <?php endforeach ?>
                    <?php if (!empty($datos['acciones'])): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#tablaABM').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?= $datos['urlAjax'] ?>',
        columns: [
            <?php foreach ($datos['columnas_claves'] as $col): ?>
                { data: '<?= $col ?>' },
            <?php endforeach; ?>
            <?php if (!empty($datos['acciones'])): ?>
                { data: 'acciones', orderable: false, searchable: false }
            <?php endif; ?>
        ],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', text: 'Copiar', className: 'btn btn-secondary btn-sm' },
            { extend: 'csv', text: 'CSV', className: 'btn btn-primary btn-sm', bom: true, charset: 'UTF-8' },
            { extend: 'excel', text: 'Excel', className: 'btn btn-success btn-sm' },
            { extend: 'pdf', text: 'PDF', className: 'btn btn-danger btn-sm' },
            { extend: 'print', text: 'Imprimir', className: 'btn btn-info btn-sm' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});
</script>