<div class="container mt-4">
    <?php if (!empty($datos['errores'])): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($datos['errores'] as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>
    <h1 class="mb-4 text-center"style="font-family: 'Lexend';">Bienvenido al Sistema SGPC</h1>
    
    <input type="text" id="buscarReservas" class="form-control mb-3" placeholder="🔍 Buscar reservas...">

    <div class="row mb-4">
        <div class="col-md-12 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">📅 Reservas Programadas</div>
                <div class="card-body" style="max-height: 450px; overflow-y: auto;">
                    <?php if (!empty($datos['reservas_desde_hoy'])): ?>
                        <ul id="listaReservas" class="list-group list-group-flush">
                            <?php if (!empty($datos['reservas_desde_hoy'])): ?>
                                <?php foreach ($datos['reservas_desde_hoy'] as $r): ?>
                                    <li class="list-group-item" style="height:90px; ">
                                        <span style="font-size:1rem; font-weight:500; " style=> 🕜 <?= date('d/m/Y H:i', strtotime($r['fecha_horario'])) ?></span>
                                        <br><br>
                                        <span style=" text-transform: capitalize; font-size:1rem; font-weight:500; letter-spacing: 0px; word-spacing: 10px;" >
                                        🏙 <?= $r['empresa']?> | 📍 <?= $r['punto'] ?> |  🛣️ <?= $r['calle'] ?> |  🔹 <?= $r['tipo']?> 
                                        </span> <span style="text-transform: uppercase; font-size:1rem; font-weight:500; letter-spacing: 0px; word-spacing: 10px;"> | 🚌 <?= $r['dominio']?></span>
                                        
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-muted">No hay reservas futuras.</li>
                            <?php endif; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">No hay reservas futuras.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <body>  
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const buscador = document.getElementById('buscarReservas');
                const items = document.querySelectorAll('#listaReservas li');

                buscador.addEventListener('keyup', function () {
                    const filtro = this.value.toLowerCase();
                    let visibles = 0;

                    items.forEach(function (item) {
                        const texto = item.innerText.toLowerCase();
                        const coincide = texto.includes(filtro);
                        item.style.display = coincide ? '' : 'none';
                        if (coincide) visibles++;
                    });

                    // Si no hay coincidencias, mostramos un mensaje
                    if (visibles === 0) {
                        if (!document.getElementById('sinResultados')) {
                            const li = document.createElement('li');
                            li.id = 'sinResultados';
                            li.className = 'list-group-item text-muted';
                            li.textContent = 'No se encontraron resultados.';
                            document.getElementById('listaReservas').appendChild(li);
                        }
                    } else {
                        const sinResultados = document.getElementById('sinResultados');
                        if (sinResultados) sinResultados.remove();
                    }
                });
            });
        </script>
    </body>
</div>




    