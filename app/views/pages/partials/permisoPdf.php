<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body>

    <div class="permiso-header">
        <span class="permiso-titulo">PERMISO DE CIRCULACIÓN POR LA CIUDAD DE SAN CARLOS DE BARILOCHE</span>
    </div>
<table class="noborder tabla-detalles" cellspacing="0" cellpadding="0">
        <tr class="noborder">
            <td class="noborder">
                <p class="small">
                    <span class="bold"><?= strtoupper($datos['tipo']) ?>  <?= strtoupper($datos['arribo_salida']) ?></span>
                    &nbsp;- Fecha validez: <span class="bold"><?= $datos['fecha_reserva'] ?></span><br>
                    Servicio: <span class="bold"><?= $datos['empresa'] ?></span>
                    &nbsp;- Dominio: <span class="bold"><?= $datos['dominio'] ?></span>
                    &nbsp;- Interno: <span class="bold"><?= $datos['interno'] ?></span>
                    &nbsp;- Pasajeros: <span class="bold"><?= $datos['pasajeros'] ?></span>
                    <?php if ($datos['tipo'] === 'charter'): ?>
                        <br><span class="small bold">
                            Deberá presentarse en el baño químico sin pasajeros de 08:00 a 22:00 hs
                    </span>
                    <?php endif; ?>
                </p>
            </td>
            <td class="noborder permiso-observacion-box">
                <span class="large">Permiso N°: </span><span class="bold medium"><?= $datos['id_permiso'] ?></span>
            </td>
        </tr>
    </table>
    <?php if (!empty($datos['observacion'])): ?>
        <p class="small">
            Observaciones: <span class="small bold"><?= htmlspecialchars($datos['observacion']) ?></span>
        </p>
    <?php endif; ?>

    <p class="medium" style="margin-bottom: 0pt;">
        Recorrido: <?= htmlspecialchars($datos['calles_recorrido']) ?>
        <?php if ($datos['tipo'] === 'linea'): ?>
            - horario derecho de dársena: <?php if(!empty($datos['paradas'])){echo $datos['paradas'][0]['horario'];}?>
        <?php endif; ?>
    </p>
    <?php if (!empty($datos['paradas'])): ?>
        <table style="margin-top: 0pt;">
            <thead>
                <tr>
                    <th>CALLE</th>
                    <th>PARADA</th>
                    <th>HORA</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos['paradas'] as $fila): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['calle']) ?></td>
                        <td>
                            <?= htmlspecialchars($fila['parada']) ?>
                            <?php if (!empty($fila['hotel'])): ?>
                                - <?= htmlspecialchars($fila['hotel']) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($fila['horario']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <table>
        <tr>
            <td class="text-center small bold">ABONO DERECHO DE DÁRSENA PARA <?= $datos['tipo'] === 'charter' ? 'EL INGRESO' : 'EL DESCENSO DE PAX' ?><br><br><br><br><br></td>
            <td class="text-center small bold">ABONO DERECHO DE DESCARGA DE BAÑO QUÍMICO<br><br><br></td>
            <td class="text-center small bold">ABONO DERECHO DE DÁRSENA PARA <?= $datos['tipo'] === 'charter' ? 'LA SALIDA' : 'EL ASCENSO DE PAX' ?><br><br><br><br><br></td>
        </tr>
    </table>

    <table class="noborder" cellspacing="0" cellpadding="0">
        <tr class="noborder">
            <td class="noborder signature-box">
                <br>......................<br>
                FIRMA DEL CONDUCTOR<br>
                <?= htmlspecialchars($datos['nombre_chofer']) ?> <?= htmlspecialchars($datos['apellido_chofer']) ?><br>
                DNI: <?= htmlspecialchars($datos['dni_chofer']) ?>
            </td>
            <td class="noborder"></td>
            <td class="noborder signature-box">
                <br><br>
                <?= strtoupper(htmlspecialchars($datos['usuario_nombre'])) ?> <?= strtoupper(htmlspecialchars($datos['usuario_apellido'])) ?><br>
                <?= strtoupper(htmlspecialchars($datos['usuario_cargo'])) ?>, <?= strtoupper(htmlspecialchars($datos['usuario_sector'])) ?><br>
                SAN CARLOS DE BARILOCHE
            </td>
        </tr>
    </table>

    <p class="notice">
        EL ITINERARIO AUTORIZADO HA SIDO SOLICITADO POR EL CONDUCTOR DE LA EMPRESA SIENDO ESTE RESPONSABLE SOLIDARIO DE LA MISMA A ESTOS EFECTOS. SUJETO A INFRACCIÓN EN CASO DE INCUMPLIR EL MISMO. EL ASCENSO Y DESCENSO DE PASAJEROS DEBE EFECTUARSE CON EL MOTOR APAGADO, POR ORDENANZA MUNICIPAL 808-CM-1997.<br>
        EL PRESENTE PERMISO DE CIRCULACIÓN DEBERÁ SER EXHIBIDO CADA VEZ QUE LE SEA REQUERIDO POR LA AUTORIDAD COMPETENTE (PROVINCIAL, MUNICIPAL O POLICIAL).
    </p>

    <p class="notice">
        PROHIBIDO EL USO DE GPS. LA UNIDAD NO ESTÁ AUTORIZADA A ESTACIONARSE SIN REALIZAR ACTIVIDAD Y/O PERNOCTAR EN LA VÍA PÚBLICA.<br>
        POR LEY PROVINCIAL N° 651 Y ORD. MUNICIPAL 500-CM-90 Y 506-CM-90 NO SE AUTORIZA LA CIRCULACIÓN DE UNIDADES DE MÁS DE 10 MTS. DE LARGO PARA LA REALIZACIÓN DE EXCURSIONES A LOS CIRCUITOS TURÍSTICOS: CIRCUITO CHICO, CERRO CATEDRAL, CERRO OTTO, PUERTO PAÑUELO Y TODAS LAS ALTERNATIVAS RELACIONADAS.
    </p>

    <hr>

</body>
</html>
