
<style>
    #tablaABM thead th {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #6b7280; /* subtle gray */
}

#tablaABM tbody td {
    font-size: 0.85rem;
}
</style>
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
    </div>
    <div class="row mb-3">
    <div class="col-md-2">
        <label for="fecha_desde" class="form-label">Fecha de emision desde</label>
        <input type="date" id="fecha_desde" class="form-control" value="<?php if(!empty($datos['fecha_desde'])){echo $datos['fecha_desde'];} else {echo date('Y-m-d', strtotime("-1 week"));}?>">
    </div>
    <div class="col-md-2">
        <label for="fecha_hasta" class="form-label">Fecha de emision hasta</label>
        <input type="date" id="fecha_hasta" class="form-control" value="<?php if(!empty($datos['fecha_hasta'])){echo $datos['fecha_hasta'];} else {echo date('Y-m-d');}?>">
    </div>
    <div class="col-md-2">
        <label for="empresa" class="form-label">Empresa</label>
        <select id="empresa" class="form-select">
            <option value="">Todas</option>
            <?php foreach ($datos['empresas'] as $emp): ?>
                <option value="<?= htmlspecialchars($emp['id_empresa'])?>"<?php if (!empty($datos['empresa']) && $datos['empresa'] === $emp['id_empresa']): ?>selected<?php endif; ?>><?= htmlspecialchars($emp['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-1 d-flex align-items-end">
        <button id="btnFiltrar" class="btn btn-primary w-100">Filtrar</button>
    </div>
    </div>

    <div class="table-responsive shadow rounded">
        <table class="table table-sm table-hover align-middle mb-0" id="tablaABM" style="min-width: 800px;">
            <thead class="table-light">
                <tr class="sticky">
                    <?php 
                    $afterAcciones = false;
                    foreach ($datos['columnas'] as $col): ?>
                        <th class="<?php if ($afterAcciones){echo "none";} else {echo "all";} ?>"><?= $col ?></th>
                        
                        <?php if ($col == 'Empresa' && !empty($datos['acciones'])): ?>
                            <th>Acciones</th>
                        <?php $afterAcciones = true; ?>
                        <?php endif; ?>
                    <?php endforeach ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                $afterAcciones = false;
                foreach ($datos['data'] as $fila): ?>
                    <tr>
                        <?php foreach ($datos['columnas_claves'] as $key): ?>
                            <td class="<?php if ($afterAcciones){echo "none";} else {echo "all";} ?> text-truncate" style="max-width: 200px;"><?= ucfirst(htmlspecialchars($fila[$key])) ?></td>
                            
                            <?php if ($key == 'Empresa' && !empty($datos['acciones'])): ?>
                                <td><?= $datos['acciones']($fila) ?></td>
                                <?php $afterAcciones = true; ?>
                            <?php endif; ?>
                        <?php endforeach ?>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#tablaABM').DataTable({
        responsive: {
            details: {
                type: 'column',
                target: 0
            }
        },
        columnDefs: [
            { className: 'dtr-control', orderable: false, targets: 0 }
        ],
        dom: 'Bfrtip', // B: botones, f: filtro, r: información, t: tabla, i: info, p: paginación
        buttons: [
            { extend: 'copy', text: 'Copiar', className: 'btn btn-secondary btn-sm', exportOptions: { columns: [0,1,2,3,4,5,6,8,9,10] } },
            { extend: 'csv', text: 'CSV', className: 'btn btn-primary btn-sm', bom: true, charset: 'UTF-8', exportOptions: { columns: [0,1,2,3,4,5,6,8,9,10] } },
            { extend: 'excel', text: 'Excel', className: 'btn btn-success btn-sm', exportOptions: { columns: [0,1,2,3,4,5,6,8,9,10] } },
            { extend: 'pdf', text: 'PDF', className: 'btn btn-danger btn-sm', exportOptions: { columns: [0,1,2,3,4,5,6,8,9,10] } },
            { extend: 'print', text: 'Imprimir', className: 'btn btn-info btn-sm', exportOptions: { columns: [0,1,2,3,4,5,6,8,9,10] } }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 10,       // Número de filas por página
        lengthMenu: [5, 10, 25, 50, 100], // Opciones para cambiar cantidad
        order: []             // Sin orden inicial (para que el usuario elija)
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btnFiltrar');
    const fechaDesde = document.getElementById('fecha_desde');
    const fechaHasta = document.getElementById('fecha_hasta');
    const empresa = document.getElementById('empresa');
    function filtrar() {
        const desde = fechaDesde.value;
        const hasta = fechaHasta.value;
        const empresaValue = empresa.value;

        let url = "<?= URL ?>/permiso/index";

        if (desde) {
            url += "/" + desde;
        } else {
            url += "/0"; // "0" cuando no hay fecha desde
        }
        
        if (hasta) {
            url += "/" + hasta;
        } else {
            url += "/0"; // "0" cuando no hay fecha hasta
        }

        if (empresaValue) {
            url += "/" + encodeURIComponent(empresaValue);
        }

        window.location.href = url;
    }

    // Click en el botón
    btn.addEventListener('click', filtrar);

    // Enter en los inputs
    [fechaDesde, fechaHasta, empresa].forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // evita enviar un form si hubiera
                filtrar();
            }
        });
    });
});
</script>
<?php require_once APP . '/views/pages/partials/modalPermisoIndex.php' ?>
